<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    private function addresses(Request $r)
    {
        return CustomerAddress::where('user_id', $r->user()->id);
    }

    private function notices(Request $r)
    {
        return DB::table('order_status_history as h')->join('orders as o', 'o.id', '=', 'h.order_id')
            ->leftJoin('customer_notice_states as s', function ($join) use ($r) {
                $join->on('s.history_id', '=', 'h.id')->where('s.user_id', $r->user()->id);
            })
            ->where('o.user_id', $r->user()->id)->whereNull('s.hidden_at');
    }

    public function index(Request $r)
    {
        return view('customer.dashboard', [
            'orderCount' => $r->user()->orders()->count(),
            'shippingCount' => $r->user()->orders()->where('status', 'shipping')->count(),
            'favoriteCount' => DB::table('customer_favorites')->where('user_id', $r->user()->id)->count(),
            'unreadCount' => $this->notices($r)->whereNull('s.read_at')->count(),
            'recentOrders' => $r->user()->orders()->with('items.variant.product')->latest()->limit(2)->get(),
        ]);
    }

    public function buyAgain(Request $r, Order $order){
        abort_unless((int)$order->user_id===(int)$r->user()->id,404);$added=0;$skipped=0;
        foreach($order->items()->with('variant.product')->get() as $item){
            $v=$item->variant;if(!$v || !$v->is_active || !$v->product?->is_active){$skipped++;continue;}
            $current=\App\Support\Cart::quantities()[$v->id]??0;
            $qty=min($item->quantity,max(0,$v->stock_qty-$current),max(0,\App\Support\Cart::MAX_QTY-$current));
            if($qty>0){\App\Support\Cart::set($v->id,$current+$qty);$added+=$qty;}if($qty<$item->quantity)$skipped++;
        }
        if(!$added)return back()->withErrors(['cart'=>'Các sản phẩm hiện không còn đủ tồn kho để thêm.']);
        return redirect()->route('checkout.index')->with('success','Đã thêm '.$added.' sản phẩm theo giá hiện tại.'.($skipped?' Một số sản phẩm hết hàng hoặc không đủ số lượng; hãy kiểm tra lại giỏ.':''));
    }
    public function receiveOrder(Request $r, Order $order){
        abort_unless((int)$order->user_id===(int)$r->user()->id,404);
        DB::transaction(function()use($r,$order){
            $o=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if(!in_array($o->status,['shipping','completed'],true)||$o->received_at)throw \Illuminate\Validation\ValidationException::withMessages(['order'=>'Đơn chưa được giao hoặc bạn đã xác nhận nhận hàng.']);
            $from=$o->status;$o->update(['status'=>'completed','received_at'=>now()]);
            $o->statusHistory()->create(['from_status'=>$from,'to_status'=>'completed','changed_by'=>$r->user()->id,'note'=>'Khách hàng xác nhận đã nhận đủ sản phẩm.']);
        },3);
        return redirect()->route('customer.orders.show',$order)->with('success','Đã xác nhận nhận hàng. Hãy chia sẻ đánh giá về sản phẩm nhé!');
    }
    public function review(Request $r, Order $order, int $item){
        abort_unless((int)$order->user_id===(int)$r->user()->id,404);
        $data=$r->validate(['rating'=>'required|integer|between:1,5','content'=>'required|string|min:10|max:2000']);
        DB::transaction(function()use($r,$order,$item,$data){
            $o=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if($o->status!=='completed'||!$o->received_at)throw \Illuminate\Validation\ValidationException::withMessages(['review'=>'Hãy xác nhận đã nhận hàng trước khi đánh giá.']);
            $line=$o->items()->findOrFail($item);
            if($line->review()->exists())throw \Illuminate\Validation\ValidationException::withMessages(['review'=>'Bạn đã đánh giá sản phẩm trong đơn này.']);
            $product=$line->variant?->product;
            if(!$product)throw \Illuminate\Validation\ValidationException::withMessages(['review'=>'Sản phẩm không còn trong danh mục. Hãy liên hệ hỗ trợ.']);
            $product=Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $line->review()->create($data+['user_id'=>$r->user()->id,'product_id'=>$product->id]);
            $count=(int)$product->rating_count;
            $product->update(['rating_avg'=>round(($product->rating_avg*$count+$data['rating'])/($count+1),1),'rating_count'=>$count+1]);
        },3);
        return back()->with('success','Cảm ơn bạn! Đánh giá đã được lưu và hiển thị tại trang sản phẩm.');
    }
    public function defaultAddress(Request $r,int $address){
        DB::transaction(function()use($r,$address){
            User::whereKey($r->user()->id)->lockForUpdate()->firstOrFail();$a=$this->addresses($r)->findOrFail($address);
            $this->addresses($r)->update(['is_default'=>false]);$a->update(['is_default'=>true]);
        });
        return back()->with('success','Đã đặt địa chỉ mặc định cho lần thanh toán tiếp theo.');
    }
    public function profile()
    {
        return view('customer.profile');
    }

    public function updateProfile(Request $r)
    {
        $u = $r->user();
        $data = $r->validate([
            'full_name' => 'required|string|max:100',
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users')->ignore($u->id)],
            'phone' => ['nullable', 'regex:/^0[0-9]{9}$/', Rule::unique('users')->ignore($u->id)],
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        unset($data['avatar']);
        if ($r->hasFile('avatar')) {
            $old = $u->avatar_url;
            $data['avatar_url'] = 'storage/'.$r->file('avatar')->store('avatars', 'public');
            $u->forceFill($data)->save();
            if ($old && str_starts_with($old, 'storage/avatars/')) {
                Storage::disk('public')->delete(substr($old, 8));
            }
        } else {
            $u->forceFill($data)->save();
        }

        return back()->with('success', 'Đã lưu hồ sơ của bạn.');
    }

    public function uploadAvatar(Request $r)
    {
        $r->validate(['avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'], [
            'avatar.required' => 'Hãy chọn ảnh trước khi lưu.',
            'avatar.max' => 'Ảnh tối đa 2 MB.',
            'avatar.image' => 'Vui lòng chọn ảnh JPG, PNG hoặc WebP.',
        ]);
        $u = $r->user();
        $old = $u->avatar_url;
        $path = $r->file('avatar')->store('avatars', 'public');
        try {
            $u->forceFill(['avatar_url' => 'storage/'.$path])->save();
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            throw $e;
        }
        if ($old && str_starts_with($old, 'storage/avatars/')) {
            Storage::disk('public')->delete(substr($old, 8));
        }
        return back()->with('success', 'Đã cập nhật ảnh đại diện.');
    }

    public function avatar(Request $r)
    {
        $path = (string) $r->user()->avatar_url;
        abort_unless(str_starts_with($path, 'storage/avatars/') && ! str_contains($path, '..'), 404);
        $path = substr($path, 8);
        abort_unless(Storage::disk('public')->exists($path), 404);
        return response()->file(Storage::disk('public')->path($path), ['Cache-Control' => 'private, no-store']);
    }

    public function cancelOrder(Request $r, Order $order)
    {
        abort_unless((int) $order->user_id === (int) $r->user()->id, 404);
        $data = $r->validate([
            'reason' => ['required', Rule::in(['Đổi ý mua hàng', 'Muốn đổi sản phẩm', 'Cần đổi địa chỉ nhận hàng', 'Khác'])],
            'detail' => 'nullable|required_if:reason,Khác|string|max:180',
        ]);
        DB::transaction(function () use ($r, $order, $data) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($locked->status, ['pending', 'confirmed'], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['order' => 'Chỉ có thể hủy đơn chờ xác nhận hoặc đã xác nhận, chưa giao hàng.']);
            }
            $from = $locked->status;
            app(\App\Support\OrderInventory::class)->restore($locked, true);
            $reason = $data['reason'].(! empty($data['detail']) ? ': '.$data['detail'] : '');
            $locked->update(['status' => 'cancelled', 'cancel_reason' => $reason]);
            $locked->statusHistory()->create(['from_status' => $from, 'to_status' => 'cancelled', 'changed_by' => $r->user()->id, 'note' => $reason]);
        }, 3);
        return back()->with('success', 'Đã hủy đơn hàng. Nếu đã thanh toán, vui lòng liên hệ hỗ trợ để được xử lý hoàn tiền.');
    }

    public function password(Request $r)
    {
        $data = $r->validate(['current_password' => 'required|current_password', 'password' => 'required|string|min:8|confirmed']);
        $r->user()->forceFill(['password' => $data['password']])->save();
        $r->session()->regenerate();

        return back()->with('success', 'Đã đổi mật khẩu.');
    }

    public function orders(Request $r)
    {
        $r->validate(['status' => ['nullable', Rule::in([...array_keys(Order::STATUSES),'review'])], 'q' => 'nullable|string|max:50']);
        $orders = $r->user()->orders()->with(['items.variant.product','items.review'])
            ->when($r->status,fn($q,$s)=>$s==='review'?$q->where('status','completed')->whereNotNull('received_at')->whereHas('items',fn($i)=>$i->doesntHave('review')):$q->where('status',$s))
            ->when($r->q, fn ($q, $s) => $q->where(fn($w)=>$w->where('order_code','like','%'.$s.'%')->orWhereHas('items',fn($i)=>$i->where('product_name','like','%'.$s.'%'))))
            ->latest()->paginate(8)->withQueryString();

        return view('customer.orders',['orders'=>$orders,'counts'=>$r->user()->orders()->selectRaw('status,count(*) as total')->groupBy('status')->pluck('total','status')]);
    }

    public function order(Request $r, Order $order)
    {
        abort_unless((int) $order->user_id === $r->user()->id, 404);
        $order->load('items.variant.product','items.review','statusHistory');

        return view('customer.order', compact('order'));
    }

    public function favorites(Request $r)
    {
        $r->validate(['q'=>'nullable|string|max:80']);
        $products = Product::forCard()->when($r->q,fn($q,$term)=>$q->where('name','like','%'.$term.'%'))->whereIn('id', DB::table('customer_favorites')->select('product_id')->where('user_id', $r->user()->id))->paginate(12);

        return view('customer.favorites', compact('products'));
    }

    public function favorite(Request $r, Product $product)
    {
        abort_unless($product->is_active, 404);
        DB::table('customer_favorites')->insertOrIgnore(['user_id' => $r->user()->id, 'product_id' => $product->id, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Đã lưu sản phẩm vào danh sách yêu thích.');
    }

    public function unfavorite(Request $r, Product $product)
    {
        DB::table('customer_favorites')->where('user_id', $r->user()->id)->where('product_id', $product->id)->delete();

        return back()->with('success', 'Đã bỏ sản phẩm khỏi danh sách yêu thích.');
    }

    public function notifications(Request $r)
    {
        $notices = $this->notices($r)->select('h.id', 'h.to_status', 'h.created_at', 'o.order_code', 's.read_at')->orderByDesc('h.id')->paginate(12);

        return view('customer.notifications', compact('notices'));
    }

    public function notice(Request $r, int $notice)
    {
        $row = DB::table('order_status_history as h')->join('orders as o', 'o.id', '=', 'h.order_id')->where('o.user_id', $r->user()->id)->where('h.id', $notice)->first();
        abort_unless($row, 404);
        $data = ['read_at' => now()];
        if ($r->isMethod('delete')) {
            $data['hidden_at'] = now();
        }
        DB::table('customer_notice_states')->updateOrInsert(['user_id' => $r->user()->id, 'history_id' => $notice], $data);

        return back()->with('success', $r->isMethod('delete') ? 'Đã xóa thông báo.' : 'Đã đánh dấu đã đọc.');
    }

    public function coupons(Request $r)
    {
        $r->validate(['q'=>'nullable|string|max:60','type'=>'nullable|in:product,shipping,new']);
        $coupons=Coupon::where('is_active',true)->when($r->q,fn($q,$term)=>$q->where(fn($w)=>$w->where('code','like','%'.$term.'%')->orWhere('description','like','%'.$term.'%')))->when($r->type,fn($q,$type)=>$type==='new'?$q->where(fn($w)=>$w->where('first_order_only',true)->orWhereNotNull('new_account_days')):$q->where('scope',$type))->orderBy('expires_at')->get();

        return view('customer.coupons', compact('coupons'));
    }

    public function addressIndex(Request $r)
    {
        return view('customer.addresses', ['addresses' => $this->addresses($r)->orderByDesc('is_default')->latest()->get()]);
    }

    public function addressForm(Request $r, ?int $address = null)
    {
        $address = $address ? $this->addresses($r)->findOrFail($address) : new CustomerAddress;

        return view('customer.address-form', compact('address'));
    }

    public function saveAddress(Request $r, ?int $address = null)
    {
        $data = $r->validate(['label' => 'required|string|max:50', 'receiver_name' => 'required|string|max:100', 'receiver_phone' => 'required|regex:/^0[0-9]{9}$/', 'ship_province' => 'required|string|max:100', 'ship_district' => 'required|string|max:100', 'ship_ward' => 'required|string|max:100', 'ship_address' => 'required|string|max:255', 'is_default' => 'nullable|boolean']);
        DB::transaction(function () use ($r, $address, $data) {
            User::whereKey($r->user()->id)->lockForUpdate()->first();
            $a = $address ? $this->addresses($r)->findOrFail($address) : new CustomerAddress;
            $default = $r->boolean('is_default') || ! $this->addresses($r)->exists() || $a->is_default;
            if ($default) {
                $this->addresses($r)->update(['is_default' => false]);
            }
            $a->fill($data);
            $a->user_id = $r->user()->id;
            $a->is_default = $default;
            $a->save();
        });

        return redirect()->route('customer.addresses')->with('success', 'Đã lưu địa chỉ nhận hàng.');
    }

    public function deleteAddress(Request $r, int $address)
    {
        DB::transaction(function () use ($r, $address) {
            User::whereKey($r->user()->id)->lockForUpdate()->first();
            $a = $this->addresses($r)->findOrFail($address);
            $default = $a->is_default;
            $a->delete();
            if ($default) {
                $next = $this->addresses($r)->oldest('id')->first();
                if ($next) {
                    $next->update(['is_default' => true]);
                }
            }
        });

        return back()->with('success', 'Đã xóa địa chỉ.');
    }

    public function support(Request $r)
    {
        $messages = ContactMessage::where('user_id',$r->user()->id)->latest()->limit(10)->get();

        return view('customer.support',compact('messages'));
    }
}

@extends('admin.layout')

@section('title', $coupon->exists ? 'Sửa mã '.$coupon->code : 'Thêm mã giảm giá')

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.coupons.index') }}">← Danh sách</a>
@endsection

@section('content')
  <form class="panel form-grid" method="post" action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}">
    @csrf
    @if ($coupon->exists)
      @method('PUT')
    @endif
    <label class="field">
      <span>Mã *</span>
      <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required maxlength="30" style="text-transform: uppercase" />
    </label>
    <label class="field">
      <span>Mô tả</span>
      <input type="text" name="description" value="{{ old('description', $coupon->description) }}" maxlength="255" />
    </label>
    <label class="field">
      <span>Loại giảm *</span>
      <select name="discount_type">
        <option value="percent" @selected(old('discount_type', $coupon->discount_type) === 'percent')>Theo phần trăm (%)</option>
        <option value="fixed" @selected(old('discount_type', $coupon->discount_type) === 'fixed')>Số tiền cố định (đ)</option>
      </select>
    </label>
    <label class="field">
      <span>Mức giảm *</span>
      <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}" required min="1" />
    </label>
    <label class="field">
      <span>Giảm tối đa (đ)</span>
      <input type="number" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" min="0" />
      <small>Trần cho mã theo %. Để trống = không giới hạn.</small>
    </label>
    <label class="field">
      <span>Đơn tối thiểu (đ)</span>
      <input type="number" name="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value ?? 0) }}" min="0" />
    </label>
    <label class="field">
      <span>Số lượt dùng tối đa</span>
      <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" />
      <small>Để trống = không giới hạn. Đã dùng: {{ $coupon->used_count ?? 0 }}.</small>
    </label>
    <label class="field">
      <span>Bắt đầu</span>
      <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" />
    </label>
    <label class="field">
      <span>Hết hạn</span>
      <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" />
    </label>
    <div class="field">
      <span>Tùy chọn</span>
      <input type="hidden" name="first_order_only" value="0" />
      <label class="check"><input type="checkbox" name="first_order_only" value="1" @checked(old('first_order_only', $coupon->first_order_only)) /> Chỉ cho đơn đầu tiên</label>
      <input type="hidden" name="is_active" value="0" />
      <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active)) /> Đang bật</label>
    </div>
    <label class="field"><span>Nhóm ưu đãi</span><select name="scope">@foreach(['product'=>'Giảm giá sản phẩm','shipping'=>'Giảm phí vận chuyển'] as $v=>$label)<option value="{{ $v }}" @selected(old('scope',$coupon->scope??'product')===$v)>{{ $label }}</option>@endforeach</select></label><label class="field"><span>Hình thức giao (chỉ mã vận chuyển)</span><select name="shipping_method"><option value="">Mọi hình thức</option>@foreach(['standard'=>'Tiêu chuẩn','express'=>'Hỏa tốc'] as $v=>$label)<option value="{{ $v }}" @selected(old('shipping_method',$coupon->shipping_method)===$v)>{{ $label }}</option>@endforeach</select></label><label class="field"><span>Tài khoản tạo trong số ngày (trống = mọi tài khoản)</span><input type="number" name="new_account_days" min="1" max="365" value="{{ old('new_account_days',$coupon->new_account_days) }}"></label><label class="field"><span>Số lượt / tài khoản (trống = không giới hạn)</span><input type="number" name="per_user_limit" min="1" max="1000" value="{{ old('per_user_limit',$coupon->per_user_limit) }}"></label>
    <div class="form-actions field-full">
      <button class="btn btn-primary" type="submit">Lưu</button>
    </div>
  </form>
@endsection

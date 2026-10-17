<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public const POSITIONS = [
        'home_hero' => 'Banner lớn đầu trang chủ',
        'home_category' => 'Thẻ "Danh mục hàng đầu"',
        'home_promo' => 'Thẻ ưu đãi',
        'home_wide' => 'Khối rộng giữa/cuối trang',
    ];

    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $this->save($request, new Banner);

        return redirect()->route('admin.banners.index')->with('success', 'Đã thêm banner.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $this->save($request, $banner);

        return redirect()->route('admin.banners.index')->with('success', 'Đã lưu banner.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }

    private function save(Request $request, Banner $banner): void
    {
        $data = $request->validate([
            'position' => ['required', Rule::in(array_keys(self::POSITIONS))],
            'title' => 'nullable|string|max:200',
            'subtitle' => 'nullable|string|max:200',
            'alt_text' => 'nullable|string|max:200',
            'button_text' => 'nullable|string|max:60',
            'link_url' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'image' => [$banner->image_url ? 'nullable' : 'required', 'image', 'max:4096'],
        ], ['image.required' => 'Hãy chọn ảnh banner.', 'ends_at.after_or_equal' => 'Ngày kết thúc phải sau ngày bắt đầu.']);

        $banner->fill([
            'image_url' => store_upload($request->file('image'), $banner->image_url),
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => $request->user()->id,
        ] + collect($data)->except('image', 'sort_order')->all())->save();
    }
}

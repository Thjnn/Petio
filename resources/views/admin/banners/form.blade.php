@extends('admin.layout')

@section('title', $banner->exists ? 'Sửa banner' : 'Thêm banner')

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.banners.index') }}">← Danh sách</a>
@endsection

@section('content')
  <form class="panel form-grid" method="post" enctype="multipart/form-data"
    action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}">
    @csrf
    @if ($banner->exists)
      @method('PUT')
    @endif
    <label class="field">
      <span>Vị trí *</span>
      <select name="position">
        @foreach (\App\Http\Controllers\Admin\BannerController::POSITIONS as $key => $label)
          <option value="{{ $key }}" @selected(old('position', $banner->position) === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="field">
      <span>Thứ tự</span>
      <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" />
    </label>
    <label class="field field-full">
      <span>Tiêu đề</span>
      <textarea name="title" maxlength="200" rows="2" style="min-height: 0">{{ old('title', $banner->title) }}</textarea>
      <small>Enter để xuống dòng.</small>
    </label>
    <label class="field">
      <span>Dòng phụ</span>
      <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" maxlength="200" />
    </label>
    <label class="field">
      <span>Mô tả ảnh (alt)</span>
      <input type="text" name="alt_text" value="{{ old('alt_text', $banner->alt_text) }}" maxlength="200" />
    </label>
    <label class="field">
      <span>Chữ trên nút</span>
      <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" maxlength="60" placeholder="Mua ngay" />
    </label>
    <label class="field">
      <span>Liên kết</span>
      <input type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" maxlength="255" placeholder="shop?cat=thuc-an" />
    </label>
    <label class="field">
      <span>Bắt đầu hiện</span>
      <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d\TH:i')) }}" />
    </label>
    <label class="field">
      <span>Ngừng hiện</span>
      <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d\TH:i')) }}" />
    </label>
    <div class="field">
      <span>Ảnh {{ $banner->image_url ? '' : '*' }}</span>
      @if ($banner->image_url)
        <img class="preview-img" src="{{ asset($banner->image_url) }}" alt="" />
      @endif
      <input type="file" name="image" accept="image/*" />
    </div>
    <div class="field">
      <span>Hiển thị</span>
      <input type="hidden" name="is_active" value="0" />
      <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active)) /> Đang bật</label>
    </div>
    <div class="form-actions field-full">
      <button class="btn btn-primary" type="submit">Lưu</button>
    </div>
  </form>
@endsection

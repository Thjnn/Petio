@extends('admin.layout')

@section('title', $category->exists ? 'Sửa danh mục' : 'Thêm danh mục')

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.categories.index') }}">← Danh sách</a>
@endsection

@section('content')
  <form class="panel form-grid" method="post" enctype="multipart/form-data"
    action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if ($category->exists)
      @method('PUT')
    @endif
    <label class="field">
      <span>Tên *</span>
      <input type="text" name="name" value="{{ old('name', $category->name) }}" required maxlength="120" />
    </label>
    <label class="field">
      <span>Slug</span>
      <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="140" />
      <small>Dùng trên URL cửa hàng (?cat=...). Để trống sẽ tự tạo.</small>
    </label>
    <label class="field">
      <span>Danh mục cha</span>
      <select name="parent_id">
        <option value="">— Danh mục gốc —</option>
        @foreach ($parents as $p)
          <option value="{{ $p->id }}" @selected(old('parent_id', $category->parent_id) == $p->id)>{{ $p->name }}</option>
        @endforeach
      </select>
    </label>
    <label class="field">
      <span>Thứ tự</span>
      <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" />
    </label>
    <div class="field">
      <span>Ảnh</span>
      @if ($category->image_url)
        <img class="preview-img" src="{{ asset($category->image_url) }}" alt="" />
      @endif
      <input type="file" name="image" accept="image/*" />
    </div>
    <div class="field">
      <span>Hiển thị</span>
      <input type="hidden" name="is_active" value="0" />
      <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) /> Hiện trên cửa hàng</label>
    </div>
    <div class="form-actions field-full">
      <button class="btn btn-primary" type="submit">Lưu</button>
    </div>
  </form>
@endsection

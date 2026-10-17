@extends('admin.layout')

@section('title', $product->exists ? 'Sửa: '.$product->name : 'Thêm sản phẩm')

@section('actions')
  @if ($product->exists && $product->is_active)
    <a class="btn btn-light" href="{{ route('products.show', $product) }}" target="_blank">Xem trên web</a>
  @endif
  <a class="btn btn-light" href="{{ route('admin.products.index') }}">← Danh sách</a>
@endsection

@section('content')
  @php
    // Sau lỗi validate thì hiện lại đúng các dòng vừa nhập
    $variants = old('variants', $product->allVariants->mapWithKeys(fn ($v, $i) => [$i => $v->only('id', 'sku', 'name', 'price', 'old_price', 'stock_qty', 'is_active')])->all())
      ?: [0 => ['name' => 'Mặc định', 'is_active' => 1]];
    $defaultKey = old('default_variant', $product->allVariants->search(fn ($v) => $v->is_default) ?: array_key_first($variants));
    $tagIds = old('tags', $product->tags->pluck('id')->all());
  @endphp

  <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($product->exists)
      @method('PUT')
    @endif

    <section class="panel">
      <h2>Thông tin chung</h2>
      <div class="form-grid">
        <label class="field field-full">
          <span>Tên sản phẩm *</span>
          <input type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="200" />
        </label>
        <label class="field">
          <span>Đường dẫn (slug)</span>
          <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" maxlength="220" />
          <small>Để trống sẽ tự tạo từ tên.</small>
        </label>
        <label class="field">
          <span>Danh mục *</span>
          <select name="category_id" required>
            @foreach ($categories as $c)
              <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </label>
        <label class="field">
          <span>Thương hiệu</span>
          <select name="brand_id">
            <option value="">— Không —</option>
            @foreach ($brands as $b)
              <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>
            @endforeach
          </select>
        </label>
        <label class="field">
          <span>Dành cho</span>
          <select name="pet_type">
            @foreach (['both' => 'Chó & mèo', 'dog' => 'Chó', 'cat' => 'Mèo'] as $key => $label)
              <option value="{{ $key }}" @selected(old('pet_type', $product->pet_type ?? 'both') === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </label>
        <label class="field field-full">
          <span>Mô tả ngắn</span>
          <textarea name="short_description" maxlength="500">{{ old('short_description', $product->short_description) }}</textarea>
        </label>
        <label class="field field-full">
          <span>Mô tả chi tiết (HTML)</span>
          <textarea name="description" class="code">{{ old('description', $product->description) }}</textarea>
        </label>
        <div class="field">
          <span>Ảnh sản phẩm {{ $product->thumbnail_url ? '' : '*' }}</span>
          @if ($product->thumbnail_url)
            <img class="preview-img" src="{{ asset($product->thumbnail_url) }}" alt="" />
          @endif
          <input type="file" name="thumbnail" accept="image/*" />
          @if ($product->thumbnail_url)
            <small>Chọn ảnh mới để thay, bỏ trống để giữ ảnh cũ.</small>
          @endif
        </div>
        <div class="field">
          <span>Hiển thị</span>
          <input type="hidden" name="is_active" value="0" />
          <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active)) /> Đang bán</label>
          <input type="hidden" name="is_featured" value="0" />
          <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured)) /> Nổi bật trên trang chủ</label>
        </div>
        <div class="field field-full">
          <span>Tag</span>
          <div class="check-list">
            @foreach ($tags as $t)
              <label class="check"><input type="checkbox" name="tags[]" value="{{ $t->id }}" @checked(in_array($t->id, $tagIds)) /> {{ $t->name }}</label>
            @endforeach
          </div>
        </div>
      </div>
    </section>

    <section class="panel panel-flush">
      <h2 style="padding: 24px 24px 0">Biến thể (size / giá / tồn kho)</h2>
      <table class="table rows-table">
        <thead>
          <tr><th>Mặc định</th><th>Tên *</th><th>SKU *</th><th>Giá bán *</th><th>Giá gốc</th><th>Tồn kho *</th><th>Bán</th><th></th></tr>
        </thead>
        <tbody id="variant-rows">
          @foreach ($variants as $key => $v)
            <tr>
              <td>
                <input type="hidden" name="variants[{{ $key }}][id]" value="{{ $v['id'] ?? '' }}" />
                <input type="radio" name="default_variant" value="{{ $key }}" aria-label="Biến thể mặc định" @checked($defaultKey == $key) />
              </td>
              <td><input type="text" name="variants[{{ $key }}][name]" value="{{ $v['name'] ?? '' }}" required maxlength="100" /></td>
              <td><input type="text" name="variants[{{ $key }}][sku]" value="{{ $v['sku'] ?? '' }}" required maxlength="50" /></td>
              <td><input type="number" name="variants[{{ $key }}][price]" value="{{ $v['price'] ?? '' }}" required min="0" /></td>
              <td><input type="number" name="variants[{{ $key }}][old_price]" value="{{ $v['old_price'] ?? '' }}" min="0" /></td>
              <td><input type="number" name="variants[{{ $key }}][stock_qty]" value="{{ $v['stock_qty'] ?? 0 }}" required min="0" /></td>
              <td><input type="checkbox" name="variants[{{ $key }}][is_active]" value="1" aria-label="Đang bán" @checked(! empty($v['is_active'])) /></td>
              <td><button type="button" class="btn btn-sm btn-danger" data-remove-row>Gỡ</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding: 0 24px 24px">
        <button type="button" class="btn btn-sm btn-light" data-add-row="variant-rows">+ Thêm biến thể</button>
        <small class="muted">Biến thể đã có trong đơn cũ vẫn gỡ được, đơn cũ giữ nguyên tên và giá lúc đặt.</small>
      </div>
    </section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Lưu sản phẩm</button>
    </div>
  </form>
@endsection

@push('scripts')
  @include('admin._rows-script')
@endpush

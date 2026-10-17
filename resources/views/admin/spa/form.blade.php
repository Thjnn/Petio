@extends('admin.layout')

@section('title', $service->exists ? 'Sửa dịch vụ' : 'Thêm dịch vụ spa')

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.spa.index') }}">← Danh sách</a>
@endsection

@section('content')
  @php
    $prices = old('prices', $service->prices->map->only('weight_over_kg', 'weight_up_to_kg', 'price')->all()) ?: [[]];
  @endphp
  <form method="post" action="{{ $service->exists ? route('admin.spa.update', $service) : route('admin.spa.store') }}">
    @csrf
    @if ($service->exists)
      @method('PUT')
    @endif
    <section class="panel form-grid">
      <label class="field">
        <span>Tên dịch vụ *</span>
        <input type="text" name="name" value="{{ old('name', $service->name) }}" required maxlength="120" />
      </label>
      <label class="field">
        <span>Cách tính giá *</span>
        <select name="pricing_type">
          <option value="by_weight" @selected(old('pricing_type', $service->pricing_type) === 'by_weight')>Theo cân nặng (bảng giá bên dưới)</option>
          <option value="range" @selected(old('pricing_type', $service->pricing_type) === 'range')>Khoảng giá (từ – đến)</option>
        </select>
      </label>
      <label class="field">
        <span>Giá từ (đ)</span>
        <input type="number" name="price_from" value="{{ old('price_from', $service->price_from) }}" min="0" />
        <small>Chỉ dùng khi tính theo khoảng giá.</small>
      </label>
      <label class="field">
        <span>Giá đến (đ)</span>
        <input type="number" name="price_to" value="{{ old('price_to', $service->price_to) }}" min="0" />
      </label>
      <label class="field">
        <span>Thời gian (phút)</span>
        <input type="number" name="duration_min" value="{{ old('duration_min', $service->duration_min) }}" min="0" />
      </label>
      <label class="field">
        <span>Thứ tự</span>
        <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}" />
      </label>
      <label class="field field-full">
        <span>Mô tả</span>
        <textarea name="description" maxlength="500">{{ old('description', $service->description) }}</textarea>
      </label>
      <div class="field">
        <input type="hidden" name="is_active" value="0" />
        <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->is_active)) /> Hiện trên trang spa</label>
      </div>
    </section>

    <section class="panel panel-flush">
      <h2 style="padding: 24px 24px 0">Bảng giá theo cân nặng</h2>
      <table class="table rows-table">
        <thead><tr><th>Trên (kg)</th><th>Đến (kg)</th><th>Giá (đ)</th><th></th></tr></thead>
        <tbody id="price-rows">
          @foreach ($prices as $i => $p)
            <tr>
              <td><input type="number" step="0.1" min="0" name="prices[{{ $i }}][weight_over_kg]" value="{{ $p['weight_over_kg'] ?? '' }}" /></td>
              <td><input type="number" step="0.1" min="0" name="prices[{{ $i }}][weight_up_to_kg]" value="{{ $p['weight_up_to_kg'] ?? '' }}" /></td>
              <td><input type="number" min="0" name="prices[{{ $i }}][price]" value="{{ $p['price'] ?? '' }}" /></td>
              <td><button type="button" class="btn btn-sm btn-danger" data-remove-row>Gỡ</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div style="padding: 0 24px 24px">
        <button type="button" class="btn btn-sm btn-light" data-add-row="price-rows">+ Thêm mốc</button>
        <small class="muted">Mốc đầu tiên để "Trên" = 0 sẽ hiện là "Dưới X kg".</small>
      </div>
    </section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Lưu dịch vụ</button>
    </div>
  </form>
@endsection

@push('scripts')
  @include('admin._rows-script')
@endpush

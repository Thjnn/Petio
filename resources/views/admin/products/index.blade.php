@extends('admin.layout')

@section('title', 'Sản phẩm')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.products.create') }}">+ Thêm sản phẩm</a>
@endsection

@section('content')
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tên sản phẩm..." aria-label="Tìm sản phẩm" />
    <select name="category" aria-label="Danh mục" onchange="this.form.submit()">
      <option value="">Mọi danh mục</option>
      @foreach ($categories as $c)
        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
      @endforeach
    </select>
    <button class="btn" type="submit">Lọc</button>
  </form>

  <section class="panel panel-flush">
    @if ($products->isEmpty())
      <p class="empty">Không có sản phẩm nào.</p>
    @else
      <table class="table">
        <tr><th></th><th>Tên</th><th>Danh mục</th><th class="num">Giá</th><th class="num">Tồn</th><th class="num">Đã bán</th><th>Trạng thái</th><th></th></tr>
        @foreach ($products as $p)
          @php($prices = $p->allVariants->pluck('price'))
          <tr>
            <td><img class="thumb" src="{{ asset($p->thumbnail_url) }}" alt="" loading="lazy" /></td>
            <td>
              <a href="{{ route('admin.products.edit', $p) }}"><strong>{{ $p->name }}</strong></a>
              @if ($p->is_featured)
                <span class="badge badge-confirmed">Nổi bật</span>
              @endif
            </td>
            <td>{{ $p->category?->name }}</td>
            <td class="num">{{ $prices->min() === $prices->max() ? vnd($prices->min()) : vnd($prices->min()).' – '.vnd($prices->max()) }}</td>
            <td class="num">{{ $p->allVariants->sum('stock_qty') }}</td>
            <td class="num">{{ $p->sold_count }}</td>
            <td><span class="badge badge-{{ $p->is_active ? 'active' : 'locked' }}">{{ $p->is_active ? 'Đang bán' : 'Đã ẩn' }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.products.edit', $p) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.products.destroy', $p)])
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
  {{ $products->links('pagination::bootstrap-4') }}
@endsection

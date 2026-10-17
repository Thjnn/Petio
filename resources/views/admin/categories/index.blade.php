@extends('admin.layout')

@section('title', 'Danh mục sản phẩm')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.categories.create') }}">+ Thêm danh mục</a>
@endsection

@section('content')
  <section class="panel panel-flush">
    <table class="table">
      <tr><th>Tên</th><th>Slug</th><th>Danh mục cha</th><th class="num">Thứ tự</th><th class="num">Sản phẩm</th><th>Trạng thái</th><th></th></tr>
      @foreach ($categories as $c)
        <tr>
          <td><a href="{{ route('admin.categories.edit', $c) }}"><strong>{{ $c->parent ? '— ' : '' }}{{ $c->name }}</strong></a></td>
          <td class="muted">{{ $c->slug }}</td>
          <td>{{ $c->parent?->name ?? '—' }}</td>
          <td class="num">{{ $c->sort_order }}</td>
          <td class="num"><a href="{{ route('admin.products.index', ['category' => $c->id]) }}">{{ $productCounts[$c->id] ?? 0 }}</a></td>
          <td><span class="badge badge-{{ $c->is_active ? 'active' : 'locked' }}">{{ $c->is_active ? 'Hiện' : 'Ẩn' }}</span></td>
          <td class="actions">
            <a class="btn btn-sm btn-light" href="{{ route('admin.categories.edit', $c) }}">Sửa</a>
            @include('admin._delete', ['action' => route('admin.categories.destroy', $c)])
          </td>
        </tr>
      @endforeach
    </table>
  </section>
@endsection

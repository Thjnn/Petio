@extends('admin.layout')

@section('title', 'Đơn hàng')

@section('content')
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Mã đơn, tên, SĐT..." aria-label="Tìm đơn" />
    <select name="status" aria-label="Trạng thái" onchange="this.form.submit()">
      <option value="">Mọi trạng thái</option>
      @foreach (\App\Models\Order::STATUSES as $key => $label)
        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn" type="submit">Lọc</button>
  </form>

  <section class="panel panel-flush">
    @include('admin.orders._table')
  </section>
  {{ $orders->links('pagination::bootstrap-4') }}
@endsection

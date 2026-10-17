@extends('admin.layout')

@section('title', 'Bản tin')

@section('content')
  <div class="stats">
    <div class="stat"><span>Đang nhận tin</span><strong>{{ $activeCount }}</strong></div>
  </div>
  <section class="panel panel-flush">
    @if ($subscribers->isEmpty())
      <p class="empty">Chưa có ai đăng ký.</p>
    @else
      <table class="table">
        <tr><th>Email</th><th>Ngày đăng ký</th><th>Trạng thái</th></tr>
        @foreach ($subscribers as $s)
          <tr>
            <td>{{ $s->email }}</td>
            <td>{{ \Illuminate\Support\Carbon::parse($s->subscribed_at)->format('d/m/Y H:i') }}</td>
            <td><span class="badge badge-{{ $s->is_active ? 'active' : 'locked' }}">{{ $s->is_active ? 'Đang nhận' : 'Đã hủy' }}</span></td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
  {{ $subscribers->links('pagination::bootstrap-4') }}
@endsection

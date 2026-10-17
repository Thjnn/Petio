@extends('admin.layout')

@section('title', 'Liên hệ')

@section('content')
  @php($statuses = \App\Http\Controllers\Admin\ContactMessageController::STATUSES)
  <form class="filters" method="get">
    <select name="status" aria-label="Trạng thái" onchange="this.form.submit()">
      <option value="">Tất cả</option>
      @foreach ($statuses as $key => $label)
        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
      @endforeach
    </select>
  </form>

  <section class="panel panel-flush">
    @if ($messages->isEmpty())
      <p class="empty">Không có tin nhắn.</p>
    @else
      <table class="table">
        <tr><th>Người gửi</th><th>Nội dung</th><th>Ngày gửi</th><th>Trạng thái</th></tr>
        @foreach ($messages as $m)
          <tr>
            <td><a href="{{ route('admin.contacts.show', $m) }}"><strong>{{ $m->full_name }}</strong></a><br /><span class="muted">{{ $m->phone }}</span></td>
            <td>{{ \Illuminate\Support\Str::limit($m->content, 90) }}</td>
            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
            <td><span class="badge badge-{{ $m->status }}">{{ $statuses[$m->status] }}</span></td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
  {{ $messages->links('pagination::bootstrap-4') }}
@endsection

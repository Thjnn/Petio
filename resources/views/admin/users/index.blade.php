@extends('admin.layout')

@section('title', 'Tài khoản')

@section('content')
  @php($roles = \App\Http\Controllers\Admin\UserController::ROLES)
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tên, email, SĐT..." aria-label="Tìm tài khoản" />
    <select name="role" aria-label="Vai trò" onchange="this.form.submit()">
      <option value="">Mọi vai trò</option>
      @foreach ($roles as $key => $label)
        <option value="{{ $key }}" @selected(request('role') === $key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn" type="submit">Lọc</button>
  </form>

  <section class="panel panel-flush">
    <table class="table">
      <tr><th>Tài khoản</th><th>Liên hệ</th><th class="num">Đơn</th><th>Đăng nhập gần nhất</th><th>Vai trò / trạng thái</th></tr>
      @foreach ($users as $u)
        <tr>
          <td><strong>{{ $u->username }}</strong><br /><span class="muted">{{ $u->full_name }}</span></td>
          <td>{{ $u->email }}<br /><span class="muted">{{ $u->phone }}</span></td>
          <td class="num">{{ $u->orders_count }}</td>
          <td>{{ $u->last_login_at?->format('d/m/Y H:i') ?? '—' }}</td>
          <td>
            @can('manage-users')
              <form action="{{ route('admin.users.update', $u) }}" method="post" class="filters" style="margin: 0">
                @csrf
                @method('PUT')
                <select name="role" aria-label="Vai trò" style="min-width: 0">
                  @foreach ($roles as $key => $label)
                    <option value="{{ $key }}" @selected($u->role === $key)>{{ $label }}</option>
                  @endforeach
                </select>
                <select name="status" aria-label="Trạng thái" style="min-width: 0">
                  <option value="active" @selected($u->status === 'active')>Hoạt động</option>
                  <option value="locked" @selected($u->status === 'locked')>Khóa</option>
                </select>
                <button class="btn btn-sm btn-light" type="submit">Lưu</button>
              </form>
            @else
              {{ $roles[$u->role] }} · <span class="badge badge-{{ $u->status }}">{{ $u->status === 'active' ? 'Hoạt động' : 'Khóa' }}</span>
            @endcan
          </td>
        </tr>
      @endforeach
    </table>
  </section>
  {{ $users->links('pagination::bootstrap-4') }}
@endsection

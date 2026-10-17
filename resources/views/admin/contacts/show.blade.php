@extends('admin.layout')

@section('title', 'Liên hệ từ '.$contact->full_name)

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.contacts.index') }}">← Danh sách</a>
@endsection

@section('content')
  <div class="grid-2">
    <section class="panel">
      <dl class="info">
        <dt>Họ tên</dt><dd>{{ $contact->full_name }}</dd>
        <dt>Email</dt><dd><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></dd>
        <dt>Điện thoại</dt><dd><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></dd>
        <dt>Ngày gửi</dt><dd>{{ $contact->created_at->format('d/m/Y H:i') }}</dd>
      </dl>
      <p style="margin-top: 16px; white-space: pre-line">{{ $contact->content }}</p>
    </section>

    <form class="panel form-grid" method="post" action="{{ route('admin.contacts.update', $contact) }}">
      @csrf
      @method('PUT')
      <label class="field field-full">
        <span>Trạng thái</span>
        <select name="status">
          @foreach (\App\Http\Controllers\Admin\ContactMessageController::STATUSES as $key => $label)
            <option value="{{ $key }}" @selected($contact->status === $key)>{{ $label }}</option>
          @endforeach
        </select>
      </label>
      <label class="field field-full">
        <span>Ghi chú nội bộ</span>
        <textarea name="admin_note" maxlength="500">{{ old('admin_note', $contact->admin_note) }}</textarea>
      </label>
      <div class="form-actions field-full">
        <button class="btn btn-primary" type="submit">Lưu</button>
      </div>
    </form>
  </div>
@endsection

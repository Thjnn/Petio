@extends('admin.layout')

@section('title', 'Câu hỏi thường gặp')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.faqs.create') }}">+ Thêm câu hỏi</a>
@endsection

@section('content')
  @forelse ($faqs as $section => $items)
    <section class="panel panel-flush">
      <h2 style="padding: 24px 24px 0">{{ $section }}</h2>
      <table class="table">
        @foreach ($items as $faq)
          <tr>
            <td style="width: 60px" class="num">{{ $faq->sort_order }}</td>
            <td><a href="{{ route('admin.faqs.edit', $faq) }}">{{ $faq->question }}</a></td>
            <td style="width: 80px"><span class="badge badge-{{ $faq->is_active ? 'active' : 'locked' }}">{{ $faq->is_active ? 'Hiện' : 'Ẩn' }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.faqs.edit', $faq) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.faqs.destroy', $faq)])
            </td>
          </tr>
        @endforeach
      </table>
    </section>
  @empty
    <p class="panel empty">Chưa có câu hỏi.</p>
  @endforelse
@endsection

@extends('admin.layout')

@section('title', $faq->exists ? 'Sửa câu hỏi' : 'Thêm câu hỏi')

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.faqs.index') }}">← Danh sách</a>
@endsection

@section('content')
  <form class="panel form-grid" method="post" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
    @csrf
    @if ($faq->exists)
      @method('PUT')
    @endif
    <label class="field">
      <span>Mục *</span>
      <input type="text" name="section_title" value="{{ old('section_title', $faq->section_title) }}" required maxlength="120" list="faq-sections" />
      <datalist id="faq-sections">
        @foreach ($sections as $s)
          <option value="{{ $s }}"></option>
        @endforeach
      </datalist>
      <small>Gõ đúng tên mục có sẵn để xếp chung, ví dụ "01. Đặt hàng".</small>
    </label>
    <label class="field">
      <span>Thứ tự mục</span>
      <input type="number" name="section_order" value="{{ old('section_order', $faq->section_order ?? 0) }}" />
    </label>
    <label class="field">
      <span>Thứ tự câu hỏi</span>
      <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order ?? 0) }}" />
    </label>
    <label class="field field-full">
      <span>Câu hỏi *</span>
      <input type="text" name="question" value="{{ old('question', $faq->question) }}" required maxlength="500" />
    </label>
    <label class="field field-full">
      <span>Trả lời *</span>
      <textarea name="answer" required rows="6">{{ old('answer', $faq->answer) }}</textarea>
    </label>
    <div class="field">
      <input type="hidden" name="is_active" value="0" />
      <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faq->is_active)) /> Hiện trên trang FAQ</label>
    </div>
    <div class="form-actions field-full">
      <button class="btn btn-primary" type="submit">Lưu</button>
    </div>
  </form>
@endsection

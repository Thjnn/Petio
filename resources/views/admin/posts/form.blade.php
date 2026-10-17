@extends('admin.layout')

@section('title', $post->exists ? 'Sửa bài viết' : 'Viết bài mới')

@section('actions')
  @if ($post->exists && $post->status === 'published')
    <a class="btn btn-light" href="{{ route('blog.show', $post) }}" target="_blank">Xem trên web</a>
  @endif
  <a class="btn btn-light" href="{{ route('admin.posts.index') }}">← Danh sách</a>
@endsection

@section('content')
  @php($tagIds = old('tags', $post->tags->pluck('id')->all()))
  <form class="panel form-grid" method="post" enctype="multipart/form-data"
    action="{{ $post->exists ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
    @csrf
    @if ($post->exists)
      @method('PUT')
    @endif
    <label class="field field-full">
      <span>Tiêu đề *</span>
      <input type="text" name="title" value="{{ old('title', $post->title) }}" required maxlength="255" />
    </label>
    <label class="field">
      <span>Slug</span>
      <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" maxlength="280" />
      <small>Để trống sẽ tự tạo từ tiêu đề.</small>
    </label>
    <label class="field">
      <span>Chuyên mục</span>
      <select name="category_id">
        <option value="">— Không —</option>
        @foreach ($categories as $c)
          <option value="{{ $c->id }}" @selected(old('category_id', $post->category_id) == $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
    </label>
    <label class="field">
      <span>Trạng thái *</span>
      <select name="status">
        @foreach (\App\Http\Controllers\Admin\BlogPostController::STATUSES as $key => $label)
          <option value="{{ $key }}" @selected(old('status', $post->status) === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </label>
    <label class="field">
      <span>Ngày đăng</span>
      <input type="datetime-local" name="published_at" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" />
      <small>Để trống khi đăng = lấy giờ hiện tại. Chọn ngày tương lai để hẹn giờ.</small>
    </label>
    <label class="field field-full">
      <span>Tóm tắt</span>
      <textarea name="summary" maxlength="500">{{ old('summary', $post->summary) }}</textarea>
    </label>
    <label class="field field-full">
      <span>Nội dung (HTML) *</span>
      <textarea name="content" class="code" required>{{ old('content', $post->content) }}</textarea>
    </label>
    <div class="field">
      <span>Ảnh đại diện {{ $post->thumbnail_url ? '' : '*' }}</span>
      @if ($post->thumbnail_url)
        <img class="preview-img" src="{{ asset($post->thumbnail_url) }}" alt="" />
      @endif
      <input type="file" name="thumbnail" accept="image/*" />
    </div>
    <div class="field">
      <span>Tag</span>
      <div class="check-list">
        @foreach ($tags as $t)
          <label class="check"><input type="checkbox" name="tags[]" value="{{ $t->id }}" @checked(in_array($t->id, $tagIds)) /> {{ $t->name }}</label>
        @endforeach
      </div>
    </div>
    <div class="form-actions field-full">
      <button class="btn btn-primary" type="submit">Lưu bài viết</button>
    </div>
  </form>
@endsection

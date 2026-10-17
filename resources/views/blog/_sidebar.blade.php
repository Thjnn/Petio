<!-- bar -->
<div class="side-bar">
  <!-- mục -->
  <div class="categories">
    <h1>Danh mục</h1>
    <ul>
      @foreach ($categories as $category)
        <li><a href="{{ route('blog.index', ['category' => $category->slug]) }}">{{ $category->name }}</a></li>
      @endforeach
    </ul>
  </div>
  <!-- tag -->
  <div class="tags-container">
    <h2>Tags</h2>
    <div class="tags-list">
      @foreach ($tags as $tag)
        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="tag {{ request('tag') === $tag->slug ? 'active' : '' }}">{{ $tag->name }}</a>
      @endforeach
    </div>
  </div>
</div>

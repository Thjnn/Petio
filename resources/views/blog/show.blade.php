@extends('layouts.app')

@section('title', $post->title.' – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/blogchitiet.css') }}" />
@endpush

@section('content')
    <!-- hero -->
    <div class="hero">
      <div class="box-img">
        <img src="{{ asset($post->thumbnail_url) }}" alt="{{ $post->title }}" />
      </div>
      <div class="hero-summary">
        <h1>{{ $post->category?->name ?? 'Tin tức' }}</h1>
        <p>{{ $post->title }}</p>
      </div>
    </div>
    <div class="blog-container">
      <div class="main-content">
        <div class="body">
          <!-- content: HTML do admin soạn -->
          <div class="blog-content">
            {!! $post->content !!}
          </div>

          @include('blog._sidebar')
        </div>
      </div>
    </div>
    <!-- uu dai -->
    <div class="bennefit">
      <div class="body">
        @include('partials.newsletter')
      </div>
    </div>
@endsection

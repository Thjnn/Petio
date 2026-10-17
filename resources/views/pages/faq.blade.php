@extends('layouts.app')

@section('title', 'Câu hỏi thường gặp – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/FAQ.css') }}" />
@endpush

@section('content')
    <div class="main">
      <div class="faq-section">
        <div class="faq-header">
          <span class="faq-icon material-symbols-outlined">help</span>
          <h1>Câu hỏi thường gặp.</h1>
        </div>

        <div class="faq-container">
          @foreach ($sections as $title => $faqs)
            <div class="faq-column">
              <div class="faq-item reveal">
                <h2>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ $title }}</h2>
                @foreach ($faqs as $faq)
                  <details class="faq-q">
                    <summary>{{ $faq->question }}</summary>
                    <div class="faq-a">
                      <p>{{ $faq->answer }}</p>
                    </div>
                  </details>
                @endforeach
              </div>
            </div>
          @endforeach
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

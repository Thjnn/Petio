@extends('customer.layout')
@section('page-title', 'Hỗ trợ')
@section('account-content')
<div class="pc-heading"><h1>Hỗ trợ</h1></div>
<section class="pc-panel"><p>Bạn cần hỗ trợ về tài khoản hoặc đơn hàng?</p><a class="pc-btn" href="{{ route('contact') }}">Gửi yêu cầu hỗ trợ</a></section>@forelse($messages as $message)<article class="pc-panel"><p>{{ $message->content }}</p><small>{{ $message->created_at->format('d/m/Y H:i') }}</small></article>@empty<p>Bạn chưa gửi yêu cầu hỗ trợ.</p>@endforelse
@endsection

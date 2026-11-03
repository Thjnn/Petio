@extends('customer.layout')
@section('page-title', 'Yêu thích')
@section('account-content')
<div class="pc-heading"><h1>Yêu thích</h1></div>
<div class="pc-hub-grid">@forelse($products as $product)<div class="pc-panel"><x-product-card :product="$product" /><form method="post" action="{{ route('customer.favorites.destroy', $product) }}">@csrf @method('DELETE')<button class="pc-btn pc-btn-outline">Bỏ yêu thích</button></form></div>@empty<p class="pc-panel">Bạn chưa lưu sản phẩm yêu thích.</p>@endforelse</div>{{ $products->links('customer.pagination') }}
@endsection

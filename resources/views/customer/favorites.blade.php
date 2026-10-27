@extends('customer.layout')
@section('page-title','Góc yêu thích')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Góc yêu thích</h1><p>Những món đáng yêu bạn muốn dành cho bé cưng.</p></div></div>

<div class="pc-product-grid">@forelse($products as $p)<article class="pc-panel pc-product"><a href="{{ route('products.show',$p) }}"><img src="{{ asset($p->thumbnail_url) }}" alt="{{ $p->name }}"><h2>{{ $p->name }}</h2></a><strong class="pc-orange">{{ vnd($p->defaultVariant()->price) }}</strong><a class="pc-btn pc-btn-outline" href="{{ route('products.show',$p) }}">Chọn mua</a><form method="post" action="{{ route('customer.favorites.destroy',$p) }}">@csrf @method('DELETE')<button class="pc-text-link" type="submit">Bỏ yêu thích</button></form></article>@empty<div class="pc-panel pc-empty"><span class="material-symbols-outlined">favorite</span><h2>Góc nhỏ đang chờ được lấp đầy</h2><p>Nhấn “Lưu yêu thích” ở sản phẩm để lưu vào đây.</p><a class="pc-btn" href="{{ route('shop') }}">Tìm món bé thích</a></div>@endforelse</div><div class="pc-pagination">{{ $products->links('customer.pagination') }}</div>
@endsection

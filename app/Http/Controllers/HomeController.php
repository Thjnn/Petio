<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'hero' => Banner::showing('home_hero')->first(),
            'categoryBanners' => Banner::showing('home_category')->get(),
            'promoBanners' => Banner::showing('home_promo')->get(),
            'featured' => Product::forCard()->where('is_featured', true)->orderBy('id')->take(8)->get(),
            'recommended' => Product::forCard()->inRandomOrder()->take(5)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function index()
    {
        return view('admin.newsletter.index', [
            'subscribers' => NewsletterSubscriber::latest('subscribed_at')->paginate(50),
            'activeCount' => NewsletterSubscriber::where('is_active', true)->count(),
        ]);
    }
}

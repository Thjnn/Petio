<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'content' => 'required|string|max:5000',
        ], [
            'full_name.required' => 'Hãy cho chúng tôi biết tên bạn.',
            'email.required' => 'Bạn chưa nhập Email.',
            'email.email' => 'Email không hợp lệ.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'content.required' => 'Bạn cần giúp gì nhỉ?',
        ]);

        ContactMessage::create($data + ['user_id' => $request->user()?->id]);

        return back()->with('success', 'Đã gửi! Chúng tôi sẽ phản hồi sớm thôi :3');
    }

    /** Ô "Đăng ký nhận bản tin" ở các trang. */
    public function subscribe(Request $request)
    {
        $data = $request->validate(
            ['email' => 'required|email|max:150'],
            ['email.required' => 'Bạn chưa nhập email.', 'email.email' => 'Email không hợp lệ.'],
        );

        NewsletterSubscriber::updateOrCreate(
            ['email' => strtolower($data['email'])],
            ['is_active' => true, 'unsubscribed_at' => null],
        );

        return back()->with('success', 'Đăng ký nhận bản tin thành công! Dùng mã NEWS10 để giảm 10% cho đơn tiếp theo.');
    }
}

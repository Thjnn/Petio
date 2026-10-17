<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public const STATUSES = ['new' => 'Mới', 'read' => 'Đã xem', 'replied' => 'Đã trả lời'];

    public function index(Request $request)
    {
        $messages = ContactMessage::latest()
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->paginate(20)->withQueryString();

        return view('admin.contacts.index', compact('messages'));
    }

    public function show(ContactMessage $contact)
    {
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function update(Request $request, ContactMessage $contact)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(self::STATUSES)),
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($data['status'] === 'replied' && ! $contact->replied_at) {
            $data['replied_at'] = now();
        }
        $contact->update($data);

        return redirect()->route('admin.contacts.index')->with('success', 'Đã cập nhật liên hệ.');
    }
}

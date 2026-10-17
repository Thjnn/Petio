<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('section_order')->orderBy('sort_order')->get()->groupBy('section_title');

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return $this->form(new Faq(['is_active' => true]));
    }

    public function store(Request $request)
    {
        $this->save($request, new Faq);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã thêm câu hỏi.');
    }

    public function edit(Faq $faq)
    {
        return $this->form($faq);
    }

    public function update(Request $request, Faq $faq)
    {
        $this->save($request, $faq);

        return redirect()->route('admin.faqs.index')->with('success', 'Đã lưu câu hỏi.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('success', 'Đã xóa câu hỏi.');
    }

    private function form(Faq $faq)
    {
        // Gợi ý các mục có sẵn để gõ đúng tên mục (datalist)
        return view('admin.faqs.form', ['faq' => $faq, 'sections' => Faq::distinct()->orderBy('section_order')->pluck('section_title')]);
    }

    private function save(Request $request, Faq $faq): void
    {
        $data = $request->validate([
            'section_title' => 'required|string|max:120',
            'section_order' => 'nullable|integer',
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $faq->fill([
            'section_order' => $data['section_order'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ] + $data)->save();
    }
}

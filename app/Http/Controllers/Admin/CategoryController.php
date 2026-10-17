<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->orderBy('parent_id')->orderBy('sort_order')->get();
        $productCounts = Product::selectRaw('category_id, count(*) as n')->groupBy('category_id')->pluck('n', 'category_id');

        return view('admin.categories.index', compact('categories', 'productCounts'));
    }

    public function create()
    {
        return $this->form(new Category(['is_active' => true]));
    }

    public function store(Request $request)
    {
        $this->save($request, new Category);

        return redirect()->route('admin.categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(Category $category)
    {
        return $this->form($category);
    }

    public function update(Request $request, Category $category)
    {
        $this->save($request, $category);

        return redirect()->route('admin.categories.index')->with('success', 'Đã lưu danh mục.');
    }

    public function destroy(Category $category)
    {
        if (Category::where('parent_id', $category->id)->exists() || Product::where('category_id', $category->id)->exists()) {
            return back()->withErrors(['category' => 'Danh mục còn danh mục con hoặc sản phẩm, hãy chuyển chúng đi trước.']);
        }
        $category->delete();

        return back()->with('success', 'Đã xóa danh mục.');
    }

    private function form(Category $category)
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parents' => Category::whereKeyNot($category->id)->orderBy('name')->get(),
        ]);
    }

    private function save(Request $request, Category $category): void
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('categories')->ignore($category->id)],
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$category->id])],
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|max:4096',
        ]);

        $category->fill([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'parent_id' => $data['parent_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'image_url' => store_upload($request->file('image'), $category->image_url),
            'is_active' => $request->boolean('is_active'),
            'updated_by' => $request->user()->id,
        ])->save();
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category', 'allVariants')->latest('id')
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->category, fn ($q, $id) => $q->where('category_id', $id))
            ->paginate(20)->withQueryString();

        return view('admin.products.index', ['products' => $products, 'categories' => Category::orderBy('name')->get()]);
    }

    public function create()
    {
        return $this->form(new Product(['is_active' => true]));
    }

    public function store(Request $request)
    {
        $product = new Product;
        $this->save($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Đã thêm sản phẩm.');
    }

    public function edit(Product $product)
    {
        return $this->form($product->load('allVariants', 'tags'));
    }

    public function update(Request $request, Product $product)
    {
        $this->save($request, $product);

        return back()->with('success', 'Đã lưu sản phẩm.');
    }

    /** Xóa hẳn vẫn an toàn: đơn cũ giữ snapshot tên/giá. Muốn tạm ẩn thì bỏ chọn "Đang bán". */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }

    private function form(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    private function save(Request $request, Product $product): void
    {
        $variantIds = $product->exists ? $product->allVariants()->pluck('id')->all() : [];

        $rules = [
            'name' => 'required|string|max:200',
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products')->ignore($product->id)],
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'pet_type' => 'required|in:dog,cat,both',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'thumbnail' => [$product->thumbnail_url ? 'nullable' : 'required', 'image', 'max:4096'],
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'default_variant' => 'nullable|integer',
            'variants' => 'required|array|min:1',
            'variants.*.id' => ['nullable', Rule::in($variantIds)],
            'variants.*.name' => 'required|string|max:100|distinct',
            'variants.*.price' => 'required|integer|min:0',
            'variants.*.old_price' => 'nullable|integer|gt:variants.*.price',
            'variants.*.stock_qty' => 'required|integer|min:0',
            'variants.*.is_active' => 'nullable|boolean',
        ];
        foreach ($request->input('variants', []) as $i => $row) {
            $rules["variants.$i.sku"] = ['required', 'string', 'max:50', 'distinct',
                Rule::unique('product_variants', 'sku')->ignore($row['id'] ?? null)];
        }

        $data = $request->validate($rules, [
            'thumbnail.required' => 'Hãy chọn ảnh sản phẩm.',
            'variants.*.old_price.gt' => 'Giá gốc phải lớn hơn giá bán.',
            'variants.*.sku.unique' => 'Mã SKU đã tồn tại.',
            'variants.*.name.distinct' => 'Tên biến thể bị trùng.',
            'variants.*.sku.distinct' => 'Mã SKU bị trùng.',
        ]);

        DB::transaction(function () use ($request, $product, $data) {
            $product->fill([
                'name' => $data['name'],
                'slug' => ($data['slug'] ?? null) ?: $this->uniqueSlug($data['name'], $product->id),
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'pet_type' => $data['pet_type'],
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'thumbnail_url' => store_upload($request->file('thumbnail'), $product->thumbnail_url),
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active'),
                'updated_by' => $request->user()->id,
            ])->save();

            $product->tags()->sync($data['tags'] ?? []);

            // Dòng nào bị gỡ khỏi form thì xóa; đơn cũ vẫn giữ snapshot
            $keep = []; // khóa dòng trên form => id biến thể
            foreach ($data['variants'] as $key => $row) {
                $variant = $product->allVariants()->updateOrCreate(['id' => $row['id'] ?? null], [
                    'sku' => $row['sku'],
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'old_price' => $row['old_price'] ?? null,
                    'stock_qty' => $row['stock_qty'],
                    'is_active' => ! empty($row['is_active']),
                    'is_default' => false,
                    'sort_order' => count($keep),
                ]);
                $keep[$key] = $variant->id;
            }
            $product->allVariants()->whereNotIn('id', $keep)->delete();

            $default = $keep[$data['default_variant'] ?? ''] ?? reset($keep);
            $product->allVariants()->whereKey($default)->update(['is_default' => true]);
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId): string
    {
        $base = $slug = Str::slug($name);
        for ($i = 2; Product::where('slug', $slug)->whereKeyNot($ignoreId)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }
}

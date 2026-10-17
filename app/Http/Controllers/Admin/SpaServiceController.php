<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SpaServiceController extends Controller
{
    public function index()
    {
        return view('admin.spa.index', ['services' => SpaService::with('prices')->orderBy('sort_order')->get()]);
    }

    public function create()
    {
        return view('admin.spa.form', ['service' => new SpaService(['is_active' => true, 'pricing_type' => 'by_weight'])]);
    }

    public function store(Request $request)
    {
        $this->save($request, new SpaService);

        return redirect()->route('admin.spa.index')->with('success', 'Đã thêm dịch vụ.');
    }

    public function edit(SpaService $service)
    {
        return view('admin.spa.form', ['service' => $service->load('prices')]);
    }

    public function update(Request $request, SpaService $service)
    {
        $this->save($request, $service);

        return redirect()->route('admin.spa.index')->with('success', 'Đã lưu dịch vụ.');
    }

    public function destroy(SpaService $service)
    {
        $service->delete();

        return back()->with('success', 'Đã xóa dịch vụ.');
    }

    private function save(Request $request, SpaService $service): void
    {
        // Bỏ các dòng giá để trống hoàn toàn
        $request->merge(['prices' => array_values(array_filter($request->input('prices', []), fn ($p) => array_filter($p) !== []))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('spa_services')->ignore($service->id)],
            'description' => 'nullable|string|max:500',
            'pricing_type' => 'required|in:by_weight,range',
            'price_from' => 'nullable|required_if:pricing_type,range|integer|min:0',
            'price_to' => 'nullable|integer|gte:price_from',
            'duration_min' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
            'prices' => 'array|required_if:pricing_type,by_weight',
            'prices.*.weight_over_kg' => 'required|numeric|min:0|distinct',
            'prices.*.weight_up_to_kg' => 'required|numeric|gt:prices.*.weight_over_kg',
            'prices.*.price' => 'required|integer|min:0',
        ], [
            'prices.required_if' => 'Dịch vụ tính theo cân nặng cần ít nhất một mốc giá.',
            'price_from.required_if' => 'Hãy nhập giá từ.',
            'prices.*.weight_up_to_kg.gt' => '"Đến kg" phải lớn hơn "Trên kg".',
        ]);

        DB::transaction(function () use ($request, $service, $data) {
            $service->fill([
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active'),
            ] + collect($data)->except('prices', 'sort_order')->all())->save();

            // Bảng giá ít dòng: thay toàn bộ cho gọn
            $service->prices()->delete();
            if ($data['pricing_type'] === 'by_weight') {
                $service->prices()->createMany($data['prices']);
            }
        });
    }
}

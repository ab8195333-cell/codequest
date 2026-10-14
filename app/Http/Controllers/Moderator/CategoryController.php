<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // إذا كان الطلب AJAX، ارجع البيانات فوراً دون جلب الكل إلا عند الطلب
        if ($request->ajax()) {
            return response()->json(Category::all(), 200);
        }

        // إذا كان طلب متصفح عادي، ارجع الصفحة فقط بدون الاستعلام عن البيانات
        return view('dashboard.Moderator.categories')->with(['success' => true, 'success_message' => 'تم تحميل الصفحة بنجاح']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التصنيف بنجاح',
            'data' => $category,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. معالجة التحديث الجماعي (إذا كانت القيمة القادمة bulk أو تحتوي على مصفوفة categories)
        if ($id === 'bulk' || $request->has('categories')) {
            $request->validate([
                'categories' => 'required|array',
                'categories.*.id' => 'required|exists:categories,id',
                'categories.*.name' => 'required|string|max:255',
                'categories.*.description' => 'nullable|string',
            ]);

            foreach ($request->categories as $item) {
                Category::where('id', $item['id'])->update([
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث جميع العناصر المحددة بنجاح.',
            ]);
        }

        // 2. معالجة التحديث المفرد
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث التصنيف بنجاح.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $elements = explode(',', $id);
        if (count($elements) > 1) {
            return $this->destroyMultiple($elements);
        }
        $category = Category::find($id);

        if (! $category) {
            return response()->json(['success' => false, 'message' => 'الفئة غير موجودة'], 404);
        }

        $category->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الفئة بنجاح']);
    }

    public function destroyMultiple($ids)
    {
        Category::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الفئات بنجاح']);
    }
}

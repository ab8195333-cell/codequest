<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // جلب الوسوم مرتبة من الأحدث إلى الأقدم
        $tags = Tag::latest('created_at')->get();
        // إذا كان الطلب AJAX، ارجع البيانات فوراً دون جلب الكل إلا عند الطلب
        if ($request->ajax()) {
            return response()->json($tags, 200);
        }

        // إذا كان طلب متصفح عادي، ارجع الصفحة فقط بدون الاستعلام عن البيانات
        return view('dashboard.Moderator.tags')->with(['success' => true, 'success_message' => 'تم تحميل الصفحة بنجاح']);
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

        $tag = Tag::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التصنيف بنجاح',
            'data' => $tag,
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
        // 1. معالجة التحديث الجماعي (إذا كانت القيمة القادمة bulk أو تحتوي على مصفوفة tags)
        if ($id === 'bulk' || $request->has('tags')) {
            $request->validate([
                'tags' => 'required|array',
                'tags.*.id' => 'required|exists:tags,id',
                'tags.*.name' => 'required|string|max:255',
                'tags.*.description' => 'nullable|string',
            ]);

            foreach ($request->tags as $item) {
                Tag::where('id', $item['id'])->update([
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
        $Tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $Tag->update($validated);

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
        $Tag = Tag::find($id);

        if (! $Tag) {
            return response()->json(['success' => false, 'message' => 'الفئة غير موجودة'], 404);
        }

        $Tag->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الفئة بنجاح']);
    }

    public function destroyMultiple($ids)
    {
        Tag::whereIn('tag_id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'تم حذف الفئات بنجاح']);
    }
}

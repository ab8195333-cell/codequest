<?php

namespace App\Http\Controllers;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Category;
use App\Helpers\Sanitizer;
use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
class QuestionController extends Controller
{

    function indexAsk(Request $request){

        $tags=Tag::all();
        $categories =Category::all();
        return view('ask', compact('tags','categories'));
    }
// في دالة show داخل QuestionController
public function show($id)
{
    $question = Question::with(['answers' => function ($query) {
        // ترتيب الإجابات: المقبولة أولاً، ثم حسب الأحدث
        $query->orderBy('is_accepted', 'desc')
              ->orderBy('created_at', 'desc');
    }, 'answers.user', 'comments'])->findOrFail($id);
    // إنشاء مفتاح فريد يعتمد على IP الزائر ورقم السؤال
    $ip = request()->ip();
    $cacheKey = "viewed_question_{$question->question_id}_by_ip_{$ip}";

    // إذا لم يكن هذا الـ IP قد زار الصفحة خلال آخر 30 دقيقة
    if (!Cache::has($cacheKey)) {
    // زيادة المشاهدات بمقدار 1
    $question->increment('views_count');

    // حفظ المفتاح في الـ Cache لمدة 30 دقيقة (1800 ثانية)
    Cache::put($cacheKey, true, now()->addMinutes(10));
    }
    return view('show', compact('question'));
}

public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string', // اسم الحقل القادم من نموذج الـ HTML (Form)
            'category_id' => 'required|exists:categories,id',
        ]);

        Question::create([
            'title' => $request->title,
            'body' => $request->description, // التعديل هنا: تخزين محتوى description في عمود body الحقيقي بالـ Database
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('questions.index')->with('success', 'تم نشر السؤال بنجاح!');
    }
  
            // 2. ربط الوسوم بالسؤال في الجدول الوسيط question_tag
            if ($request->filled('tags')) {
                $tagsInput = $request->tags;

                // إذا وصلت البيانات كمصفوفة (مثل name="tags[]") نأخذ العنصر الأول منها
                if (is_array($tagsInput)) {
                    $tagsInput = $tagsInput[0] ?? null;
                }

                // فك شفرة الـ JSON
                if (is_string($tagsInput)) {
                    $tagsData = json_decode($tagsInput, true);

                    if (is_array($tagsData)) {
                        // استخراج الـ IDs المحددة [3, 7, 9]
                        $tagIds = array_column($tagsData, 'id');

                        // ربط الوسوم بالسؤال
                        $question->tags()->attach($tagIds);
                    }
                }
            }
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }
    public function updateindex(Question $question)
    {
        // الـ Middleware قام بالتحقق من الصلاحية وجلب السؤال تلقائياً
        $tags = Tag::all();
        $categories = Category::all();

        return view('ask', compact('question', 'tags', 'categories'));
    }
public function updateQ(Request $request, Question $question)
{
    // 1. التحقق من صحة البيانات
    $request->validate([
        'title'       => 'required|string|max:100',
        'cat_id'      => 'required|exists:categories,id',
        'description' => 'required|string',
    ]);

    // 2. تعقيم الوصف وتحديث السؤال
    $cleanDescription = Sanitizer::clean($request->description);

    $question->update([
        'title'       => $request->title,
        'description' => $cleanDescription,
        'category_id' => $request->cat_id,
    ]);

    // 3. تحديث الوسوم (Tags)
    if ($request->filled('tags')) {
        $tagsInput = $request->tags;

        if (is_array($tagsInput)) {
            $tagsInput = $tagsInput[0] ?? null;
        }

        if (is_string($tagsInput)) {
            $tagsData = json_decode($tagsInput, true);

            if (is_array($tagsData)) {
                $tagIds = array_column($tagsData, 'id');
                $question->tags()->sync($tagIds);
            }
        }
    } else {
        // في حال تم مسح كل الوسوم من النموذج
        $question->tags()->detach();
    }
    if (auth()->user()->isStaff()) {

        Notification::send($question->user_id, ' قام المشرف ' . auth()->user()->name . ' بتحديث سؤالك.', 'update_question', $question->question_id, Question::class);
    }

    // 4. التوجيه
    return redirect()->route('questions.show', $question->question_id)
        ->with('success', 'تم تحديث السؤال بنجاح');
}
    function deleteQ($id){

        $question=Question::findOrFail($id);
        $question->delete();
        if (auth()->user()->isStaff()) {

            Notification::send($question->user_id, ' قام المشرف ' . auth()->user()->name . ' بحذف سؤالك.', 'update_question', $question->question_id, Question::class);
        }

        return redirect()->route('home')->with('success', 'تم حذف السؤال بنجاح');

    }
}


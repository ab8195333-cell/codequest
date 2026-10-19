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
    function indexAsk(Request $request)
    {
        $tags = Tag::all();
        $categories = Category::all();
        return view('ask', compact('tags', 'categories'));
    }

    public function show($id)
    {
        $question = Question::with(['answers' => function ($query) {
            $query->orderBy('is_accepted', 'desc')
                  ->orderBy('created_at', 'desc');
        }, 'answers.user', 'comments'])->findOrFail($id);

        $ip = request()->ip();
        $cacheKey = "viewed_question_{$question->question_id}_by_ip_{$ip}";

        if (!Cache::has($cacheKey)) {
            $question->increment('views_count');
            Cache::put($cacheKey, true, now()->addMinutes(10));
        }

        return view('show', compact('question'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        // 1. إنشاء السؤال وحفظه مع ربط الـ description بعمود body
        $question = Question::create([
            'title' => $request->title,
            'body' => $request->description,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
        ]);

        // 2. ربط الوسوم بالسؤال في الجدول الوسيط question_tag داخل الدالة بشكل صحيح
        if ($request->filled('tags')) {
            $tagsInput = $request->tags;

            if (is_array($tagsInput)) {
                $tagsInput = $tagsInput[0] ?? null;
            }

            if (is_string($tagsInput)) {
                $tagsData = json_decode($tagsInput, true);

                if (is_array($tagsData)) {
                    $tagIds = array_column($tagsData, 'id');
                    $question->tags()->attach($tagIds);
                }
            }
        }

        return redirect()->route('questions.index')->with('success', 'تم نشر السؤال بنجاح!');
    }

    public function updateindex(Question $question)
    {
        $tags = Tag::all();
        $categories = Category::all();

        return view('ask', compact('question', 'tags', 'categories'));
    }

    public function updateQ(Request $request, Question $question)
    {
        $request->validate([
            'title'       => 'required|string|max:100',
            'cat_id'      => 'required|exists:categories,id',
            'description' => 'required|string',
        ]);

        $cleanDescription = Sanitizer::clean($request->description);

        $question->update([
            'title'       => $request->title,
            'description' => $cleanDescription,
            'category_id' => $request->cat_id,
        ]);

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
            $question->tags()->detach();
        }

        if (auth()->user()->isStaff()) {
            Notification::send($question->user_id, ' قام المشرف ' . auth()->user()->name . ' بتحديث سؤالك.', 'update_question', $question->question_id, Question::class);
        }

        return redirect()->route('questions.show', $question->question_id)
            ->with('success', 'تم تحديث السؤال بنجاح');
    }

    function deleteQ($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        if (auth()->user()->isStaff()) {
            Notification::send($question->user_id, ' قام المشرف ' . auth()->user()->name . ' بحذف سؤالك.', 'update_question', $question->question_id, Question::class);
        }

        return redirect()->route('home')->with('success', 'تم حذف السؤال بنجاح');
    }
}
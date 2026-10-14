<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $questions = Question::with(['user', 'category', 'tags'])
            ->withCount('answers')
            ->filter($request->all())
            ->sortBy($request->get('sort'))
            ->paginate(10)
            ->appends($request->all());

        $categories = Category::withCount('questions')->get();
        $tags = Tag::withCount('questions')->get();

        $currentTags = is_array($request->input('tags')) ? json_encode($request->input('tags')) : $request->input('tags', '');

        return view('home', compact('questions', 'categories', 'tags', 'currentTags'));
    }

    public function indextag(Request $request)
    {
        // جلب التصنيفات مع عدد الأسئلة المرتبطة بكل تصنيف
        $categories = Category::withCount('questions')->get();

        // بحث وجلب الوسوم مع الباجينيشن (Pagination)
        $search = $request->input('search');
        $tags = Tag::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%");
        })
            ->withCount('questions')
            ->orderBy('questions_count', 'desc')
            ->paginate(24);

        return view('tags&categories.index', compact('categories', 'tags', 'search'));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    /** @use HasFactory<\Database\Factories\QuestionFactory> */
    use HasFactory;

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'user_id'
    ];

    public function answers()
    {
        // التصحيح: المفتاح الأجنبي في answers هو question_id، والمفتاح المحلي في questions هو id
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'question_tag', 'question_id', 'tag_id');
    }

    // علاقة التعليقات
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // علاقة البلاغات
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['category'] ?? null, function ($q, $category) {
            $q->whereHas('category', fn($sub) => $sub->where('name', $category)->orWhere('id', $category));
        });

        $query->when($filters['tags'] ?? $filters['tag'] ?? null, function ($q, $tagsInput) {
            $tagsData = is_string($tagsInput) ? json_decode($tagsInput, true) : $tagsInput;
            if (!is_array($tagsData)) return;

            $tagValues = array_filter(array_map(fn($item) => is_array($item) ? ($item['id'] ?? $item['value'] ?? null) : $item, $tagsData));

            if (!empty($tagValues)) {
                $q->whereHas('tags', fn($sub) => $sub->whereIn('tags.tag_id', $tagValues)->orWhereIn('tags.name', $tagValues));
            }
        });

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(fn($sub) => $sub->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        });
    }

    public function scopeSortBy($query, $sort)
    {
        match ($sort) {
            'popular' => $query->orderBy('views_count', 'desc'),
            'unanswered' => $query->doesntHave('answers')->latest(),
            default => $query->latest(),
        };
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    // حساب صافي الأصوات (+1 للأعلى و -1 للأسفل)
    public function getNetVotesAttribute()
    {
        $upVotes = $this->votes()->where('vote_type', 'up')->count();
        $downVotes = $this->votes()->where('vote_type', 'down')->count();
        return $upVotes - $downVotes;
    }

    public function getRouteKeyName()
{
    return 'id'; // إجبار لاراول على استخدام عمود id للبحث في الروابط
}
}
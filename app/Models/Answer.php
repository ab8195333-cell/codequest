<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // تحديد أن المفتاح الأساسي هو id وليس answer_id

    protected $fillable = [
        'question_id',
        'user_id',
        'answer_text',
        'is_accepted',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    // حساب صافي الأصوات للإجابة (+1 للأعلى و -1 للأسفل)
    public function getNetVotesAttribute()
    {
        $upVotes = $this->votes()->where('vote_type', 'up')->count();
        $downVotes = $this->votes()->where('vote_type', 'down')->count();
        return $upVotes - $downVotes;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /** @use HasFactory<\Database\Factories\AnswerFactory> */
    use HasFactory;

    protected $primaryKey = 'answer_id';
    protected $fillable = [
        'answer_text',
        'question_id',
        'is_accepted',
        'user_id'
    ];
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
public function votes()
{
    return $this->morphMany(Vote::class, 'votable');
}

public function getNetVotesAttribute()
{
    $upVotes = $this->votes()->where('vote_type', 'up')->count();
    $downVotes = $this->votes()->where('vote_type', 'down')->count();
    return $upVotes - $downVotes;
}
}

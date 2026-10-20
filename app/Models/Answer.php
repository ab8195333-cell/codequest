<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $table = 'answers';
    protected $primaryKey = 'id';

    public function getRouteKeyName()
    {
        return 'id';
    }

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

    public function getNetVotesAttribute()
    {
        $upVotes = $this->votes()->where('vote_type', 'up')->count();
        $downVotes = $this->votes()->where('vote_type', 'down')->count();
        return $upVotes - $downVotes;
    }
}
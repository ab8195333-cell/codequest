<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $primaryKey = 'vote_id';
    protected $fillable = ['user_id', 'votable_id', 'votable_type', 'vote_type'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function votable()
    {
        return $this->morphTo();
    }
}

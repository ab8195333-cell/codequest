<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $primaryKey = 'id'; // لأن المفتاح الرئيسي ليس id في مخططك

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_tag', 'tag_id', 'question_id');
    }
}

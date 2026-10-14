<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type',
        'reason',
        'status',
    ];

    // علاقة Polymorphic للعنصر المُبلاغ عنه
    public function reportable()
    {
        return $this->morphTo()->withDefault([
            'title' => 'محتوى محذوف أو غير متوفر',
            'content' => 'محتوى محذوف أو غير متوفر',
            'body' => 'محتوى محذوف أو غير متوفر',
        ]);
    }

    // علاقة صاحب البلاغ
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

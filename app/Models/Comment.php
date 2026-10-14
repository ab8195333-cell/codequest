<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type'
    ];
    // تعطيل التحديث التلقائي لحقل updated_at غير الموجود بالجدول
    const UPDATED_AT = null;
    public function user()
{
    return $this->belongsTo(User::class);
}

public function commentable()
{
    return $this->morphTo();
}

public function reports()
{
    return $this->morphMany(Report::class, 'reportable');
}

public static function createWithNotification(Model $model, string $content, string $type): self
    {
        // 1. إنشاء التعليق
        $comment = $model->comments()->create([
            'user_id' => auth()->id(),
            'content' => $content,
        ]);

        // 2. إرسال الإشعار لصاحب الكيان (بشرط ألا يكون هو صاحب التعليق نفسه)
        if ($model->user_id !== auth()->id()) {
            $typeInArabic = ($type === 'question') ? 'سؤالك' : 'إجابتك';
            $message = 'قام ' . auth()->user()->name . ' بالتعليق على ' . $typeInArabic . '.';

            Notification::send(
                $model->user_id,
                $message,
                'comment',
                $model->getKey(),
                get_class($model)
            );
        }
        return $comment;
    }

}


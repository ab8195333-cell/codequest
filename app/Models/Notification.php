<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\GenericNotification;



class Notification extends Model
{
    use HasFactory;

    protected $primaryKey = 'notification_id';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'notifiable_id',
        'notifiable_type',
        'is_read',
        'created_at',
    ];

    // تحويل الحقل تلقائياً إلى كائن تاريخ Carbon
    protected $casts = [
        'created_at' => 'datetime',
        'is_read' => 'boolean',
    ];
    public static function send($userId, $message, $type = 'info', $notifiableId = null, $notifiableType = null)
    {
        // 1. منع إرسال إشعار للمستخدم إذا كان هو صاحب الإجراء نفسه
        if (auth()->check() && auth()->id() === (int)$userId) {
            return null;
        }

        // 2. حفظ الإشعار في جدول الإشعارات المخصص بالداتابيز
        $notification = self::create([
            'user_id'         => $userId,
            'message'         => $message,
            'type'            => $type,
            'notifiable_id'   => $notifiableId,
            'notifiable_type' => $notifiableType,
            'is_read'         => false,
            'created_at'      => now(),
        ]);

        // 3. جلب المستخدم المستهدف لفحص تفضيلات الإيميل
        $user = User::find($userId);

        if ($user && $user->email_notifications && !empty($user->email)) {
            try {
                // تحديد الرابط التلقائي بناءً على نوع المنشور
                $url = null;
                if ($notifiableId && $notifiableType) {
                    // إذا كان الإشعار على سؤال أو إجابة/تعليق يتبع لسؤال
                    if ($notifiableType === \App\Models\Question::class) {
                        $url = '/questions/' . $notifiableId;
                    }
                }

                // تحديد عنوان الإيميل بحسب الـ type
                $titles = [
                    'answer'   => 'إجابة جديدة على سؤالك',
                    'accepted' => 'تم قبول إجابتك!',
                    'comment'  => 'تعليق جديد على مشاركتك',
                    'info'     => 'إشعار جديد في منصة CodeQuest',
                ];
                $subject = $titles[$type] ?? 'إشعار جديد في منصة CodeQuest';

                // إرسال الإشعار عبر البريد
                $user->notify(new GenericNotification(
                    $subject,
                    $message,
                    $url,
                    'عرض المنشور'
                ));

            } catch (\Exception $e) {
                Log::error('فشل إرسال إشعار البريد الإلكتروني: ' . $e->getMessage());
            }
        }

        return $notification;
    }
    public function notifiable()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
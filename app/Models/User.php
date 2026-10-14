<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // 1. استيراد الواجهة هنا

#[Fillable(['name', 'email', 'password', 'provider_name',
    'provider_id', 'reputation_points', 'role', 'email_notifications'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail // 2. تطبيق الواجهة هنا
{
    use Notifiable;

    protected $primaryKey = 'id';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === config('app.super_admin_email');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notifications' => 'boolean',
        ];
    }

    public function questions(): HasMany
    {
        // تأكد من اسم المفتاح الأجنبي (user_id)
        return $this->hasMany(Question::class, 'user_id', 'id');
    }

    /**
     * العلاقة: المستخدم يملك عدة إجابات
     */
    public function answers(): HasMany
    {
        // تأكد من اسم المفتاح الأجنبي (user_id)
        return $this->hasMany(Answer::class, 'user_id', 'id');
    }

    // فحص هل المستخدم مدير
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // فحص هل المستخدم مشرف
    public function isModerator(): bool
    {
        return $this->role === 'Moderator';
    }

    // فحص هل المستخدم لديه صلاحية الإشراف أو الإدارة
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'moderator']);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function reports()
    {
        // ربط البلاغات بناءً على المفتاح id الخاص بجدول المستخدمين
        return $this->hasMany(Report::class, 'user_id', 'id');
    }

    // 1. بلاغات الأسئلة الخاصة بالمستخدم
    public function reportsOnQuestions()
    {
        return $this->hasManyThrough(
            Report::class,
            Question::class,
            'user_id',         // المفتاح الأجنبي في جدول الأسئلة
            'reportable_id',   // المفتاح الأجنبي في جدول البلاغات
            'id',              // المفتاح الرئيسي في جدول المستخدمين
            'question_id'      // المفتاح الرئيسي في جدول الأسئلة
        )->where('reportable_type', Question::class);
    }

    // 2. بلاغات الإجابات الخاصة بالمستخدم
    public function reportsOnAnswers()
    {
        return $this->hasManyThrough(
            Report::class,
            Answer::class,
            'user_id',
            'reportable_id',
            'id',
            'answer_id'
        )->where('reportable_type', Answer::class);
    }

    // 3. بلاغات التعليقات الخاصة بالمستخدم
    public function reportsOnComments()
    {
        return $this->hasManyThrough(
            Report::class,
            Comment::class,
            'user_id',
            'reportable_id',
            'id',
            'comment_id'
        )->where('reportable_type', Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')->orderBy('created_at', 'desc');
    }
    // app/Models/User.php

    /**
     * Accessor للحصول على شارة المستخدم ديناميكياً بناءً على نقاط السمعة
     * الاستخدام في Blade: $user->badge['name'] أو $user->badge['color']
     */
    public function getBadgeAttribute()
    {
        $points = $this->reputation_points ?? 0;

        if ($points >= 500) {
            return [
                'name' => 'خبير',
                'level' => 'Gold',
                'color' => 'warning', // أصفر ذهبي
                'icon' => 'fas fa-award',
                'bg_class' => 'badge-gold-shiny',
            ];
        } elseif ($points >= 200) {
            return [
                'name' => 'متقدم',
                'level' => 'Silver',
                'color' => 'secondary', // فضي
                'icon' => 'fas fa-award',
                'bg_class' => 'badge-silver-shiny',
            ];
        } elseif ($points >= 15) {
            return [
                'name' => 'مساهم',
                'level' => 'Bronze',
                'color' => 'danger', // برونزي/أحمر
                'icon' => 'fas fa-award',
                'bg_class' => 'badge-bronze-shiny',
            ];
        }

        return [
            'name' => 'مبتدئ',
            'level' => 'None',
            'color' => 'light',
            'icon' => 'fas fa-user-tag',
            'bg_class' => 'bg-light text-dark border',
        ];
    }

    // التقييمات التي تلقاها المستخدم على أسئلته
    public function questionVotes()
    {
        return $this->hasManyThrough(Vote::class, Question::class, 'user_id', 'question_id');
    }

    // التقييمات التي تلقاها المستخدم على إجاباته
    public function answerVotes()
    {
        return $this->hasManyThrough(Vote::class, Answer::class, 'user_id', 'answer_id');
    }
}

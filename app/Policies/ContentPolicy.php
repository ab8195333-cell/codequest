<?php

namespace App\Policies;

use App\Models\User;

class ContentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * دالة تحقق عامة تسمح للتعديل بناءً على ملكية المحتوى أو الرتبة
     * تقبل أي موديل (Question, Answer, Comment)
     */
    public function update(User $user, mixed $model): bool
    {
        // 1. السماح فوراً إذا كان المستخدم مدير (admin) أو مشرف (Moderator)
        if (isset($user->role) && in_array($user->role, ['admin', 'Moderator'])) {
            return true;
        }

        // 2. السماح إذا كان المستخدم هو المالك الأصلي للمحتوى (السؤال أو الإجابة أو التعليق)
        return isset($model->user_id) && $user->id === $model->user_id;
    }

    /**
     * دالة تحقق عامة للحذف (يمكنك استخدامها لاحقاً لمنع/سماح الحذف بنفس الشروط)
     */
    public function delete(User $user, mixed $model): bool
    {
        return $this->update($user, $model);
    }
}
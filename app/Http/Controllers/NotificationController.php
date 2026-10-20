<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        if (! $userId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'unread_count' => 0, 'notifications' => []]);
            }

            return redirect()->route('login');
        }

        $query = Notification::where('user_id', $userId)
            ->with('notifiable')
            ->latest();

        if ($request->wantsJson() || $request->ajax()) {
            $query->where('is_read', false)->limit(10);
        }

        $rawNotifications = $query->get();

        $formattedNotifications = $rawNotifications->map(function ($notif) {
            $targetUrl = '#';
            $item = $notif->notifiable;

            if ($item) {
                $targetUrl = match ($notif->notifiable_type) {
                    // استخدام id بدلاً من question_id
                    Question::class => route('questions.show', $item->id),

                    // استخدام id و question_id بالشكل الصحيح للإجابة
                    Answer::class => route('questions.show', $item->question_id).'#answer-'.$item->id,

                    Comment::class => match ($item->commentable_type) {
                        Question::class => route('questions.show', $item->commentable_id),
                        Answer::class => ($answer = Answer::find($item->commentable_id))
                            ? route('questions.show', $answer->question_id).'#answer-'.$answer->id
                            : '#',
                        default => '#',
                    },

                    default => '#',
                };
            }

            $iconData = match ($notif->type) {
                'answer' => ['icon' => 'bi-check-circle-fill', 'color' => 'text-success'],
                'comment' => ['icon' => 'bi-chat-left-dots-fill', 'color' => 'text-primary'],
                'vote' => ['icon' => 'bi-hand-thumbs-up-fill', 'color' => 'text-info'],
                'warning' => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'text-danger'],
                default => ['icon' => 'bi-exclamation-circle-fill', 'color' => 'text-warning'],
            };

            $title = match ($notif->type) {
                'answer' => 'إجابة جديدة',
                'comment' => 'تعليق جديد',
                'warning' => 'تحذير إشرافي',
                default => 'إشعار جديد',
            };

            return [
                'notification_id' => $notif->notification_id,
                'id' => $notif->notification_id,
                'title' => $title,
                'message' => $notif->message,
                'is_read' => $notif->is_read,
                'created_at' => $notif->created_at,
                'time' => $notif->created_at->diffForHumans(),
                'target_url' => $targetUrl,
                'url' => $targetUrl,
                'icon_class' => $iconData['icon'],
                'icon_color' => $iconData['color'],
                'type' => $notif->type,
            ];
        });

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount,
                'notifications' => $formattedNotifications,
            ]);
        }

        return view('notifications', [
            'notifications' => $formattedNotifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        try {
            Notification::where('notification_id', $id)
                ->where('user_id', auth()->id())
                ->update(['is_read' => true]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة');
    }

    public function toggleEmailNotifications(Request $request)
    {
        $user = auth()->user();
        $user->email_notifications = $request->has('email_notifications');
        $user->save();

        return redirect()->back()->with('success', 'تم تحديث تفضيلات الإشعارات بنجاح.');
    }
}
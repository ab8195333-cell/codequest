<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GenericNotification extends Notification
{
    use Queueable;

    public $title;
    public $messageText;
    public $url;
    public $actionText;

    /**
     * بناء الإشعار بمظاهر ديناميكية
     */
    public function __construct(string $title, string $messageText, string $url = null, string $actionText = 'عرض التفاصيل')
    {
        $this->title       = $title;
        $this->messageText = $messageText;
        $this->url         = $url;
        $this->actionText  = $actionText;
    }

    public function via(object $notifiable): array
    {
        $channels = [];

        // التحقق من تفضيل البريد
        if ($notifiable->email_notifications) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject($this->title)
        ->markdown('emails.notifications.generic', [
            'name'        => $notifiable->name,
            'title'       => $this->title,
            'messageText' => $this->messageText,
            'url'         => $this->url,
            'actionText'  => $this->actionText,
        ]);
    }
}
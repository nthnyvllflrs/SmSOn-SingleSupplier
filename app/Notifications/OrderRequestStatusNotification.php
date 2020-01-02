<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderRequestStatusNotification extends Notification
{
    use Queueable;

    public $order_request;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order_request)
    {
        $this->order_request = $order_request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'code' => $this->order_request->code,
            'status' => $this->order_request->status,
        ];
    }
}

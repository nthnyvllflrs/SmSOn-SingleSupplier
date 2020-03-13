<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockCriticalNotification extends Notification
{
    use Queueable;

    public $stock;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($stock)
    {
        $this->stock = $stock;
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

    public function toArray($notifiable)
    {
        return [
            'code' => $this->stock->product->code,
            'status' => $this->stock->stock_status
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class AdminDailyReportNotification
 * @package App\Notifications
 */
class AdminDailyReportNotification extends Notification
{
    use Queueable;
    /**
     * @var
     */
    private $dangerStock;
    /**
     * @var
     */
    private $transactions;

    /**
     * Create a new notification instance.
     *
     * @param $dangerStock
     * @param $transactions
     */
    public function __construct($dangerStock, $transactions)
    {
        $this->dangerStock  = $dangerStock;
        $this->transactions = $transactions;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('emails.adminDailyReportEmail', [
                'dangerStock'  => $this->dangerStock,
                'transactions' => $this->transactions,
            ])
            ->subject('Daily report')
            ->from('no-reply@admin.com');
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
            //
        ];
    }
}

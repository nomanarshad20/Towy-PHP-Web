<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendReceiptNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($bookingRecord)
    {
        $this->bookingRecord = $bookingRecord;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Receipt of Tow Service')
            ->line('Receipt of the Tow Service with Booking ID: '.$this->bookingRecord->booking_unique_id)
            ->line(new HtmlString('Total Distance: <strong>'.$this->bookingRecord->total_distance.'</strong> '))
            ->line(new HtmlString('Waiting Time: <strong>'.$this->bookingRecord->bookingDetail->driver_wating_time.'</strong> '))
            ->line(new HtmlString('Total Ride Time: <strong>'.$this->bookingRecord->bookingDetail->total_ride_minutes.'</strong> '))
            ->line(new HtmlString('Total Fare: <strong>'.$this->bookingRecord->actual_fare.'</strong> '));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

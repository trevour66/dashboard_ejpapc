<?php

namespace App\Notifications;

use Illuminate\Support\HtmlString;
use App\Models\Incentives;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Incentives_gift_transfer;


class newIndividualGift extends Notification
{
    use Queueable;

    public $incentiveGiftData = null;
    public $incentiveData = null;
    public $Incentives_gift_transfer = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( Incentives_gift_transfer $Incentives_gift_transfer)
    {
        $this->Incentives_gift_transfer = $Incentives_gift_transfer;
        $this->incentiveGiftData = $this->Incentives_gift_transfer->Incentive_gift()->first();
        
        $this->incentiveData = Incentives::find($this->incentiveGiftData->incentive_id);
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // print_r(json_encode($notifiable));
        // die();
        return new \App\Mail\IndividiualgiftNewMail($notifiable, null, null);

        // return (new MailMessage)
        //             ->subject("Congratulations! You've Received a Special Recognition at E-Justice Project 🎉")
        //             ->greeting('Hurray!')
        //             ->line("I am thrilled to inform you that your hard work and dedication have not gone unnoticed. A special recognition has been awarded to you for your exceptional contributions to the team.")
        //             ->line(new HtmlString("<p>As a token of appreciation, a <b>".$this->incentiveData->name."</b> has been sent to you. Your efforts have made a significant impact, and we want to celebrate your success.</p>"))
        //             ->line("Once again, congratulations on this well-deserved recognition. We are proud to have you as part of the E-Justice Project team");
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

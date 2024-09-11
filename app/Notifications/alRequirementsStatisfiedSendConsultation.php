<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class alRequirementsStatisfiedSendConsultation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $lead_name = $notifiable->lead_name ?? '';
        $consultationLink = "https://calendly.com/d/dqn-z3k-p4d/consultation-ejp";
        
        $message = new MailMessage();

        $message->subject("Schedule Your Consultation With EJPAPC - ".$lead_name);
        $message->greeting("Hi there!");

        $message->line("You have successfully fullfil all the requirements needed in order to process along with your case. I am reaching out to facilitate the scheduling of our upcoming consultation. To make the process convenient for you. Please use the link below to book a consultation.");
        
        $message->action("Consultation Schedule Link", $consultationLink);

        $message->line("Feel free to select a time slot that best fits your schedule.");
        $message->line("I look forward to the opportunity to discuss your case and provide you with valuable legal insights. Should you have any questions or encounter any issues with the scheduling system, don't hesitate to reach out to me directly.");
        
        return $message;
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

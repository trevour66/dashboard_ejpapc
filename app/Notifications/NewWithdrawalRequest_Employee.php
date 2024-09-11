<?php

namespace App\Notifications;

use Illuminate\Support\HtmlString;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Withdrawal_requests;

class NewWithdrawalRequest_Employee extends Notification
{
    use Queueable;

    public $Withdrawal_requests = null;
    public $sender = null;
    public $for_attorney = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Withdrawal_requests $new_W_Request)
    {
        $this->Withdrawal_requests =  $new_W_Request;
        
        // print_r(json_encode($this->Withdrawal_requests));
        // die();

        $this->sender =  $this->Withdrawal_requests->employee()
            ->join('users', 'employees.user_id', 'users.user_id' )            
            ->first();
        $this->for_attorney =  false;
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

        $withdrawal_requests_link = route('withdrawal_requests.show', ["withdrawal_request_id" => $this->Withdrawal_requests->withdrawal_request_link_id]);

        return (new MailMessage)
                    ->subject("Withdrawal Request Submitted")
                ->line(new HtmlString("I hope this message finds you well. You have submitted a withdrawal request for <b>USD ".$this->Withdrawal_requests->amount."</b>. "))
                ->line("Click the button view that status of your request.")
                ->action('Withdrawal', $withdrawal_requests_link)
                ->line("Thanks");
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

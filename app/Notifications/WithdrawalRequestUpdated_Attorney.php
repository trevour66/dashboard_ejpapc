<?php

namespace App\Notifications;

use Illuminate\Support\HtmlString;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Withdrawal_requests;


class WithdrawalRequestUpdated_Attorney extends Notification
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
    public function __construct(Withdrawal_requests $updated_W_Request)
    {
        //
        $this->Withdrawal_requests =  $updated_W_Request;
        $this->sender =  $this->Withdrawal_requests->employee()
            ->join('users', 'employees.user_id', 'users.user_id' )            
            ->first();
        $this->for_attorney =  true;
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
        // print_r(json_encode([
        //     'Atorney',
        //     $this->Withdrawal_requests,
        //     $this->sender,
        // ]));

        // die();


        $withdrawal_requests_link = route('withdrawal_requests.show', ["withdrawal_request_id" => $this->Withdrawal_requests->withdrawal_request_link_id]);

        return (new MailMessage)
                ->subject("Withdrawal Request Status Update")
                ->line(new HtmlString("I hope this message finds you well. There has been an update regarding the withdrawal request submitted by <b>".$this->sender->name."</b> for <b>USD ".$this->Withdrawal_requests->amount."</b>. The current status is as follows:"))

                ->line(new HtmlString("<b>Status:</b> ".$this->Withdrawal_requests->status.""))                

                ->line("Click the button review this request ")
                ->action('Withdrawal', $withdrawal_requests_link);
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

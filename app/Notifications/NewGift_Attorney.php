<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

use App\Models\Incentive_gift;

class NewGift_Attorney extends Notification
{
    use Queueable;

    public $Incentive_gift = null;
    public $recipients_name = [];
    public $for_sender = null;
    public $purpose = "GIFT_SENDING";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Incentive_gift $Incentive_gift)
    {
        //
        $this->Incentive_gift =  $Incentive_gift;
        $this->recipients_name = $this->Incentive_gift->incentives_gift_transfer()
            ->join('employees', 'to_employee_id', 'employees.employee_id' )
            ->join('users', 'employees.user_id', 'users.user_id' )
            ->select('users.name AS recipient_name')
            ->get();
        $this->for_sender =  true;
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
        //     $this->Incentive_gift,
        //     $this->recipients_name,
        //     // $this->incentiveGiftData,
        //     // $this->incentiveData
        // ]));

        // die();

        $message = new MailMessage();
        $message = $message
            ->subject("Your Generosity at Work Deserves a Reward! 🌟")
            ->line("Thank you for your outstanding generosity in recognizing the exceptional work of your employees. Your thoughtful gesture in providing an incentive is a testament to the collaborative spirit we value at E-Justice Project.");

        if(count($this->recipients_name)){
            $message = $message->line("Below are the recipient(s) of your kind gesture"); 
            
            foreach ($this->recipients_name as $key => $value) {
                $message = $message->line(new HtmlString("<p><b>".$value->recipient_name ?? ""."</b></p>"));
            }
        }


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

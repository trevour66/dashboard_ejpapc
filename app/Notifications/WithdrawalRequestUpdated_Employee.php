<?php

namespace App\Notifications;

use Illuminate\Support\HtmlString;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Withdrawal_requests;


class WithdrawalRequestUpdated_Employee extends Notification
{
    use Queueable;

    
    public $Withdrawal_requests = null;
    public $sender = null;
    public $for_attorney = null;

    public $allEMailTemplate= [
        'success' => [
            [
                'subject' => "🌟 Hooray! Your Withdrawal is Complete! 🎉",
                'message' => "Break out the confetti because your withdrawal is officially a success! Your funds are on their way to you. Treat yourself—it's time to celebrate your hard work!"
            ],
            [
                'subject' => "💰 Cash Alert: Your Withdrawal Has Been Processed! 💸",
                'message' => "Great news! Your withdrawal request has been processed successfully. Your cash is en route to your account. Go ahead, check your balance, and enjoy your well-deserved reward!"
            ],
            [
                'subject' => "🌈 Bright News: Your Withdrawal is a Success! 💰",
                'message' => "We're thrilled to share the bright news with you—your withdrawal has been successfully processed! Your dedication shines, and so does your reward. Enjoy!"
            ],
            [
                'subject' => "🚀 Blast Off: Your Successful Withdrawal is Ready! 💸",
                'message' => "Get ready for liftoff! Your successful withdrawal is ready to rocket into your account. Your dedication is truly out of this world."
            ],
            [
                'subject' => "💼 Job Well Done! Your Withdrawal is Complete! 💰",
                'message' => "Your dedication speaks volumes, and so does your successful withdrawal! Your funds are ready for action. Job well done!"
            ],
            [
                'subject' => "🌈 Rainbow of Rewards: Your Withdrawal is a Success! 🎁",
                'message' => "Your hard work has created a rainbow of rewards! Your withdrawal is a success, and your funds are ready to brighten your day. Enjoy every color!"
            ],
        ],
        'failed' => [
            [
                'subject' => "❌ Withdrawal Denied - Action Needed! 🚫",
                'message' => "Unfortunately, your withdrawal request has been denied. "
            ],
            [
                'subject' => "❌ Withdrawal Denied - Action Needed! 🚫",
                'message' => "We regret to inform you that your withdrawal request has encountered an issue and has been rejected. "
            ],
            [
                'subject' => "🛑 Withdrawal Alert - Your Request Has Been Declined! 🚷",
                'message' => "We're sorry to inform you that your recent withdrawal request has been declined. Please check your account information, and if you require further assistance, contact us."
            ],
            [
                'subject' => "🔒 Important: Your Withdrawal Request Was Not Approved",
                'message' => "We regret to inform you that your withdrawal request has not been approved. Review the provided information, and if needed, update your account details. For any questions, please contact us"
            ],
        ],
        
    ];

    public $selectedEmailData = null;

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
        $this->for_attorney =  false;

        
        $mailType = '';

        if($this->Withdrawal_requests->status == "success"){
            $mailType = 'success';

        }else {
            $mailType = 'failed';

        }

        if($mailType){
            $emailempByType = $this->allEMailTemplate[$mailType];

            $this->selectedEmailData = $emailempByType[rand(0, count($emailempByType) - 1)];
        }
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
                ->subject($this->selectedEmailData['subject'])
                ->line($this->selectedEmailData['message'])
                
                ->line(new HtmlString("<b>Transaction details</b>"))

                ->line(new HtmlString("<b>Amount:</b> USD".$this->Withdrawal_requests->amount.""))                
                ->line(new HtmlString("<b>Status:</b> ".$this->Withdrawal_requests->status.""))                

                ->line("Click the button review this request ")
                ->action('Withdrawal', $withdrawal_requests_link)
                ->line("If you have any questions or concerns about this update, please feel free to reach out to me. Your understanding and cooperation are appreciated.");
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

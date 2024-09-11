<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

use App\Models\WithNoDatabaseEntry\AGavelSubmission;

class allRequirementsNotStatisfied extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public AGavelSubmission $aGavelSubmission,)
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
        
        // logger(print_r($notifiable, true));

        $required = $notifiable->required ?? [];
        $lead_name = $notifiable->lead_name ?? '';
        $email_link_id = $notifiable->email_link_id ?? false;

        $isThisAttachedToAnUpdateEvent = $notifiable->isThisAttachedToAnUpdateEvent ?? false;

        // logger(print_r("notifiable", true));
        // logger(print_r($notifiable, true));
        // die();

        
        if($email_link_id){
            $uploadURL = route('requirements.index', [
                "email_link_id" => $email_link_id
            ]);

            $beginTimeline = "https://ejpapc.com/timeline/create-timeline.php";
            
            $message = new MailMessage();

            $message->subject('EJPAPC: Attention Needed');
    
            $message->greeting("Dear ".$lead_name);

            if($isThisAttachedToAnUpdateEvent){                
                $message->line(new HtmlString('You made some update to the requirements needed before you can proceed to booking a consultatiion. However, we noticed that some requirements are not still met. To proceed with the consultation process, we kindly request you to fulfill all requirements:'));
            }else {
                $message->line(new HtmlString('We appreciate your interest in our services and the recent submission you made for a consultation booking. However, after reviewing your submission, we noticed that you have not met some requirements. To proceed with the consultation process, we kindly request you to fulfill all requirements:'));                
            }
            
            $message->line(new HtmlString('<br />'));
    
            if($required['proof_of_employment'] ?? false){
                $message->line(new HtmlString('❌  <b>Proof of Employment (REQUIRED)</b>'));                        
            }else{
                $message->line(new HtmlString('✅  <b>Proof of Employment (UPLOADED)</b>'));
            }

            if($required['admin_rems'] ?? false){
                $message->line(new HtmlString('❌  <b>Admin Remedy Document (REQUIRED)</b>'));
            }else{
                $message->line(new HtmlString('✅  <b>Admin Remedy Document (UPLOADED)</b>'));
            }

            if($required['timeline'] ?? false){
                $message->line(new HtmlString('❌  <b>Timeline (NOT STARTED)</b>'));
            }else{
                $message->line(new HtmlString('✅  <b>Timeline (COMPLETED)</b>'));
            }


            $message->line(new HtmlString('<br />'));


            if($required['proof_of_employment'] ?? false){
                $message->line(new HtmlString('<b>Proof of Employment -</b> You indicated on your questionnaire that you could provide proof of employment within 3 days. We are yet to receive your proof of employment such as a paystub, W2, or offer letter that includes the name and address of your employer. Please upload your proof of employment <a href="'.$uploadURL.'">HERE</a>'));            
            }
    
            if($required['admin_rems'] ?? false){        
               $message->line(new HtmlString('<b>Admin. Remedies -</b> You indicated on your questionnaire that you had filed a complaint with an agency(s). In order to move forward with your claims we need to receive a document regarding your complaint that includes a case number. Please upload <a href="'.$uploadURL.'">HERE</a>'));          
          
            }
    
            if($required['timeline'] ?? false){
                $message->line(new HtmlString('<b>Timeline -</b> A timeline serves as a chronicle, offering a summary of all the significant events leading up to your case. It provides our attorneys valuable contexts that aids in understanding the intricacies of your case.  Kindly complete your timeline through the following link: <a href="'.$beginTimeline.'">Begin your timeline</a>'));            
            }
            
            $message->line(new HtmlString('<br />'));
            $message->line('Completing these requirements is crucial to ensure a smooth consultation process. Once all the necessary information is submitted, we will promptly review your application and schedule a consultation at your convenience.');

            // $message->line('If you encounter any issues or have questions regarding the submission process, please do not hesitate to contact our support team at support@email.com.');
            
            $message->line('Thank you for your cooperation. We look forward to assisting you further.');
        
            return $message;
        }
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

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Incentives_gift_transfer;
use App\Models\Employee;
use App\Models\Incentive_gift;
use App\Models\User;

class IndividiualgiftNewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipentsEmail = null;

    public $allEmployeesGettingGift = [ ];
    public $allGiftImages = [ 
        "coffee" => [
            "https://incentive.ejpapc.com/images/coffee/1.jpg",
            "https://incentive.ejpapc.com/images/coffee/2.jpg"
        ],
        "cupcake" => [
            "https://incentive.ejpapc.com/images/cupcake/1.jpg",
            "https://incentive.ejpapc.com/images/cupcake/2.jpg",
            "https://incentive.ejpapc.com/images/cupcake/3.jpg",
            
        ],
        "flower" => [
            "https://incentive.ejpapc.com/images/flower/1.jpg",
            "https://incentive.ejpapc.com/images/flower/2.jpg",
            "https://incentive.ejpapc.com/images/flower/3.jpg",
            "https://incentive.ejpapc.com/images/flower/4.jpg",
            "https://incentive.ejpapc.com/images/flower/5.jpg",
            
        ],
        "pizza" => [
            "https://incentive.ejpapc.com/images/pizza/1.jpg",                        
            "https://incentive.ejpapc.com/images/pizza/2.jpg",                        
            "https://incentive.ejpapc.com/images/pizza/3.jpg",                        
            "https://incentive.ejpapc.com/images/pizza/4.jpg",                        
            "https://incentive.ejpapc.com/images/pizza/5.jpg",                        
        ],
        "silver_pen" => [
            "https://incentive.ejpapc.com/images/pen/2.jpg",            
        ],        
    ];

    public $allTitleStatements = [
        "coffee" => [
            "Brewed Brilliance - A Fresh Cup of Thanks Awaits You! 🎁",
            "Caffeine Cheers - Your Work Deserves a Java Boost! 🎁" ,
            "Espresso Elation - A Cup of Motivation Just for You!  🎁",
            "Mug Magic - Unlock Your Coffee Incentive Today! 🎁"
        ],
        
        "cupcake" => [
            "Sweet Success Unwrapped - 🧁 A Cupcake Celebration for You!",
            "Bite-Sized Appreciation - 🧁 Indulge in a Cupcake Treat!",
            "Frosting Fiesta - 🧁 Your Hard Work Deserves a Sweet Reward!",
            "Sprinkled Joy - 🧁 Cupcakes to Celebrate Your Wins!"
        ],
        
        "flower" => [
            "Blossoms of Gratitude - 💐 A Floral Thank You for You!",
            "Petals and Praise - 💐 Your Dedication Blooms Brightly!",
            "Flower Power - 💐 A Bouquet of Thanks Just for You!",
            "Botanical Cheers - 💐 Celebrate Your Achievements with Flowers!"
        ],
        
        "silver_pen" => [
            "Inkredible Work! - A Shiny Silver Pen to Express Our Thanks! 🎁",
            "Write On! - Your Achievements Deserve a Silver Signature! 🎁",
            "Pen Perfection - A Token of Appreciation in Silver Ink! 🎁",
            "Silver Lining - A Pen for Your Remarkable Contributions! 🎁"
        ],
        
        "pizza" => [
            "Pizza Party Time! - 🍕 Savoring Success, One Slice at a Time!",
            "Slice of Victory - 🍕 Your Hard Work Earns a Pizza Reward!",
            "Cheesy Cheers - 🍕 Celebrate Your Wins with a Pizza Feast!",
            "Pizza Pleasantries - 🍕 A Delicious Thank You Just for You!"
        ]
    ];


    public $incentiveGiftGeneralData_name = null;
    public $incentiveGiftGeneralData_icon_name = null;
    public $incentiveGiftGeneralData_amount_per_item = null;
    public $incentiveGiftGeneralData_quantity = null;
    public $incentiveGiftGeneralData_total_amount = null;
    public $incentiveGiftGeneralData_url = '';
    
    public $giftImageToUse = '';
    public $giftHollaToUse = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Incentives_gift_transfer $Incentives_gift_transfer, $allRecipientData, Incentive_gift|null $incentiveGift)
    {        

        //             print_r(json_encode($allRecipientData));
        // die();

        $to_employee = Employee::find($Incentives_gift_transfer->to_employee_id);
        $user_id = $to_employee->user_id;
        
        $user = User::find($user_id);    
        
        $this->recipentsEmail = $user->email;

        array_push($this->allEmployeesGettingGift, [
            "isRecipentOfMail" => true,
            "email" => $user->email,
            "amount" => $Incentives_gift_transfer->amount
        ]);   

        if(count($allRecipientData) > 0){
            for ($i=0; $i < count($allRecipientData) ; $i++) {
                if($this->recipentsEmail === $allRecipientData[$i]->user_to_email){
                    continue;
                } 
                array_push($this->allEmployeesGettingGift, [
                    "isRecipentOfMail" => false,
                    "email" => $allRecipientData[$i]->user_to_email,
                    "amount" => $allRecipientData[$i]->incentives_gift_transfer_amount_per_employee
                ]);
            }
        }
        

        $this->incentiveGiftGeneralData_name = $incentiveGift->incentive->name ?? '';
        $this->incentiveGiftGeneralData_icon_name = $incentiveGift->incentive->icon_name ?? '';
        $this->incentiveGiftGeneralData_amount_per_item = $incentiveGift->incentive->amount_per_item ?? '';
        $this->incentiveGiftGeneralData_quantity = $incentiveGift->gift_quantity ?? '';
        $this->incentiveGiftGeneralData_total_amount = $incentiveGift->amount ?? '';
            
        //     print_r(json_encode($allRecipientData));
        // die();

        $this->giftImageToUse = $this->allGiftImages[$this->incentiveGiftGeneralData_icon_name][rand(0, count($this->allGiftImages[$this->incentiveGiftGeneralData_icon_name]) - 1)];
        
        $this->giftHollaToUse = $this->allTitleStatements[$this->incentiveGiftGeneralData_icon_name][rand(0, count($this->allGiftImages[$this->incentiveGiftGeneralData_icon_name]) - 1)];

        if($Incentives_gift_transfer->incentives_gift_type_id){
            $this->incentiveGiftGeneralData_url = "https://incentive.ejpapc.com/incentive_gift/$Incentives_gift_transfer->incentives_gift_type_id";            
        }

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: "Congratulations! You've Received a Special Recognition at E-Justice Project 🎉",
            to: [$this->recipentsEmail]
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            html: 'vendor.custom.gifts.giftMail',
            text: 'vendor.custom.gifts.giftMail_plain_text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }

}

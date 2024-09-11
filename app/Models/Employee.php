<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Notifications\NewGift_Attorney;

class Employee extends Model
{
    use HasFactory, Notifiable;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_only_users';

    protected $primaryKey = 'employee_id';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "user_id");
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balance',
        'has_elevated_permission',
        'employee_public_ref',
        'status'
    ];

    /**
     * Get the roles associated with the employee.
     */
    public function role(): HasMany {
        return $this->hasMany(Role::class, 'employee_id');
    }

    public function routeNotificationForMail(
        NewGift_Attorney $notification): array|string
    {   
    
        $forSender = $notification->for_sender;
        $purpose = $notification->purpose;
        
        
        if($forSender && $purpose === 'GIFT_SENDING'){

            $user =  $this->user()->first();
        
            // print_r(json_encode($user));
            // die();
            
            // Return email address only...
            return $user->email;
     
 
        }

    }

}

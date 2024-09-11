<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_employee'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the employee associated with the user.
     */
    public function employee(): HasOne {
        return $this->hasOne(Employee::class, 'user_id');
    }

     /**
     * Get the employee associated with the user.
     */
    public function invite(): HasMany {
        return $this->hasMany(Invite::class, 'invite_sent_by');
    }

    /**
     * Get the actionStepCredRequest for the blog post.
     */
    public function actionStepCred(): HasOne
    {
        return $this->hasOne(ActionStep_Asset::class, 'user_id');
    }

    /**
     * Get the actionStepCredRequest for the blog post.
     */
    public function actionStepCredRequest(): HasOne
    {
        return $this->hasOne(actionStepCredRequest::class, 'request_to_be_fulfiled_by');
    }
}

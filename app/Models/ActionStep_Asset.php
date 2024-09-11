<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionStep_Asset extends Model
{
    use HasFactory;

    protected $table  = "action_step_assets";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'token_type',
        'access_token',
        'expires_in',
        'refresh_token',    
        'orgkey',
        'api_endpoint',            
        'client_id',
        'client_secret'
    ];
}

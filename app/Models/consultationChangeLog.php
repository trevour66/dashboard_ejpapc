<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class consultationChangeLog extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CCL_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'CCL_outcome',
        'CCL_schedule_platform',
        'CCL_action_step_matter_id',

        'CCL_schedule_date',
        'CCL_date_conducted',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class intakeChangeLog extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'ICL_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ICL_intake_code',
        'ICL_action_step_matter_id',
        'ICL_status',
        'ICL_completed_by',
        'ICL_deposition',
        'ICL_schedule_platform',
        'ICL_schedule_date',
        'ICL_completed_date',
    ];
}

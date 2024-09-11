<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class currentStepChangeLog extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CSCL_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'CSCL_previous_step',
        'CSCL_current_step',
        'CSCL_action_step_matter_id',

        'CSCL_change_date_time',
        'CSCL_change_status',

    ];

    public function matter(): BelongsTo
    {
        return $this->belongsTo(matter::class, 'CSCL_action_step_matter_id', 'matter_id');
    }
}

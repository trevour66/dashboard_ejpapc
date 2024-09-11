<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class leadStatusChangeLog extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'LSCL_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'LSCL_previous_status',
        'LSCL_current_status',
        'LSCL_action_step_lead_id',
        'LSCL_action_step_matter_id',

        'LSCL_change_date_time',
        'LSCL_change_status',

    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(lead::class, 'LSCL_action_step_lead_id', 'lead_id');
    }

    public function matter(): BelongsTo
    {
        return $this->belongsTo(matter::class, 'LSCL_action_step_matter_id', 'matter_id');
    }
}

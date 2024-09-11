<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class leadOverallStatusChangeLog extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'LOSCL_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'LOSCL_prev_ov_status',
        'LOSCL_active_ov_status',
        'LOSCL_as_lead_id',
        'LOSCL_as_matter_id',

        'LOSCL_change_date_time',
        'LOSCL_change_status',

    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(lead::class, 'LOSCL_as_lead_id', 'lead_id');
    }

    public function matter(): BelongsTo
    {
        return $this->belongsTo(matter::class, 'LOSCL_as_matter_id', 'matter_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lead extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'lead_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lead_name',
        'lead_email',
        'lead_zipcode',
        'lead_current_overall_lead_status',
        'lead_source',
        'lead_current_status',

        // Datetime values
        'lead_date_created',
    ];

    public function currentLeadStatus()
    {
        return $this->hasOne(leadStatus::class, 'LSt_id', 'lead_current_status');
    }

    public function currentLeadOverallStatus()
    {
        return $this->hasOne(leadOverallStatus::class, 'LOS_id', 'lead_current_overall_lead_status');
    }

    public function matters()
    {
        return $this->hasMany(matter::class, null, 'matter_lead');
    }
}

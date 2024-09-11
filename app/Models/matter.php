<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class matter extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'matter_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // String values
        'matter_actionstep_id',
        'matter_current_name',
        'matter_demand_letter_type',
        'matter_last_filenote',
        'matter_next_task',

        // Datetime values
        'matter_date_created',
        'matter_tentv_settlement_date',
        'matter_settlement_agreement_signed',
        'matter_settlement_funds_expected_date',
        'matter_settlement_funds_received_date',
        'matter_last_activity',
        'matter_next_task_due_date',
        'matter_close_date',
        'matter_current_status_started_date',

        // Decimal values
        'matter_atty_fees',
        'matter_current_offer',

        // foreign keys
        'matter_current_step',
        'matter_current_matter_type',
        'matter_current_status',
        'matter_assigned_to',
        'matter_lead'
    ];

    // If using $casts property (recommended)
    protected $casts = [
        'matter_date_created' => 'date',
        'matter_atty_fees' => 'double',
        'matter_current_offer' => 'double',

    ];

    // $table->foreignId('matter_current_step')->constrained('a_s__steps', 'step_id');
    public function currentStep()
    {
        return $this->hasOne(AS_Step::class, 'step_id', 'matter_current_step');
    }

    // $table->foreignId('matter_current_matter_type')->constrained('matter_types', 'MT_id');
    public function currentMatterType()
    {
        return $this->hasOne(matterType::class, 'MT_id', 'matter_current_matter_type');
    }

    // $table->foreignId('matter_current_status')->constrained('matter_statuses', 'MSt_id');
    public function currentMatterStatus()
    {
        return $this->hasOne(matterStatus::class, 'MSt_id', 'matter_current_status');
    }

    // $table->foreignId('matter_assigned_to')->constrained('action_step_attorneys_legal_assistants', 'ASALA_id');
    public function currentMatterAttorney()
    {
        return $this->hasOne(actionStep_attorneys_legalAssistant::class, 'ASALA_id', 'matter_assigned_to');
    }

    // $table->foreignId('matter_lead')->nullable()->constrained('leads', 'lead_id');
    public function lead()
    {
        return $this->hasOne(lead::class, 'lead_id', 'matter_lead');
    }

    public function matterIntakes()
    {
        return $this->hasMany(intakeChangeLog::class, 'ICL_action_step_matter_id');
    }

    public function matterConsultations()
    {
        return $this->hasMany(consultationChangeLog::class, 'CCL_action_step_matter_id');
    }
}

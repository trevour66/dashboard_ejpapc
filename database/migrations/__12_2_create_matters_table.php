<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matters', function (Blueprint $table) {
            $table->id('matter_id');

            $table->string('matter_actionstep_id')->unique();
            $table->string('matter_current_name')->nullable();
            $table->string('matter_demand_letter_type')->nullable();
            $table->text('matter_last_filenote')->nullable();
            $table->string('matter_next_task')->nullable();

            $table->foreignId('matter_current_step')->nullable()->constrained('a_s__steps', 'step_id');
            $table->foreignId('matter_current_matter_type')->nullable()->constrained('matter_types', 'MT_id');
            $table->foreignId('matter_current_status')->nullable()->constrained('matter_statuses', 'MSt_id');
            $table->foreignId('matter_assigned_to')->nullable()->constrained('action_step_attorneys_legal_assistants', 'ASALA_id');
            $table->foreignId('matter_lead')->nullable()->constrained('leads', 'lead_id');

            $table->dateTime('matter_date_created')->nullable();
            $table->dateTime('matter_tentv_settlement_date')->nullable();
            $table->dateTime('matter_settlement_agreement_signed')->nullable();
            $table->dateTime('matter_settlement_funds_expected_date')->nullable();
            $table->dateTime('matter_settlement_funds_received_date')->nullable();
            $table->dateTime('matter_last_activity')->nullable();
            $table->dateTime('matter_next_task_due_date')->nullable();
            $table->dateTime('matter_close_date')->nullable();
            $table->dateTime('matter_current_status_started_date')->nullable();

            $table->decimal('matter_atty_fees')->nullable();
            $table->decimal('matter_current_offer')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matters');
    }
};

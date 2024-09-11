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
        Schema::create('intake_change_logs', function (Blueprint $table) {
            $table->id('ICL_id');
            $table->string('ICL_intake_code')->nullable();

            $table->foreignId('ICL_action_step_matter_id')->nullable()->constrained('matters', 'matter_id');

            $table->foreignId('ICL_status')->nullable()->constrained('intake_statuses', 'IS_id');
            $table->foreignId('ICL_completed_by')->nullable()->constrained('action_step_attorneys_legal_assistants', 'ASALA_id');
            $table->foreignId('ICL_deposition')->nullable()->constrained('intake_depositions', 'ID_id');
            $table->foreignId('ICL_schedule_platform')->nullable()->constrained('intake_schedule_platforms', 'ISP_id');

            $table->dateTime('ICL_schedule_date')->nullable();
            $table->dateTime('ICL_completed_date')->nullable();
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
        Schema::dropIfExists('intake_change_logs');
    }
};

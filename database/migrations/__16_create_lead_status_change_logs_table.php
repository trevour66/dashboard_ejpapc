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
        Schema::create('lead_status_change_logs', function (Blueprint $table) {
            $table->id('LSCL_id');

            $table->foreignId('LSCL_previous_status')->nullable()->constrained('lead_statuses', 'LSt_id');
            $table->foreignId('LSCL_current_status')->nullable()->constrained('lead_statuses', 'LSt_id');
            $table->foreignId('LSCL_action_step_lead_id')->constrained('leads', 'lead_id');
            $table->foreignId('LSCL_action_step_matter_id')->constrained('matters', 'matter_id');

            $table->timestamp('LSCL_change_date_time')->nullable();
            $table->enum('LSCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('lead_status_change_logs');
    }
};

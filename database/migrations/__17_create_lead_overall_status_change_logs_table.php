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
        Schema::create('lead_overall_status_change_logs', function (Blueprint $table) {
            $table->id('LOSCL_id');

            $table->foreignId('LOSCL_prev_ov_status')->nullable()->constrained('lead_overall_statuses', 'LOS_id');
            $table->foreignId('LOSCL_active_ov_status')->nullable()->constrained('lead_overall_statuses', 'LOS_id');
            $table->foreignId('LOSCL_as_lead_id')->constrained('leads', 'lead_id');
            $table->foreignId('LOSCL_as_matter_id')->constrained('matters', 'matter_id');


            $table->timestamp('LOSCL_change_date_time')->nullable();
            $table->enum('LOSCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('lead_overall_status_change_logs');
    }
};

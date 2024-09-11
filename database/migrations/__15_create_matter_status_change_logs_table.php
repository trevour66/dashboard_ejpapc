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
        Schema::create('matter_status_change_logs', function (Blueprint $table) {
            $table->id('MSCL_id');

            $table->foreignId('MSCL_previous_status')->nullable()->constrained('matter_statuses', 'MSt_id');
            $table->foreignId('MSCL_current_status')->nullable()->constrained('matter_statuses', 'MSt_id');
            $table->foreignId('MSCL_action_step_matter_id')->constrained('matters', 'matter_id');

            $table->timestamp('MSCL_change_date_time')->nullable();
            $table->enum('MSCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('matter_status_change_logs');
    }
};

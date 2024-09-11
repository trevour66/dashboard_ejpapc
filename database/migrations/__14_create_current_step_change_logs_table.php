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
        Schema::create('current_step_change_logs', function (Blueprint $table) {
            $table->id('CSCL_id');

            $table->foreignId('CSCL_previous_step')->nullable()->constrained('a_s__steps', 'step_id');
            $table->foreignId('CSCL_current_step')->nullable()->constrained('a_s__steps', 'step_id');
            $table->foreignId('CSCL_action_step_matter_id')->nullable()->constrained('matters', 'matter_id');

            $table->timestamp('CSCL_change_date_time')->nullable();
            $table->enum('CSCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('current_step_change_logs');
    }
};

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
        Schema::create('consultation_change_logs', function (Blueprint $table) {
            $table->id('CCL_id');
            $table->string('CCL_outcome')->nullable();

            $table->foreignId('CCL_schedule_platform')->nullable()->constrained('consultation_schedule_platforms', 'CSP_id');
            $table->foreignId('CCL_action_step_matter_id')->nullable()->constrained('matters', 'matter_id');

            $table->dateTime('CCL_schedule_date')->nullable();
            $table->dateTime('CCL_date_conducted')->nullable();

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
        Schema::dropIfExists('consultation_change_logs');
    }
};

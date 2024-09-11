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
        Schema::create('matter_name_change_logs', function (Blueprint $table) {
            $table->id('MNCL_id');

            $table->string('MNCL_previous_name')->nullable();
            $table->string('MNCL_current_name')->nullable();
            $table->foreignId('MNCL_action_step_matter_id')->nullable()->constrained('matters', 'matter_id');

            $table->timestamp('MNCL_change_date_time')->nullable();
            $table->enum('MNCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('matter_name_change_logs');
    }
};

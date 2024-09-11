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
        Schema::create('matter_type_change_logs', function (Blueprint $table) {
            $table->id('MTCL_id');

            $table->foreignId('MTCL_previous_matter_type')->nullable()->constrained('matter_types', 'MT_id');
            $table->foreignId('MTCL_current_matter_type')->nullable()->constrained('matter_types', 'MT_id');
            $table->foreignId('MTCL_action_step_matter')->nullable()->constrained('matters', 'matter_id');

            $table->timestamp('MTCL_change_date_time')->nullable();
            $table->enum('MTCL_change_status', ['INITIAL', 'REAL_CHANGE'])->default('INITIAL');

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
        Schema::dropIfExists('matter_type_change_logs');
    }
};

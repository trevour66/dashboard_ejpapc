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
        Schema::create('leads', function (Blueprint $table) {
            $table->id('lead_id');
            $table->string('lead_name')->nullable();
            $table->string('lead_email')->unique();
            $table->string('lead_zipcode')->nullable();

            $table->foreignId('lead_current_overall_lead_status')->nullable()->constrained('lead_overall_statuses', 'LOS_id');
            $table->foreignId('lead_source')->nullable()->constrained('lead_sources', 'LS_id');
            $table->foreignId('lead_current_status')->nullable()->constrained('lead_statuses', 'LSt_id');

            $table->dateTime('lead_date_created')->nullable();

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
        Schema::dropIfExists('leads');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('facility_hours', function (Blueprint $table) {
            $table->id(); 
            
            $table->unsignedBigInteger('facility_id');
            
            $table->enum('day_of_week', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);

            $table->time('open_time');
            $table->time('close_time');

            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();

            $table->timestamps();

            $table->foreign('facility_id')
                  ->references('id')
                  ->on('facilities')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('facility_hours');
    }
};

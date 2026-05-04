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
        Schema::create('online_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_username', 50);
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('consultation_fee', 9, 2);
            $table->text('description');
            $table->softDeletes();

            $table->foreign('doctor_username')
                ->references('username')
                ->on('doctors')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_sessions');
    }
};

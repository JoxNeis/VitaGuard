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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_username', 50);
            $table->string('member_username', 50);
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('consultation_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('doctor_username')
                ->references('username')
                ->on('doctors')
                ->cascadeOnUpdate();

            $table->foreign('member_username')
                ->references('username')
                ->on('members')
                ->cascadeOnUpdate();

            $table->foreign('appointment_id')
                ->references('id')
                ->on('appointments')
                ->cascadeOnUpdate();

            $table->foreign('consultation_id')
                ->references('id')
                ->on('consultations')
                ->cascadeOnUpdate();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};

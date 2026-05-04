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
        Schema::create('prescription_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prescription_id');
            $table->unsignedBigInteger('medicine_id');
            $table->integer('quantity');
            $table->date('start');
            $table->date('end');
            $table->dateTime('taken')->nullable();
            $table->unsignedBigInteger('taken_at')->nullable();
            $table->string('instructions', 255)->nullable();
            $table->timestamps();

            $table->foreign('prescription_id')
                ->references('id')
                ->on('prescriptions')
                ->cascadeOnUpdate();

            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicines')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreign('taken_at')
                ->references('id')
                ->on('facilities')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_details');
    }
};

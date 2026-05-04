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
        Schema::create('medicines', function (Blueprint $table){
            $table->id();
            $table->string('name', 100);
            $table->enum('dosage_form', ['tablet', 'capsules', 'syrup', 'injection', 'ointment']);
            $table->enum('medicine_class', ['over_the_counter', 'limited_otc', 'prescription', 'narcotic', 'psychotropic']);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};

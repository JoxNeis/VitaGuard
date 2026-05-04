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
        Schema::create('member_medical_profiles', function (Blueprint $table) {            
            $table->string('member_username', 50)->primary();            
            $table->enum('blood_type', ['A-', 'A+', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 2);
            $table->enum('smoking_status', ['never', 'former', 'current']);
            $table->enum('alcohol_consumption', ['none', 'light', 'moderate', 'heavy']);            
            $table->foreign('member_username')->references('username')->on('members'); 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

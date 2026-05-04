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
        Schema::create('member_allergies', function (Blueprint $table) {            
            $table->id();
            $table->string('member_username', 50);
            $table->unsignedBigInteger('allergen_id');
            $table->string('inputted_by', 50);
            $table->enum('severity', ['mild', 'moderate', 'severe'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('member_username')->references('username')->on('members')->cascadeOnUpdate();
            $table->foreign('allergen_id')->references('id')->on('allergens')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('inputted_by')->references('username')->on('users')->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_allergies');
    }
};

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
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->string('member_username', 50);
            $table->string('inputted_by', 50);
            $table->date('diagnosed_date');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('member_username')->references('username')->on('members')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('inputted_by')->references('username')->on('users')->cascadeOnDelete()->cascadeOnUpdate();            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};

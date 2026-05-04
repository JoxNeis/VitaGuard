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
        Schema::create('doctors', function (Blueprint $table) {
            $table->string('username', 50)->primary();
            
            $table->string('prefix_name', 20)->nullable();
            $table->string('first_name', 100); 
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('suffix_name', 100)->nullable();
            
            $table->date('date_of_birth');
            $table->string('address', 255);
                
            $table->unsignedBigInteger('district_id'); 
            $table->foreign('district_id')->references('id')->on('districts');

            $table->decimal('rating_avg', 3, 2)->nullable();
            $table->integer('rating_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

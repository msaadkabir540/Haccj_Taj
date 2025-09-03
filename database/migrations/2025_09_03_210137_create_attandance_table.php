<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attandance', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->timestamp('time');
            $table->string('employeecode');
            $table->foreign('employeecode')->references('employeecode')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attandance');
    }
};

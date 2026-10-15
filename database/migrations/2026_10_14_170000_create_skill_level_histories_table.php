<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_level_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('old_level')->nullable();
            $table->string('new_level');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_level_histories');
    }
};
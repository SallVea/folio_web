<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("certificates", function (Blueprint $table) {
            $table->string("credential_status", 20)->default("unknown")->after("credential_url");
        });
    }

    public function down(): void
    {
        Schema::table("certificates", function (Blueprint $table) {
            $table->dropColumn("credential_status");
        });
    }
};

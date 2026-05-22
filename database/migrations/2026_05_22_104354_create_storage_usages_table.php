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
        if (Schema::hasTable('storage_usages')) {
            return;
        }

        Schema::create('storage_usages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('used_bytes')->default(0);
            $table->bigInteger('max_bytes')->default(1073741824); // 1GB
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_usages');
    }
};

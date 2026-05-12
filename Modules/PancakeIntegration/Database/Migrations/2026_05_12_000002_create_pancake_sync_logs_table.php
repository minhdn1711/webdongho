<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pancake_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->index();
            $table->unsignedBigInteger('model_id')->index()->nullable();
            $table->string('action'); // create, update, delete
            $table->string('status'); // pending, success, failed
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('pancake_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('topic')->index();
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pancake_webhook_logs');
        Schema::dropIfExists('pancake_sync_logs');
    }
};

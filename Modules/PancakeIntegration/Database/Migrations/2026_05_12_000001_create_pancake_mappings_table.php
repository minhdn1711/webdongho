<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Mappings
        Schema::create('pancake_product_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('pancake_product_id')->index();
            $table->string('pancake_variant_id')->nullable()->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // Order Mappings
        Schema::create('pancake_order_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('pancake_order_id')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });

        // Customer Mappings
        Schema::create('pancake_customer_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pancake_customer_id')->index();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pancake_customer_mappings');
        Schema::dropIfExists('pancake_order_mappings');
        Schema::dropIfExists('pancake_product_mappings');
    }
};

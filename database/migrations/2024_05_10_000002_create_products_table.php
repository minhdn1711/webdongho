<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $col) {
            $col->id();
            $col->foreignId('category_id')->constrained()->onDelete('cascade');
            $col->string('name');
            $col->string('slug')->unique();
            $col->text('description')->nullable();
            $col->decimal('price', 15, 2);
            $col->decimal('sale_price', 15, 2)->nullable();
            $col->string('image')->nullable();
            $col->boolean('is_featured')->default(false);
            $col->integer('stock')->default(0);
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

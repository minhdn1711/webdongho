<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_product', function (Blueprint $box) {
            $box->id();
            $box->foreignId('category_id')->constrained()->onDelete('cascade');
            $box->foreignId('product_id')->constrained()->onDelete('cascade');
            $box->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};

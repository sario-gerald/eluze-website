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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('collection');
            $table->string('scent')->nullable();
            $table->string('inspiration')->nullable();
            $table->unsignedInteger('price_10ml')->default(850);
            $table->unsignedInteger('price_30ml')->default(1350);
            $table->unsignedInteger('price_50ml')->default(1850);
            $table->unsignedInteger('price_75ml')->default(2350);
            $table->unsignedInteger('price_100ml')->default(2850);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['collection', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

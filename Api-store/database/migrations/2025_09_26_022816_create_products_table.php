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

            $table->foreignId('user_id')->references('id')->on("users")->onDelete('cascade')->onUpdate('cascade');
            $table->string("name");
            $table->string("description");
            $table->decimal('price', 12, 2)->default(0);
            $table->string('slug')->unique()->nullable();
            $table->string('sku')->unique()->nullable(); // كود مميز للمنتج
            $table->integer('stock')->default(0);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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

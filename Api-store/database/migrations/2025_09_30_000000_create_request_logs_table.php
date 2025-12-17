<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('path', 255);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['method', 'path']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};



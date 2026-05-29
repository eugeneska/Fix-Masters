<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source', 64);
            $table->json('quiz_answers')->nullable();
            $table->string('name');
            $table->string('phone', 32);
            $table->text('comment')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('gclid')->nullable();
            $table->string('yclid')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('qualification_status')->nullable();
            $table->string('quality_status')->nullable();
            $table->boolean('conversion_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

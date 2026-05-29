<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5)->default('ru')->index();
            // URL-путь страницы: '/', '/about' и т.п. Источник истины по роутингу.
            $table->string('path');
            $table->string('title');
            // published | draft — что отдавать публично.
            $table->string('status')->default('published');
            // Если задано — резолвер вернёт редирект на этот путь.
            $table->string('redirect_to')->nullable();
            // Блочная модель контента и SEO — JSONB на PostgreSQL (text на sqlite в тестах).
            $table->jsonb('blocks')->nullable();
            $table->jsonb('seo')->nullable();
            $table->timestamps();

            // path уникален в рамках локали.
            $table->unique(['locale', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};

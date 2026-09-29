<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('type');                   // enum: bot, web, sistem, magang, kegiatan
            $table->json('title');                    // translatable
            $table->json('summary');                  // translatable
            $table->json('body')->nullable();         // translatable (markdown)
            $table->json('stack')->nullable();        // JSON array
            $table->string('cover_image')->nullable();
            $table->json('cover_alt')->nullable();    // translatable
            $table->string('telegram_url')->nullable();
            $table->string('site_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft'); // enum: draft, published
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('type');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

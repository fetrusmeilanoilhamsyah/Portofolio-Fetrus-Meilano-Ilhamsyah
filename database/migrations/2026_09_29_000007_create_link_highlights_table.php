<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('link_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->cascadeOnDelete();
            $table->json('title');                    // translatable
            $table->json('summary');                  // translatable
            $table->string('url')->nullable();
            $table->date('highlighted_at');
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['link_id', 'highlighted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_highlights');
    }
};

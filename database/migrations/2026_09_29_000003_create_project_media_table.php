<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('kind');                   // enum: image, video, embed
            $table->string('path')->nullable();
            $table->string('url')->nullable();
            $table->json('caption')->nullable();      // translatable
            $table->json('alt')->nullable();          // translatable
            $table->string('poster')->nullable();     // untuk video
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_media');
    }
};

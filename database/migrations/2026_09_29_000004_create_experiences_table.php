<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('kind');                     // enum: kerja, magang, organisasi, pendidikan
            $table->json('title');                      // translatable
            $table->string('organization');
            $table->string('location')->nullable();
            $table->json('description')->nullable();    // translatable (markdown)
            $table->string('logo')->nullable();
            $table->date('started_at');
            $table->date('ended_at')->nullable();       // null = masih berjalan
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['kind', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};

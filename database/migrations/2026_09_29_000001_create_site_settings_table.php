<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->json('role')->nullable();          // translatable
            $table->json('intro_home')->nullable();    // translatable
            $table->json('about_body')->nullable();    // translatable (markdown)
            $table->string('photo')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('location')->nullable();
            $table->boolean('open_to_work')->default(false);
            $table->json('open_to_work_note')->nullable(); // translatable
            $table->json('skills')->nullable();        // JSON: kelompok berisi daftar keahlian
            $table->string('og_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->boolean('show_on_cv')->default(true)->after('is_published');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->boolean('show_on_cv')->default(true)->after('is_published');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->boolean('show_on_cv')->default(false)->after('is_published');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->json('cv_summary')->nullable()->after('about_body');
            $table->boolean('cv_show_photo')->default(false)->after('cv_summary');
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('show_on_cv');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('show_on_cv');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('show_on_cv');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['cv_summary', 'cv_show_photo']);
        });
    }
};

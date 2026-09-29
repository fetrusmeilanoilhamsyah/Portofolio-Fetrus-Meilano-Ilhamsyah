<?php

namespace Tests\Feature;

use App\Enums\ProjectType;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslatableFallbackTest extends TestCase
{
    use RefreshDatabase;

    /** Locale en kosong (null) fall back ke id */
    public function test_fallback_when_en_is_null(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul Indonesia'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        // Minta locale en yang tidak ada → harus fall back ke id
        $this->assertEquals('Judul Indonesia', $project->getTranslation('title', 'en'));
    }

    /** Locale en kosong (string kosong) fall back ke id */
    public function test_fallback_when_en_is_empty_string(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul Indonesia', 'en' => ''],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        // String kosong tidak dianggap sebagai terjemahan valid → fall back ke id
        $this->assertEquals('Judul Indonesia', $project->getTranslation('title', 'en'));
    }

    /** Locale en terisi → tampilkan bahasa Inggris */
    public function test_returns_en_when_set(): void
    {
        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul Indonesia', 'en' => 'English Title'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals('English Title', $project->getTranslation('title', 'en'));
    }

    /** getAttributeValue saat locale app = id mengembalikan versi id */
    public function test_attribute_access_with_app_locale_id(): void
    {
        app()->setLocale('id');

        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul ID', 'en' => 'EN Title'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        // Akses via magic property mengikuti locale app
        $this->assertEquals('Judul ID', $project->title);
    }

    /** getAttributeValue saat locale app = en mengembalikan versi en jika ada */
    public function test_attribute_access_with_app_locale_en(): void
    {
        app()->setLocale('en');

        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul ID', 'en' => 'EN Title'],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals('EN Title', $project->title);

        // Reset locale
        app()->setLocale('id');
    }

    /** getAttributeValue saat locale app = en dan en kosong, fall back ke id */
    public function test_attribute_access_fallback_with_app_locale_en_empty(): void
    {
        app()->setLocale('en');

        $project = Project::create([
            'type' => ProjectType::Web->value,
            'title' => ['id' => 'Judul ID', 'en' => ''],
            'summary' => ['id' => 'Ringkasan'],
        ]);

        $this->assertEquals('Judul ID', $project->title);

        // Reset locale
        app()->setLocale('id');
    }
}

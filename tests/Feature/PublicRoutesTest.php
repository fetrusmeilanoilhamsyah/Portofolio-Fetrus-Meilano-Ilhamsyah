<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Tes untuk semua rute publik.
 * Setiap rute harus mengembalikan HTTP 200.
 */
class PublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rute versi Indonesia (tanpa prefix).
     */
    #[DataProvider('indonesianRoutes')]
    public function test_indonesian_routes_return_200(string $url): void
    {
        $response = $this->get($url);
        $response->assertStatus(200);
    }

    /**
     * Rute versi Inggris (prefix /en).
     */
    #[DataProvider('englishRoutes')]
    public function test_english_routes_return_200(string $url): void
    {
        $response = $this->get($url);
        $response->assertStatus(200);
    }

    public static function indonesianRoutes(): array
    {
        return [
            'Home (ID)' => ['/'],
            'About (ID)' => ['/about'],
            'Experience (ID)' => ['/experience'],
            'Projects (ID)' => ['/projects'],
            'Social (ID)' => ['/social'],
            'Contact (ID)' => ['/contact'],
        ];
    }

    public static function englishRoutes(): array
    {
        return [
            'Home (EN)' => ['/en'],
            'About (EN)' => ['/en/about'],
            'Experience (EN)' => ['/en/experience'],
            'Projects (EN)' => ['/en/projects'],
            'Social (EN)' => ['/en/social'],
            'Contact (EN)' => ['/en/contact'],
        ];
    }

    /** Halaman proyek individual tidak error 500 (slug tidak ditemukan → 200 dengan empty state). */
    public function test_project_show_returns_200(): void
    {
        $response = $this->get('/projects/contoh-proyek');
        $response->assertStatus(200);
    }

    /** Versi EN halaman proyek individual. */
    public function test_en_project_show_returns_200(): void
    {
        $response = $this->get('/en/projects/example-project');
        $response->assertStatus(200);
    }
}

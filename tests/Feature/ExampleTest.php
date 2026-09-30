<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tes dasar aplikasi — verifikasi bahwa rute utama bekerja.
 * Halaman welcome bawaan Laravel sudah dihapus dan diganti dengan layout baru.
 */
class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** Halaman utama (/) menggunakan layout publik baru dan mengembalikan 200. */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

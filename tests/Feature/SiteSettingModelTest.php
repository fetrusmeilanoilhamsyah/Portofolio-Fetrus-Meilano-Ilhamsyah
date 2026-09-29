<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingModelTest extends TestCase
{
    use RefreshDatabase;

    /** current() membuat baris pertama jika belum ada */
    public function test_current_creates_if_not_exists(): void
    {
        $this->assertEquals(0, SiteSetting::query()->count());

        $setting = SiteSetting::current();

        $this->assertInstanceOf(SiteSetting::class, $setting);
        $this->assertEquals(1, SiteSetting::query()->count());
    }

    /** current() mengembalikan baris yang ada, tidak membuat baru */
    public function test_current_returns_existing(): void
    {
        $first = SiteSetting::current();
        $first->name = 'Fetrus';
        $first->save();

        $second = SiteSetting::current();

        $this->assertEquals(1, SiteSetting::query()->count());
        $this->assertEquals($first->id, $second->id);
        $this->assertEquals('Fetrus', $second->name);
    }

    /** Tidak boleh ada baris kedua di site_settings */
    public function test_cannot_create_second_row(): void
    {
        SiteSetting::current(); // baris pertama

        // Coba buat lagi secara eksplisit—harus dicegah oleh booted hook
        $second = SiteSetting::create(['name' => 'Kedua']);

        $this->assertEquals(1, SiteSetting::query()->count());
        // Kalau dicegah, $second seharusnya tidak punya ID (atau false)
        // Di sini kita hanya cek jumlahnya
    }

    /** current() dipanggil berkali-kali tidak duplikasi */
    public function test_current_idempotent(): void
    {
        SiteSetting::current();
        SiteSetting::current();
        SiteSetting::current();

        $this->assertEquals(1, SiteSetting::query()->count());
    }
}

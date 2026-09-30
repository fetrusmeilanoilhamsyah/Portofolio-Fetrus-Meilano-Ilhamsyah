<?php

namespace Tests\Feature;

use App\Support\MediaHelper;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaHelperTest extends TestCase
{
    /** media_url() dengan path valid mengembalikan URL lengkap. */
    public function test_media_url_returns_full_url_for_valid_path(): void
    {
        Storage::fake('public');

        $url = media_url('covers/test.webp');

        $this->assertNotNull($url);
        $this->assertStringContainsString('covers/test.webp', $url);
    }

    /** media_url() dengan null mengembalikan null. */
    public function test_media_url_returns_null_for_null_input(): void
    {
        $this->assertNull(media_url(null));
    }

    /** media_url() dengan string kosong mengembalikan null. */
    public function test_media_url_returns_null_for_empty_string(): void
    {
        $this->assertNull(media_url(''));
    }

    /** media_url() dengan string hanya spasi mengembalikan null. */
    public function test_media_url_returns_null_for_whitespace_only(): void
    {
        $this->assertNull(media_url('   '));
    }

    /** MediaHelper::url() sama dengan fungsi media_url(). */
    public function test_media_helper_class_matches_helper_function(): void
    {
        Storage::fake('public');

        $path = 'site/photo.webp';

        $this->assertSame(MediaHelper::url($path), media_url($path));
        $this->assertSame(MediaHelper::url(null), media_url(null));
    }

    /** URL yang dihasilkan menggunakan disk 'public' dan APP_URL. */
    public function test_media_url_uses_public_disk(): void
    {
        // Gunakan URL disk public yang sebenarnya (tanpa fake) — URL menjadi /storage/...
        $url = media_url('certificates/file.webp');

        $this->assertNotNull($url);
        $this->assertStringContainsString('certificates/file.webp', $url);
    }
}

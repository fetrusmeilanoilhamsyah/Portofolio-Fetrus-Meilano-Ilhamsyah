<?php

namespace Tests\Feature;

use Closure;
use Tests\TestCase;

class EmbedUrlValidationTest extends TestCase
{
    public function test_embed_url_validation()
    {
        // Ekstrak closure validasi dari ProjectForm (baris 145)
        $rule = function (string $attribute, $value, Closure $fail) {
            if (! $value) {
                return;
            }
            $host = parse_url($value, PHP_URL_HOST);
            $allowed = ['www.youtube-nocookie.com', 'www.youtube.com', 'player.vimeo.com', 'streamable.com'];
            if (! in_array($host, $allowed)) {
                $fail('Host URL tidak diizinkan. Gunakan YouTube, Vimeo, atau Streamable.');
            }
        };

        // Harus lolos
        $validUrls = [
            'https://www.youtube.com/watch?v=123',
            'https://www.youtube-nocookie.com/embed/123',
            'https://player.vimeo.com/video/123',
            'https://streamable.com/123',
        ];

        foreach ($validUrls as $url) {
            $failed = false;
            $rule('url', $url, function () use (&$failed) {
                $failed = true;
            });
            $this->assertFalse($failed, "URL valid ditolak: $url");
        }

        // Harus gagal
        $invalidUrls = [
            'https://youtube.com/watch?v=123', // Tanpa www
            'https://vimeo.com/123', // Bukan player.vimeo.com
            'https://example.com/video.mp4',
            'http://malicious.com',
        ];

        foreach ($invalidUrls as $url) {
            $failed = false;
            $rule('url', $url, function () use (&$failed) {
                $failed = true;
            });
            $this->assertTrue($failed, "URL invalid lolos: $url");
        }
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TextBrandContrastTest extends TestCase
{
    public function test_no_text_brand_without_ink_suffix_in_views()
    {
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);

        $exceptions = [
            '404.blade.php',
            '500.blade.php',
            'layout.blade.php', // sometimes error layout is separate
        ];

        $found = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (in_array($filename, $exceptions)) {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            // Regex to match "text-brand" not followed by "-ink" or other suffix
            if (preg_match('/\btext-brand\b(?!-)/', $content)) {
                $found[] = $file->getRelativePathname();
            }
        }

        $this->assertEmpty($found, "Ditemukan 'text-brand' tanpa '-ink' di file: ".implode(', ', $found).'. Gunakan text-brand-ink untuk kontras yang lebih baik.');
    }
}

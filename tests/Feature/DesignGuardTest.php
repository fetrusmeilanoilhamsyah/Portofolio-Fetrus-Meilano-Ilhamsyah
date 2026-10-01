<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DesignGuardTest extends TestCase
{
    public function test_views_do_not_violate_design_rules()
    {
        $viewsPath = resource_path('views');
        $files = File::allFiles($viewsPath);

        $violations = [];

        foreach ($files as $file) {
            // Abaikan direktori errors/ dan vendor/filament
            if (str_contains($file->getPathname(), 'errors'.DIRECTORY_SEPARATOR) ||
                str_contains($file->getPathname(), 'filament') ||
                ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            $content = file_get_contents($file->getPathname());

            $patterns = [
                '/(?<!-)((?:text|bg|border|divide|ring|from|to|via)-(?:primary|accent)(?:-\d+)?(?:(?:\/)\d+)?)\b/' => 'Color primary/accent class found',
                '/\bbackdrop-blur(?:-[a-z0-9]+)?\b/' => 'Glassmorphism (backdrop-blur) found',
                '/\bshadow-(?!none\b)[a-z0-9-]+\b/' => 'Shadow found',
                '/\brounded-(?:xl|2xl|3xl)\b/' => 'Large rounded corners found',
            ];

            foreach ($patterns as $pattern => $message) {
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[0] as $match) {
                        $violations[] = "{$file->getRelativePathname()}: {$message} -> {$match}";
                    }
                }
            }
        }

        $this->assertEmpty($violations, "Design violations found:\n".implode("\n", $violations));
    }
}

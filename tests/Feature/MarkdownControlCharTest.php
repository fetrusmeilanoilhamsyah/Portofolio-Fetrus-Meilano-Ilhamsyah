<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MarkdownControlCharTest extends TestCase
{
    public function test_no_control_characters_in_docs()
    {
        $docsDir = base_path('docs');
        $files = File::glob($docsDir.'/*.md');

        foreach ($files as $file) {
            $content = file_get_contents($file);

            // Check for control characters (ASCII 0-31), except \n (10) and \t (9) and \r (13)
            // preg_match with [\x00-\x08\x0B\x0C\x0E-\x1F]
            $hasControlChars = preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $content, $matches);

            $this->assertFalse(
                (bool) $hasControlChars,
                'Found control character in file '.basename($file).': '.bin2hex($matches[0] ?? '')
            );
        }
    }
}

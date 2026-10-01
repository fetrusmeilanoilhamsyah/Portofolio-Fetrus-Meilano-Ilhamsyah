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

            // Cek tab di dalam kata atau sebelum huruf (tanpa spasi) yang biasanya merupakan sisa karakter yang terhapus bersama 'a'
            $hasTabInWord = preg_match('/[a-zA-Z]\t[a-zA-Z]|\t[a-zA-Z]/', $content, $tabMatches);
            $this->assertFalse(
                (bool) $hasTabInWord,
                'Found tab inside or before word in file '.basename($file).': '.($tabMatches[0] ?? '')
            );

            // Cek kata-kata rusak yang diketahui (harus di awal kata, tidak boleh bagian dari kata yang benar)
            $brokenWords = ['spect-video', 'spect-\[4\/3\]', 'spect-square', 'llow_unsafe_links', 'ria-label', '\\\\vendor\/bin\/pint'];
            foreach ($brokenWords as $word) {
                $this->assertFalse(
                    (bool) preg_match('/(?:^|[^a-zA-Z0-9_-])'.$word.'(?:$|[^a-zA-Z0-9_-])/', $content),
                    "Found broken word '$word' in file ".basename($file)
                );
            }
        }
    }
}

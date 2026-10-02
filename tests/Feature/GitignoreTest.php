<?php

namespace Tests\Feature;

use Tests\TestCase;

class GitignoreTest extends TestCase
{
    public function test_gitignore_has_no_bom_or_null_bytes()
    {
        $path = base_path('.gitignore');
        $this->assertFileExists($path);

        $content = file_get_contents($path);

        // Check for BOM (EF BB BF)
        $bom = pack('H*', 'EFBBBF');
        $this->assertStringNotContainsString($bom, substr($content, 0, 3), '.gitignore contains UTF-8 BOM!');

        // Check for NUL bytes
        $this->assertStringNotContainsString("\0", $content, '.gitignore contains NUL bytes!');
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ColorContrastTest extends TestCase
{
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private function getLuminance(array $rgb): float
    {
        $rgb = array_map(function ($val) {
            $val /= 255;

            return $val <= 0.03928 ? $val / 12.92 : pow(($val + 0.055) / 1.055, 2.4);
        }, $rgb);

        return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
    }

    private function getContrastRatio(string $hex1, string $hex2): float
    {
        $lum1 = $this->getLuminance($this->hexToRgb($hex1));
        $lum2 = $this->getLuminance($this->hexToRgb($hex2));

        $lightest = max($lum1, $lum2);
        $darkest = min($lum1, $lum2);

        return ($lightest + 0.05) / ($darkest + 0.05);
    }

    private function extractVariables(string $css, string $selector): array
    {
        $pattern = '/'.preg_quote($selector, '/').'\s*\{([^}]+)\}/s';
        if (preg_match($pattern, $css, $matches)) {
            $block = $matches[1];
            preg_match_all('/--([a-zA-Z0-9-]+):\s*(#[a-fA-F0-9]{3,6});/', $block, $vars);

            $result = [];
            foreach ($vars[1] as $index => $name) {
                $result[$name] = $vars[2][$index];
            }

            return $result;
        }

        return [];
    }

    public function test_colors_meet_wcag_aa_contrast_requirements()
    {
        $css = File::get(resource_path('css/app.css'));

        $lightTheme = $this->extractVariables($css, ':root');
        $darkTheme = $this->extractVariables($css, 'html.dark');

        $this->assertNotEmpty($lightTheme, 'Light theme variables not found');
        $this->assertNotEmpty($darkTheme, 'Dark theme variables not found');

        $scenarios = [
            'Light Mode: ink on canvas' => ['theme' => $lightTheme, 'fg' => 'ink', 'bg' => 'canvas'],
            'Light Mode: ink-muted on canvas' => ['theme' => $lightTheme, 'fg' => 'ink-muted', 'bg' => 'canvas'],
            'Light Mode: brand-ink on canvas' => ['theme' => $lightTheme, 'fg' => 'brand-ink', 'bg' => 'canvas'],
            'Light Mode: ink on surf' => ['theme' => $lightTheme, 'fg' => 'ink', 'bg' => 'surf'],
            'Light Mode: ink-muted on surf' => ['theme' => $lightTheme, 'fg' => 'ink-muted', 'bg' => 'surf'],
            'Light Mode: brand-ink on surf' => ['theme' => $lightTheme, 'fg' => 'brand-ink', 'bg' => 'surf'],
            'Light Mode: brand-fg on brand' => ['theme' => $lightTheme, 'fg' => 'brand-fg', 'bg' => 'brand'],
            'Light Mode: ink-muted on canvas-muted' => ['theme' => $lightTheme, 'fg' => 'ink-muted', 'bg' => 'canvas-muted'],
            'Light Mode: brand-ink on canvas-muted' => ['theme' => $lightTheme, 'fg' => 'brand-ink', 'bg' => 'canvas-muted'],

            'Dark Mode: ink on canvas' => ['theme' => $darkTheme, 'fg' => 'ink', 'bg' => 'canvas'],
            'Dark Mode: ink-muted on canvas' => ['theme' => $darkTheme, 'fg' => 'ink-muted', 'bg' => 'canvas'],
            'Dark Mode: brand-ink on canvas' => ['theme' => $darkTheme, 'fg' => 'brand-ink', 'bg' => 'canvas'],
            'Dark Mode: ink on surf' => ['theme' => $darkTheme, 'fg' => 'ink', 'bg' => 'surf'],
            'Dark Mode: ink-muted on surf' => ['theme' => $darkTheme, 'fg' => 'ink-muted', 'bg' => 'surf'],
            'Dark Mode: brand-ink on surf' => ['theme' => $darkTheme, 'fg' => 'brand-ink', 'bg' => 'surf'],
            'Dark Mode: brand-fg on brand' => ['theme' => $darkTheme, 'fg' => 'brand-fg', 'bg' => 'brand'],
            'Dark Mode: ink-muted on canvas-muted' => ['theme' => $darkTheme, 'fg' => 'ink-muted', 'bg' => 'canvas-muted'],
            'Dark Mode: brand-ink on canvas-muted' => ['theme' => $darkTheme, 'fg' => 'brand-ink', 'bg' => 'canvas-muted'],
        ];

        foreach ($scenarios as $name => $data) {
            $theme = $data['theme'];
            $fg = $theme[$data['fg']];
            $bg = $theme[$data['bg']];

            $ratio = $this->getContrastRatio($fg, $bg);
            $this->assertGreaterThanOrEqual(
                4.5,
                $ratio,
                "Contrast ratio for {$name} ({$fg} on {$bg}) is {$ratio}:1, which is below 4.5:1"
            );

            // Print for PROGRESS.md
            // echo "\n{$name}: " . round($ratio, 2) . ":1";
        }
    }
}

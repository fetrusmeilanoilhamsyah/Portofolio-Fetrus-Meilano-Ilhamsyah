<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UndefinedColorTokenTest extends TestCase
{
    public function test_all_custom_color_classes_are_defined_in_app_css()
    {
        $cssContent = File::get(resource_path('css/app.css'));

        // Ekstrak semua variabel --color-* dari app.css
        preg_match_all('/--color-([a-zA-Z0-9\-]+):/', $cssContent, $matches);
        $definedColors = $matches[1];

        $viewFiles = File::allFiles(resource_path('views'));
        $invalidClasses = [];

        foreach ($viewFiles as $file) {
            $path = $file->getRelativePathname();
            // Lewati folder errors
            if (str_starts_with($path, 'errors'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $content = $file->getContents();

            // Ekstrak semua kata dalam atribut class="..."
            preg_match_all('/class="([^"]+)"/', $content, $classMatches);

            foreach ($classMatches[1] as $classList) {
                // Bersihkan karakter aneh jika tertangkap quote
                $classList = str_replace(["'", '`'], ' ', $classList);
                $classes = explode(' ', $classList);
                foreach ($classes as $class) {
                    $class = trim($class);
                    if (empty($class) || str_contains($class, '{{')) {
                        continue;
                    }

                    // Buang modifier (hover:, group-hover:, focus:, md:, lg:, dark:, dll)
                    $parts = explode(':', $class);
                    $baseClass = end($parts);

                    // Hapus opacity jika ada (misal bg-brand/50 -> bg-brand)
                    $baseClass = explode('/', $baseClass)[0];

                    // Cari prefix warna
                    $prefixes = ['text-', 'bg-', 'border-', 'divide-', 'ring-', 'fill-', 'stroke-', 'from-', 'to-', 'via-'];
                    $hasPrefix = false;
                    $matchedPrefix = '';

                    foreach ($prefixes as $prefix) {
                        if (str_starts_with($baseClass, $prefix)) {
                            $hasPrefix = true;
                            $matchedPrefix = $prefix;
                            break;
                        }
                    }

                    if ($hasPrefix) {
                        $colorName = substr($baseClass, strlen($matchedPrefix));

                        // Periksa apakah ini warna kustom kita
                        $customPrefixes = ['brand', 'ink', 'canvas', 'surf', 'line', 'ok'];
                        $isCustomColor = false;
                        foreach ($customPrefixes as $cp) {
                            if ($colorName === $cp || str_starts_with($colorName, $cp.'-')) {
                                $isCustomColor = true;
                                break;
                            }
                        }

                        if ($isCustomColor && ! in_array($colorName, $definedColors)) {
                            $invalidClasses[] = "Class '$class' in {$path} uses undefined color '$colorName'";
                        }
                    }
                }
            }
        }

        $this->assertEmpty($invalidClasses, "Found undefined custom color classes:\n".implode("\n", array_unique($invalidClasses)));
    }
}

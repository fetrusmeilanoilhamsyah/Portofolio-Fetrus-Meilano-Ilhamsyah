<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    public function test_all_ui_keys_exist_in_id_and_en()
    {
        $directories = [
            app_path(),
            resource_path('views'),
        ];

        $keys = [];

        foreach ($directories as $dir) {
            $files = File::allFiles($dir);
            foreach ($files as $file) {
                $content = $file->getContents();
                // Match __('ui.something') or trans('ui.something') or @lang('ui.something')
                preg_match_all("/__\(['\"]ui\.([a-zA-Z0-9_]+)['\"]\)/", $content, $matches1);
                preg_match_all("/trans\(['\"]ui\.([a-zA-Z0-9_]+)['\"]\)/", $content, $matches2);
                preg_match_all("/@lang\(['\"]ui\.([a-zA-Z0-9_]+)['\"]\)/", $content, $matches3);

                $found = array_merge($matches1[1], $matches2[1], $matches3[1]);
                foreach ($found as $key) {
                    $keys[$key] = true;
                }
            }
        }

        $idTranslations = require base_path('lang/id/ui.php');
        $enTranslations = require base_path('lang/en/ui.php');

        $missingId = [];
        $missingEn = [];

        foreach (array_keys($keys) as $key) {
            if (! array_key_exists($key, $idTranslations)) {
                $missingId[] = $key;
            }
            if (! array_key_exists($key, $enTranslations)) {
                $missingEn[] = $key;
            }
        }

        $this->assertEmpty($missingId, 'Missing ID translations for ui keys: '.implode(', ', $missingId));
        $this->assertEmpty($missingEn, 'Missing EN translations for ui keys: '.implode(', ', $missingEn));
    }
}

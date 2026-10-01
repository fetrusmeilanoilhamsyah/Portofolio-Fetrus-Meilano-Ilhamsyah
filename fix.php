<?php

$files = ['resources/views/pages/social.blade.php', 'resources/views/pages/contact.blade.php'];
foreach ($files as $file) {
    $c = file_get_contents($file);

    // Tokens
    $c = preg_replace('/\btext-primary-900\b\s*(\bdark:text-primary-100\b)?/', 'text-ink', $c);
    $c = preg_replace('/\bdark:text-primary-50\b/', '', $c);
    $c = preg_replace('/\btext-primary-600\b\s*(\bdark:text-primary-400\b)?/', 'text-ink-muted', $c);
    $c = preg_replace('/\bbg-white\b\s*(\bdark:bg-primary-900\/50\b)?/', 'bg-surf', $c);
    $c = preg_replace('/\bborder-primary-[12]00\b\s*(\bdark:border-primary-800\b)?/', 'border-line', $c);
    $c = preg_replace('/\bhover:bg-primary-50\b\s*(\bdark:hover:bg-primary-800(\/30)?\b)?/', 'hover:bg-ink/5', $c);

    // Teks berwarna aksen
    $c = preg_replace('/\btext-accent-\d+\b\s*(\bdark:text-accent-\d+\b)?/', 'text-brand-ink', $c);
    $c = preg_replace('/\btext-primary-500\b/', 'text-brand-ink', $c);
    $c = preg_replace('/\bhover:text-accent-\d+\b\s*(\bdark:hover:text-accent-\d+\b)?/', 'hover:text-brand-hover', $c);
    $c = preg_replace('/\bgroup-hover:text-accent-\d+\b\s*(\bdark:group-hover:text-accent-\d+\b)?/', 'group-hover:text-brand-hover', $c);
    $c = preg_replace('/\bhover:border-accent-\d+\b\s*(\bdark:hover:border-accent-\d+\b)?/', 'hover:border-brand', $c);
    $c = preg_replace('/\bhover:shadow-sm\b/', '', $c);

    // text-white bg-accent-600 hover:bg-accent-700 dark:bg-accent-500 dark:hover:bg-accent-600 -> we can replace this with x-button?
    // The prompt says use x-card, x-button, x-empty-state.

    // public_text
    $c = preg_replace('/\{\{\s*\$([a-zA-Z0-9_]+)->(label|note|title|summary)\s*\}\}/', '{{ public_text($\1->\2) }}', $c);

    // Clean up multiple spaces in classes
    $c = preg_replace_callback('/class="([^"]+)"/', function ($m) {
        return 'class="'.trim(preg_replace('/\s+/', ' ', $m[1])).'"';
    }, $c);

    file_put_contents($file, $c);
}
echo 'Done';

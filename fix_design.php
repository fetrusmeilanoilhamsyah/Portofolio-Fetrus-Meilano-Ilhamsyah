<?php

$files = [
    'resources/views/components/command-palette.blade.php',
    'resources/views/pages/about.blade.php',
    'resources/views/pages/experience.blade.php',
    'resources/views/pages/project-show.blade.php',
    'resources/views/pages/projects.blade.php',
];

foreach ($files as $file) {
    if (! file_exists($file)) {
        continue;
    }
    $c = file_get_contents($file);

    // Command palette
    $c = str_replace('text-accent', 'text-brand-ink', $c);
    $c = str_replace('border-accent', 'border-brand', $c);
    $c = str_replace('shadow-2xl', '', $c);
    $c = preg_replace('/\bbackdrop-blur-[a-z0-9]+\b/', '', $c);
    $c = str_replace('backdrop-blur', '', $c);

    // Shadow
    $c = preg_replace('/\bshadow-(?!none\b)[a-z0-9-]+\b/', '', $c);

    // Rounded
    $c = str_replace('rounded-3xl', 'rounded-lg', $c);
    $c = str_replace('rounded-2xl', 'rounded-lg', $c);
    $c = str_replace('rounded-xl', 'rounded-lg', $c);

    // Background overlay for modal in experience
    if (strpos($file, 'experience.blade.php') !== false) {
        $c = preg_replace('/bg-ink\/80/', 'bg-ink/60', $c);
        $c = preg_replace('/bg-white\/80/', 'bg-surf', $c);
        // Maybe it was bg-black/50 or bg-gray-900/50? I'll replace any overlay background
        $c = preg_replace('/bg-gray-900\/50/', 'bg-ink/60', $c);
    }

    // Fix multiple spaces
    $c = preg_replace_callback('/class="([^"]+)"/', function ($m) {
        return 'class="'.trim(preg_replace('/\s+/', ' ', $m[1])).'"';
    }, $c);

    file_put_contents($file, $c);
}
echo "Fixed design violations.\n";

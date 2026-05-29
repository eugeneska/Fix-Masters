<?php

/**
 * One-off: converts public HTML pages to Blade view bodies.
 * Run: php scripts/convert-html-to-blade.php
 */

$base = dirname(__DIR__);

function convertHtml(string $html): string
{
    $html = preg_replace('#src="\.\./images/([^"]+)"#', "src=\"{{ asset('images/$1') }}\"", $html);
    $html = preg_replace('#src="\./images/([^"]+)"#', "src=\"{{ asset('images/$1') }}\"", $html);
    $html = preg_replace('#href="\.\./"#', 'href="{{ route(\'home\') }}"', $html);
    $html = preg_replace('#href="\./"#', 'href="{{ route(\'home\') }}"', $html);
    $html = str_replace('href="pages/quiz.html"', 'href="{{ route(\'quiz\') }}"', $html);
    $html = str_replace('href="pages/request.html"', 'href="{{ route(\'request\') }}"', $html);
    $html = str_replace('href="pages/quiz-step-2.html"', 'href="{{ route(\'quiz.step-2\') }}"', $html);
    $html = str_replace('href="pages/quiz-step-3.html"', 'href="{{ route(\'quiz.step-3\') }}"', $html);
    $html = str_replace('href="pages/quiz-step-3-tv.html"', 'href="{{ route(\'quiz.step-3-tv\') }}"', $html);

    return $html;
}

function extractMain(string $html): ?string
{
    if (preg_match('/<main\b[^>]*>.*?<\/main>/s', $html, $m)) {
        return convertHtml($m[0]);
    }

    return null;
}

function wrapBlade(string $extends, string $section, string $body, array $extra = []): string
{
    $lines = ["@extends('$extends')", ''];
    foreach ($extra as $key => $value) {
        $lines[] = "@section('$key', '$value')";
        $lines[] = '';
    }
    $lines[] = "@section('$section')";
    $lines[] = $body;
    $lines[] = '@endsection';

    return implode("\n", $lines);
}

$map = [
    'public/index.html' => ['view' => 'resources/views/pages/home.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — ремонт техники', 'showQuizPromo' => true]],
    'public/pages/quiz.html' => ['view' => 'resources/views/pages/quiz/index.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — Квиз, шаг 1', 'bodyClass' => 'quiz-page']],
    'public/pages/quiz-step-2.html' => ['view' => 'resources/views/pages/quiz/step-2.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — Квиз, шаг 2', 'bodyClass' => 'quiz-page']],
    'public/pages/quiz-step-3.html' => ['view' => 'resources/views/pages/quiz/step-3.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — Квиз, шаг 3', 'bodyClass' => 'quiz-page']],
    'public/pages/quiz-step-3-tv.html' => ['view' => 'resources/views/pages/quiz/step-3-tv.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — Квиз, шаг 3 (ТВ)', 'bodyClass' => 'quiz-page']],
    'public/pages/request.html' => ['view' => 'resources/views/pages/request.blade.php', 'section' => 'content', 'extends' => 'layouts.fix-masters', 'extra' => ['title' => 'FIX-MASTERS — Оставить заявку', 'bodyClass' => 'quiz-page quiz-page--request']],
];

$scripts = [
    'public/index.html' => ['phone-mask.js', 'site-fab.js', 'services-quiz.js', 'callback-modal.js', 'quiz-promo-modal.js', 'cookie-consent.js'],
    'public/pages/quiz.html' => ['phone-mask.js', 'site-fab.js', 'quiz-page.js', 'callback-modal.js', 'cookie-consent.js'],
    'public/pages/quiz-step-2.html' => ['phone-mask.js', 'site-fab.js', 'quiz-step-2.js', 'callback-modal.js', 'cookie-consent.js'],
    'public/pages/quiz-step-3.html' => ['phone-mask.js', 'site-fab.js', 'quiz-step-3.js', 'callback-modal.js', 'cookie-consent.js'],
    'public/pages/quiz-step-3-tv.html' => ['phone-mask.js', 'site-fab.js', 'quiz-step-3-tv.js', 'callback-modal.js', 'cookie-consent.js'],
    'public/pages/request.html' => ['phone-mask.js', 'site-fab.js', 'callback-modal.js', 'cookie-consent.js'],
];

foreach ($map as $source => $config) {
    $path = $base.'/'.$source;
    if (! is_file($path)) {
        fwrite(STDERR, "Missing: $path\n");
        exit(1);
    }
    $html = file_get_contents($path);
    $main = extractMain($html);
    if ($main === null) {
        fwrite(STDERR, "No <main> in $source\n");
        exit(1);
    }

    $blade = wrapBlade($config['extends'], $config['section'], $main, $config['extra'] ?? []);

    $scriptLines = ["@push('scripts')"];
    foreach ($scripts[$source] as $file) {
        $scriptLines[] = "<script src=\"{{ asset('assets/js/$file') }}\"></script>";
    }
    $scriptLines[] = "@endpush";
    $blade .= "\n\n".implode("\n", $scriptLines)."\n";

    $out = $base.'/'.$config['view'];
    $dir = dirname($out);
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($out, $blade);
    echo "Wrote {$config['view']}\n";
}

echo "Done.\n";

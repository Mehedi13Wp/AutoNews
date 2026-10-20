<?php

// Repeatable local initialization; never overwrite existing configuration or keys.
$root = dirname(__DIR__);
if (! file_exists($root.'/.env')) {
    copy($root.'/.env.example', $root.'/.env');
}
$env = file_get_contents($root.'/.env');
if (preg_match('/^APP_KEY=\s*$/m', $env)) {
    $env = preg_replace('/^APP_KEY=\s*$/m', 'APP_KEY=base64:'.base64_encode(random_bytes(32)), $env, 1);
    file_put_contents($root.'/.env', $env);
}
if (! file_exists($root.'/database/database.sqlite')) {
    touch($root.'/database/database.sqlite');
}
echo "Local configuration and SQLite file prepared.\n";

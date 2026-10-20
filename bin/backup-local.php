<?php

// VACUUM INTO creates a consistent snapshot, including active WAL data.
$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$connection = config('database.default');
if ($connection !== 'sqlite') {
    fwrite(STDERR, "Automatic local update backups support SQLite only. Back up your configured database before running migrations manually.\n");
    exit(1);
}
$file = config('database.connections.sqlite.database');
if (! $file || $file === ':memory:' || ! is_file($file)) {
    fwrite(STDERR, "SQLite database not found. Update stopped before migrations.\n");
    exit(1);
}
$directory = $root.'/.autonews-backups';
if (! is_dir($directory) && ! mkdir($directory, 0700, true)) {
    fwrite(STDERR, "Could not create the local backup directory.\n");
    exit(1);
}
$destination = $directory.'/database-'.date('Ymd-His').'-'.bin2hex(random_bytes(3)).'.sqlite';
try {
    $source = new PDO('sqlite:'.$file, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $source->exec('PRAGMA busy_timeout = 5000');
    $source->exec('VACUUM INTO '.$source->quote($destination));
    $check = new PDO('sqlite:'.$destination);
    if ($check->query('PRAGMA quick_check')->fetchColumn() !== 'ok') {
        throw new RuntimeException('Database backup integrity check failed.');
    }
    echo 'Database backup saved: '.$destination."\n";
} catch (Throwable $error) {
    fwrite(STDERR, $error->getMessage()."\n");
    exit(1);
}

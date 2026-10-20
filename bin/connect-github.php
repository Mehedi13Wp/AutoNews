<?php

// One-time migration from an extracted ZIP to a Git checkout.
// Back up replaced source files first. Never copy or delete runtime data.
function command(array $arguments, ?string $cwd = null): string
{
    $process = proc_open($arguments, [0 => STDIN, 1 => ['pipe', 'w'], 2 => STDERR], $pipes, $cwd, null, ['bypass_shell' => true]);
    if (! is_resource($process)) {
        throw new RuntimeException('Could not start Git.');
    }
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $status = proc_close($process);
    if ($status !== 0) {
        throw new RuntimeException('Git command failed. No existing database or configuration has been changed.');
    }
    return $output;
}

function copyFileSafely(string $source, string $destination): void
{
    $parent = dirname($destination);
    if (! is_dir($parent) && ! mkdir($parent, 0777, true) && ! is_dir($parent)) {
        throw new RuntimeException('Could not create '.$parent);
    }
    if (! copy($source, $destination)) {
        throw new RuntimeException('Could not copy '.$destination);
    }
}

function protectedPath(string $path): bool
{
    $path = strtolower(str_replace('\\', '/', $path));
    if ($path === '.env.example') {
        return false;
    }
    return str_starts_with($path, '.env')
        || preg_match('#(^|/)(\.git|vendor|node_modules)(/|$)#', $path)
        || preg_match('#\.(sqlite|sqlite3|db)(-|$|\.)#', $path)
        || ($path === 'auth.json')
        || ($path === 'public/storage' || str_starts_with($path, 'public/storage/'))
        || (str_starts_with($path, 'storage/') && basename($path) !== '.gitignore');
}

try {
    $project = realpath($argv[1] ?? getcwd());
    if (! $project || ! is_file($project.'/artisan') || ! is_file($project.'/composer.json')) {
        throw new RuntimeException('Run this command from your extracted AutoNews project folder.');
    }
    if (file_exists($project.'/.git')) {
        throw new RuntimeException('This folder already has Git metadata. Use update.bat instead; existing Git history was left unchanged.');
    }
    $repository = getenv('AUTONEWS_CONNECT_REPOSITORY') ?: 'https://github.com/Mehedi13Wp/AutoNews.git';
    // Keep the clone on the project's drive so moving .git also works on Windows.
    $temporary = dirname($project).DIRECTORY_SEPARATOR.'.autonews-connect-'.bin2hex(random_bytes(6));
    echo "Downloading AutoNews from GitHub...\n";
    command(['git', 'clone', '--branch', 'main', '--single-branch', '--depth', '1', $repository, $temporary]);
    $files = array_filter(explode("\0", command(['git', 'ls-files', '-z'], $temporary)));
    foreach ($files as $file) {
        if (str_contains($file, '..') || str_starts_with($file, '/') || str_contains($file, ':') || protectedPath($file)) {
            throw new RuntimeException('Unsafe or protected repository path found. Existing files have not been changed.');
        }
        if (is_link($temporary.'/'.$file)) {
            throw new RuntimeException('Repository symlinks are not supported by this connector.');
        }
        $parts = explode('/', str_replace('\\', '/', $file));
        $target = $project;
        foreach ($parts as $part) {
            $target .= '/'.$part;
            if (is_link($target)) {
                throw new RuntimeException('Existing symlink encountered: '.$file.'. No files have been changed.');
            }
        }
    }
    $backup = dirname($project).DIRECTORY_SEPARATOR.basename($project).'-source-backup-'.date('Ymd-His').'-'.bin2hex(random_bytes(2));
    echo 'Backing up existing source files to: '.$backup."\n";
    foreach ($files as $file) {
        if (is_file($project.'/'.$file)) {
            copyFileSafely($project.'/'.$file, $backup.'/'.$file);
        }
    }
    echo "Installing updated source files. Your .env, database and uploads are preserved.\n";
    foreach ($files as $file) {
        copyFileSafely($temporary.'/'.$file, $project.'/'.$file);
    }
    if (! rename($temporary.'/.git', $project.'/.git')) {
        throw new RuntimeException('Could not attach Git metadata. Your source backup is at '.$backup);
    }
    // The attached main branch now tracks origin/main for fast-forward updates.
    echo "GitHub connection complete.\n";
    echo "Next, run: update.bat\n";
    echo 'Source backup: '.$backup."\n";
    // Only the temporary clone created above is eligible for cleanup.
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temporary, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $entry) {
        $entry->isDir() && ! $entry->isLink() ? rmdir($entry->getPathname()) : unlink($entry->getPathname());
    }
    rmdir($temporary);
} catch (Throwable $error) {
    fwrite(STDERR, 'Connection stopped: '.$error->getMessage()."\n");
    exit(1);
}

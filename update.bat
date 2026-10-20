@echo off
setlocal
cd /d "%~dp0"
if not exist .git (
 echo This folder is not connected to GitHub yet.
 echo Complete the one-time connection first.
 goto failed
)
echo [1/5] Checking for local source edits...
git diff --quiet
if errorlevel 1 (
 echo Local source changes found. Save them before updating.
 goto failed
)
git diff --cached --quiet
if errorlevel 1 (
 echo Staged source changes found. Save them before updating.
 goto failed
)
echo [2/5] Downloading the latest code...
git pull --ff-only
if errorlevel 1 goto failed
echo [3/5] Installing locked PHP dependencies...
call composer install --no-interaction --prefer-dist
if errorlevel 1 goto failed
echo [4/5] Backing up the database before migrations...
php bin/backup-local.php
if errorlevel 1 goto failed
php artisan migrate --force
if errorlevel 1 goto failed
echo [5/5] Refreshing page caches...
php artisan optimize:clear
if errorlevel 1 goto failed
echo.
echo Update complete! Refresh your dashboard with Ctrl+F5.
echo Your login, posts and uploads have been preserved.
echo Frontend assets are included; npm is not needed for updates.
exit /b 0
:failed
echo.
echo Update stopped. Please send the error shown above.
exit /b 1

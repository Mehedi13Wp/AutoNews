@echo off
cd /d "%~dp0"
if not exist artisan (
 echo Copy this file into your existing AutoNews project folder first.
 pause
 exit /b 1
)
php artisan view:clear
if errorlevel 1 (
 echo Could not clear the view cache. Please send the error shown above.
 pause
 exit /b 1
)
echo.
echo Design update ready. Keep your existing server running.
echo Open your dashboard in the browser and press Ctrl+F5.
echo Your database and login have not been changed.
pause

@echo off
echo ========================================
echo Starting Kalawag Barangay System
echo ========================================
echo.

REM Check if node_modules exists
if not exist "node_modules\" (
    echo [ERROR] node_modules not found!
    echo Please run: npm install
    echo.
    pause
    exit /b 1
)

echo [1/2] Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve --host=192.168.0.110 --port=8000"

echo [2/2] Starting Vite Dev Server...
timeout /t 2 /nobreak >nul
start "Vite Dev Server" cmd /k "npm run dev"

echo.
echo ========================================
echo Servers Starting...
echo ========================================
echo.
echo Laravel: http://192.168.0.110:8000
echo Vite:    http://192.168.0.110:5173
echo.
echo Wait for Vite to show "ready" message
echo Then open: http://192.168.0.110:8000
echo.
echo Press any key to open browser...
pause >nul

start http://192.168.0.110:8000

echo.
echo To stop servers, close the terminal windows
echo or press Ctrl+C in each window
echo.

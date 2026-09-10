@echo off
chcp 65001 >nul
title Dernek Sitesi
cd /d "%~dp0"

rem --- MySQL calisiyor mu? Degilse baslat ---
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
    echo MySQL baslatiliyor...
    start "MySQL (Dernek Sitesi)" /MIN "C:\xampp\mysql\bin\mysqld.exe" --defaults-file=C:/xampp/mysql/bin/my.ini --standalone
    echo MySQL'in hazir olmasi bekleniyor...
    timeout /t 5 /nobreak >nul
)

echo Site baslatiliyor: http://127.0.0.1:8000
echo Kapatmak icin bu pencerede Ctrl+C tuslayin.
start "" http://127.0.0.1:8000
"C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t "%~dp0public" "%~dp0router.php"

@echo off
chcp 65001 >nul
title Petio - link tam thoi
cd /d "%~dp0"

rem Tạo link công khai tạm thời (https://....trycloudflare.com) cho web Petio.
rem Cần bật MySQL trong Laragon trước. Đóng cửa sổ này (hoặc Ctrl+C) là link tắt.

set "PHP=E:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
set "CF=E:\laragon\bin\cloudflared\cloudflared.exe"
set PORT=8000

if not exist "%PHP%" (echo Khong tim thay PHP: %PHP% & pause & exit /b 1)
if not exist "%CF%" (echo Khong tim thay cloudflared: %CF% & pause & exit /b 1)

echo Dang chay web tai http://127.0.0.1:%PORT% ...
start "Petio server" /min "%PHP%" artisan serve --host=127.0.0.1 --port=%PORT%
timeout /t 3 /nobreak >nul

echo.
echo ================================================================
echo  Link tam thoi la dong co dang  https://xxxx.trycloudflare.com
echo  Gui link do cho nguoi khac. Dong cua so nay de tat link.
echo ================================================================
echo.
"%CF%" tunnel --no-autoupdate --url http://127.0.0.1:%PORT%

rem Tunnel tắt thì tắt luôn web server
taskkill /fi "WINDOWTITLE eq Petio server*" /t /f >nul 2>&1

@echo off
chcp 65001 >nul
cd /d "%~dp0"
python prepare_lab9.py
if errorlevel 1 python3 prepare_lab9.py
echo.
pause

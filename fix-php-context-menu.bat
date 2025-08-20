@echo off
echo Fixing PHP file context menu association...
echo This script must be run as Administrator

REM Create PHP file association
reg add "HKEY_CLASSES_ROOT\.php" /ve /d "phpfile" /f
reg add "HKEY_CLASSES_ROOT\phpfile" /ve /d "PHP File" /f

REM Add context menu to open in browser via localhost
reg add "HKEY_CLASSES_ROOT\phpfile\shell\openlocalhost" /ve /d "Open in Localhost" /f
reg add "HKEY_CLASSES_ROOT\phpfile\shell\openlocalhost\command" /ve /d "cmd /c start http://localhost/%%~1" /f

REM Add context menu to edit with VS Code
reg add "HKEY_CLASSES_ROOT\phpfile\shell\editwithcode" /ve /d "Edit with VS Code" /f
reg add "HKEY_CLASSES_ROOT\phpfile\shell\editwithcode\command" /ve /d "\"C:\Users\%USERNAME%\AppData\Local\Programs\Microsoft VS Code\Code.exe\" \"%%1\"" /f

echo.
echo Context menu entries added successfully!
echo Right-click any PHP file to see the new options.
pause

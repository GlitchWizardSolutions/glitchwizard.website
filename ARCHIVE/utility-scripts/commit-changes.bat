@echo off
cd /d "c:\xampp\htdocs\gws-universal-hybrid-app"
echo Current directory: %CD%
echo.
echo Checking git status...
git status
echo.
echo Adding all changes...
git add .
echo.
echo Committing changes...
git commit -m "Add accessibility-compliant hero section with teal offer form

- Implemented WCAG AA compliant text over hero images with multi-layer shadows
- Added teal 'Get Started' form section below hero image
- Created hero-offer-form.php handler for lead collection emails
- Enhanced all theme variants (default, bold, casual, high-contrast, subtle) with accessibility features
- Added responsive design and keyboard navigation support
- Created index-default.php template backup
- Improved form security with CSRF protection and honeypot fields"
echo.
echo Pushing to GitHub...
git push origin main
echo.
echo Git operations completed!
pause

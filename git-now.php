cd /d "d:\xampp\htdocs\gws-universal-hybrid-app\

# Check what's changed
git status

# Add all changes
git add .

# Commit with a message
git commit -m "This commit has mmenu issues"

# Push to GitHub
git push origin main

#check status again
git status   

Discard everyting local 
git fetch origin
git checkout main
git reset --hard origin/main
git clean -fd
git status
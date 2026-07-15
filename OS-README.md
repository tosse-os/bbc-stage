./go
npm run watch
npm run build


Danach:
git add .gitignore
git add -f web/app/plugins/polylang-string-importer
git status --short
git diff --cached --stat
git commit -m "Add Polylang string importer plugin"
git push origin main

Dann im Live-Workspace:
cd ~/sites/bbc-live-release
git pull --ff-only origin main

Prüfen:
find web/app/plugins/polylang-string-importer -maxdepth 3 -type f -print



git status --short

git add -A

git status --short
git diff --cached --stat
git diff --cached

git commit -m "Fix footer translations and update project documentation"
git push origin main


.release-tools/pull-and-build-upload.sh

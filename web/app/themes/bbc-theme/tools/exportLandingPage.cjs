const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');

const paths = {
  views: path.join(themeRoot, 'resources', 'views'),
  app: path.join(themeRoot, 'app'),
  css: path.join(themeRoot, 'resources', 'css'),
  js: path.join(themeRoot, 'resources', 'js')
};

function walk(dir, exclude) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap(e => {
    const p = path.join(dir, e.name);
    if (exclude && exclude(p)) return [];
    return e.isDirectory() ? walk(p, exclude) : p;
  });
}

function containsDashboard(p) {
  return p.toLowerCase().includes('dashboard');
}

function isExcludedName(p) {
  const name = path.basename(p).toLowerCase();
  return name.includes('off') || name.includes('copy') || /\d/.test(name);
}

const files = new Set();

const add = arr =>
  arr.forEach(f => {
    if (!containsDashboard(f) && !isExcludedName(f)) {
      files.add(f);
    }
  });

add(
  walk(path.join(paths.views, 'pages'))
    .filter(f => f.endsWith('.blade.php') && !path.basename(f).startsWith('page-dashboard'))
);

add(
  walk(path.join(paths.views, 'layouts'))
    .filter(f => f.endsWith('.blade.php') && !path.basename(f).startsWith('dashboard'))
);

add(walk(path.join(paths.views, 'partials')).filter(f => f.endsWith('.blade.php')));
add(walk(path.join(paths.views, 'sections')).filter(f => f.endsWith('.blade.php')));

add(
  fs.existsSync(paths.views)
    ? fs.readdirSync(paths.views, { withFileTypes: true })
      .filter(e => e.isFile() && e.name.endsWith('.blade.php') && !e.name.startsWith('page-dashboard'))
      .map(e => path.join(paths.views, e.name))
    : []
);

add(
  walk(paths.app, p => {
    const r = path.relative(paths.app, p).replace(/\\/g, '/');
    return r.startsWith('dashboard') || r.startsWith('analysis');
  })
);

add(walk(paths.css));
add(walk(paths.js));

const sorted = [...files].sort();
const relativeFiles = sorted.map(f => path.relative(themeRoot, f));

console.log('--- Landingpage Export ---');
console.log('Anzahl Dateien:', relativeFiles.length);
console.log('');
relativeFiles.forEach(f => console.log(' - ' + f));
console.log('--------------------------');

const out = [
  '=== LANDINGPAGE EXPORT ===',
  'Anzahl Dateien: ' + relativeFiles.length,
  '',
  'Dateistruktur:',
  ...relativeFiles.map(f => '- ' + f),
  '',
  '==========================',
  '',
  `Projekt:
WordPress Theme (Sage/Acorn) mit Blade Templates, ACF, Vite, Tailwind.
Fokus: Landingpage + Dashboard getrennt. Daten über Composer in Views.

Technik:
PHP (WordPress + Acorn), Blade, ACF, JS (Vanilla), Tailwind, Vite.

Verhalten ChatGPT:

1. Anweisungen:
- IMMER exakt befolgen, - KEINE Erweiterungen, - KEINE Interpretation, - KEINE zusätzlichen Optimierungen

2. Änderungen:
- NUR das ändern, was explizit verlangt ist, - minimal-invasiv arbeiten, - KEINE Refactorings ohne Auftrag

3. Kommentare:
- NIEMALS entfernen
- vorhandene Kommentare bleiben 1:1 erhalten

4. Code-Struktur:
- bestehende Struktur strikt beibehalten
- KEINE Umstrukturierung
- KEINE neuen Patterns einführen

5. Blade / ACF:
- nur get_field → Variablen ersetzen wenn verlangt
- KEINE zusätzliche Logik einbauen

6. Output:
- nur die angeforderten Dateien liefern
- vollständig, aber ohne zusätzliche Änderungen
- keine “Verbesserungen” außerhalb des Scopes

7. Grundregel:
- User entscheidet alles
- ChatGPT denkt nicht mit, erweitert nicht, optimiert nicht
- ChatGPT setzt exakt um

Ziel:
100% deterministisches Verhalten ohne Abweichung vom Auftrag.`,
  '',
  '==========================',
  ...sorted.map(f => {
    const content = fs.readFileSync(f, 'utf8');
    return `===== ${path.relative(themeRoot, f)} =====\n\n${content}\n`;
  })
].join('\n');

const filename = 'exportedLandingPageCode.txt';
fs.writeFileSync(filename, out);

console.log('');
console.log('Landingpage-Modul exportiert nach ' + filename);

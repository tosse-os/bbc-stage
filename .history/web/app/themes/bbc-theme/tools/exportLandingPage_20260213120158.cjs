const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');

const paths = {
  views: path.join(themeRoot, 'resources', 'views'),
  app: path.join(themeRoot, 'app'),
  css: path.join(themeRoot, 'resources', 'css'),
  js: path.join(themeRoot, 'resources', 'js')
};

function containsDashboard(p) {
  return p.toLowerCase().includes('dashboard');
}

function walk(dir) {
  if (!fs.existsSync(dir)) return [];
  return fs.readdirSync(dir, { withFileTypes: true }).flatMap(e => {
    const p = path.join(dir, e.name);
    if (containsDashboard(p)) return [];
    return e.isDirectory() ? walk(p) : p;
  });
}

const files = new Set();

const add = arr => arr.forEach(f => {
  if (!containsDashboard(f)) files.add(f);
});

add(walk(paths.views).filter(f => f.endsWith('.blade.php')));
add(walk(paths.app));
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
  ...sorted.map(f => {
    const content = fs.readFileSync(f, 'utf8');
    return `===== ${path.relative(themeRoot, f)} =====\n\n${content}\n`;
  })
].join('\n');

const filename = 'exportedLandingPageCode.txt';
fs.writeFileSync(filename, out);

console.log('');
console.log('Landingpage-Code exportiert nach ' + filename);

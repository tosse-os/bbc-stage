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

const files = new Set();

const add = arr => arr.forEach(f => files.add(f));

add(walk(path.join(paths.views, 'pages'))
  .filter(f => f.endsWith('.blade.php') && !path.basename(f).startsWith('page-dashboard'))
);

add(walk(path.join(paths.views, 'layouts'))
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

const out = [
  '=== LANDINGPAGE EXPORT ===',
  'files: ' + sorted.length,
  '',
  ...sorted.map(f =>
    `### ${path.relative(themeRoot, f)}\n${fs.readFileSync(f, 'utf8')}\n`
  )
].join('\n');

fs.writeFileSync('exportedLandingPageCode.txt', out);
console.log('Landingpage-Code exportiert');

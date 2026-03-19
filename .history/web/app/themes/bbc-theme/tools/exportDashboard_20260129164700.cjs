const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');
const appPath = path.join(themeRoot, 'app');
const viewsPath = path.join(themeRoot, 'resources', 'views');

const EXPORT_GROUPS = [
  {
    label: 'APP_DASHBOARD_LOGIC',
    base: appPath,
    match: file =>
      file.startsWith('dashboard-') ||
      file.startsWith('analysis') ||
      file === 'ajax.php' ||
      file === 'filters.php'
  },
  {
    label: 'DASHBOARD_LAYOUT',
    files: [
      path.join(viewsPath, 'layouts', 'dashboard.blade.php')
    ]
  },
  {
    label: 'DASHBOARD_VIEWS',
    base: path.join(viewsPath, 'dashboard'),
    recursive: true
  }
];

function getFilesRecursive(dir) {
  let results = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  entries.forEach(entry => {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      results = results.concat(getFilesRecursive(fullPath));
    }
    if (entry.isFile()) {
      results.push(fullPath);
    }
  });

  return results;
}

let collectedFiles = [];

/* APP files */
if (fs.existsSync(appPath)) {
  const appFiles = fs.readdirSync(appPath);
  appFiles.forEach(file => {
    EXPORT_GROUPS[0].match(file) &&
      collectedFiles.push(path.join(appPath, file));
  });
}

/* layout */
EXPORT_GROUPS[1].files.forEach(file => {
  if (fs.existsSync(file)) {
    collectedFiles.push(file);
  }
});

/* dashboard views */
if (fs.existsSync(EXPORT_GROUPS[2].base)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(EXPORT_GROUPS[2].base)
      .filter(f => f.endsWith('.blade.php'))
  );
}

collectedFiles = [...new Set(collectedFiles)].sort();

const output = collectedFiles.map(file => {
  const relative = path.relative(themeRoot, file);
  const content = fs.readFileSync(file, 'utf8');

  return [
    `===== ${relative} =====`,
    '',
    content,
    ''
  ].join('\n');
}).join('\n\n');

const filename = 'exportedDashboardCode.txt';
fs.writeFileSync(filename, output);
console.log('Dashboard-Modul exportiert nach ' + filename);

const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');
const appPath = path.join(themeRoot, 'app');
const viewsPath = path.join(themeRoot, 'resources', 'views');

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

/* APP_DASHBOARD_LOGIC */
if (fs.existsSync(appPath)) {
  fs.readdirSync(appPath).forEach(file => {
    if (
      file.startsWith('dashboard-') ||
      file.startsWith('analysis') ||
      file === 'ajax.php' ||
      file === 'filters.php'
    ) {
      collectedFiles.push(path.join(appPath, file));
    }
  });
}

/* DASHBOARD_LAYOUT */
const dashboardLayout = path.join(viewsPath, 'layouts', 'dashboard.blade.php');
if (fs.existsSync(dashboardLayout)) {
  collectedFiles.push(dashboardLayout);
}

/* DASHBOARD_VIEWS */
const dashboardViewsPath = path.join(viewsPath, 'dashboard');
if (fs.existsSync(dashboardViewsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(dashboardViewsPath).filter(f => f.endsWith('.blade.php'))
  );
}

/* DASHBOARD_ENTRY_VIEWS */
fs.readdirSync(viewsPath).forEach(file => {
  if (file.startsWith('page-dashboard') && file.endsWith('.blade.php')) {
    collectedFiles.push(path.join(viewsPath, file));
  }
});

collectedFiles = [...new Set(collectedFiles)].sort();

const relativeFiles = collectedFiles.map(file =>
  path.relative(themeRoot, file)
);

/* TERMINAL OUTPUT */
console.log('--- Dashboard Export ---');
console.log('Anzahl Dateien:', relativeFiles.length);
console.log('');
relativeFiles.forEach(file => console.log(' - ' + file));
console.log('------------------------');

const output = [
  '=== DASHBOARD EXPORT ===',
  'Anzahl Dateien: ' + relativeFiles.length,
  '',
  'Dateistruktur:',
  ...relativeFiles.map(f => '- ' + f),
  '',
  '========================',
  '',
  ...collectedFiles.map(file => {
    const content = fs.readFileSync(file, 'utf8');
    return `===== ${path.relative(themeRoot, file)} =====\n\n${content}\n`;
  })
].join('\n');

const filename = 'exportedDashboardCode.txt';
fs.writeFileSync(filename, output);
console.log('');
console.log('Dashboard-Modul exportiert nach ' + filename);

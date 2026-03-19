const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');
const appPath = path.join(themeRoot, 'app');
const viewsPath = path.join(themeRoot, 'resources', 'views');
const acfJsonPath = path.join(themeRoot, 'resources', 'acf-json');
const jsPath = path.join(themeRoot, 'resources', 'js');

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

const dashboardLayout = path.join(viewsPath, 'layouts', 'dashboard.blade.php');
if (fs.existsSync(dashboardLayout)) {
  collectedFiles.push(dashboardLayout);
}

const dashboardAuthLayout = path.join(
  viewsPath,
  'layouts',
  'dashboard-auth.blade.php'
);
if (fs.existsSync(dashboardAuthLayout)) {
  collectedFiles.push(dashboardAuthLayout);
}

const dashboardViewsPath = path.join(viewsPath, 'dashboard');
if (fs.existsSync(dashboardViewsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(dashboardViewsPath).filter(f => f.endsWith('.blade.php'))
  );
}

fs.readdirSync(viewsPath).forEach(file => {
  if (file.startsWith('page-dashboard') && file.endsWith('.blade.php')) {
    collectedFiles.push(path.join(viewsPath, file));
  }
});

if (fs.existsSync(acfJsonPath)) {
  fs.readdirSync(acfJsonPath).forEach(file => {
    if (file.startsWith('group_') && file.endsWith('.json')) {
      collectedFiles.push(path.join(acfJsonPath, file));
    }
  });
}

if (fs.existsSync(jsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(jsPath).filter(f => f.endsWith('.js'))
  );
}

collectedFiles = [...new Set(collectedFiles)].sort();

const relativeFiles = collectedFiles.map(file =>
  path.relative(themeRoot, file)
);

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

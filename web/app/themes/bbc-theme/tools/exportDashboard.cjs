const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');
const appPath = path.join(themeRoot, 'app');
const helpersPath = path.join(appPath, 'helper');
const viewsPath = path.join(themeRoot, 'resources', 'views');
const acfJsonPath = path.join(themeRoot, 'resources', 'acf-json');
const jsPath = path.join(themeRoot, 'resources', 'js');
const cssPath = path.join(themeRoot, 'resources', 'css');

const excludedIconsPath = path.join(viewsPath, 'dashboard', 'icons');

function shouldIgnore(filePath) {
  const name = path.basename(filePath).toLowerCase();
  const normalizedPath = path.normalize(filePath);

  if (normalizedPath.startsWith(path.normalize(excludedIconsPath))) {
    return true;
  }

  if (/\d/.test(name)) {
    return true;
  }

  if (name.includes('save')) {
    return true;
  }

  return name.includes('off') || name.includes('copy');
}

function getFilesRecursive(dir) {
  let results = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  entries.forEach(entry => {
    const fullPath = path.join(dir, entry.name);

    if (shouldIgnore(fullPath)) {
      return;
    }

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
    const fullPath = path.join(appPath, file);

    if (
      !shouldIgnore(fullPath) &&
      (
        file.startsWith('stripe-') ||
        file.startsWith('dashboard-') ||
        file.startsWith('analysis') ||
        file.startsWith('media-entry') ||
        file.includes('media') ||
        file === 'stripe.php' ||
        file === 'translations.php' ||
        file === 'ajax.php' ||
        file === 'filters.php' ||
        file === 'setup.php'
      )
    ) {
      collectedFiles.push(fullPath);
    }
  });
}

if (fs.existsSync(helpersPath)) {
  collectedFiles = collectedFiles.concat(getFilesRecursive(helpersPath));
}

const dashboardLayout = path.join(viewsPath, 'layouts', 'dashboard.blade.php');
if (fs.existsSync(dashboardLayout) && !shouldIgnore(dashboardLayout)) {
  collectedFiles.push(dashboardLayout);
}

const dashboardAuthLayout = path.join(
  viewsPath,
  'layouts',
  'dashboard-auth.blade.php'
);
if (fs.existsSync(dashboardAuthLayout) && !shouldIgnore(dashboardAuthLayout)) {
  collectedFiles.push(dashboardAuthLayout);
}

const dashboardViewsPath = path.join(viewsPath, 'dashboard');
if (fs.existsSync(dashboardViewsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(dashboardViewsPath).filter(f => f.endsWith('.blade.php'))
  );
}

const mediaViewsPath = path.join(viewsPath, 'dashboard', 'media');
if (fs.existsSync(mediaViewsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(mediaViewsPath).filter(f => f.endsWith('.blade.php'))
  );
}

const pagesViewsPath = path.join(viewsPath, 'pages');
if (fs.existsSync(pagesViewsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(pagesViewsPath).filter(f => f.endsWith('.blade.php'))
  );
}

fs.readdirSync(viewsPath).forEach(file => {
  const fullPath = path.join(viewsPath, file);

  if (
    !shouldIgnore(fullPath) &&
    (
      (file.startsWith('page-dashboard') ||
        file.startsWith('page-media') ||
        file.includes('media')) &&
      file.endsWith('.blade.php')
    )
  ) {
    collectedFiles.push(fullPath);
  }
});

if (fs.existsSync(acfJsonPath)) {
  fs.readdirSync(acfJsonPath).forEach(file => {
    const fullPath = path.join(acfJsonPath, file);

    if (
      !shouldIgnore(fullPath) &&
      file.startsWith('group_') &&
      file.endsWith('.json')
    ) {
      collectedFiles.push(fullPath);
    }
  });
}

if (fs.existsSync(jsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(jsPath).filter(f => f.endsWith('.js'))
  );
}

if (fs.existsSync(cssPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(cssPath).filter(f =>
      f.toLowerCase().includes('dashboard') ||
      f.toLowerCase().includes('admin') ||
      f.toLowerCase().includes('editor') ||
      f.toLowerCase().includes('media')
    )
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
  '',
  '=== CONTEXT ===',
  'System: Custom WordPress Dashboard (kein WP-Login Frontend)',
  'Auth: Custom via admin-post (login/register/password)',
  'Access Control: user_meta basiert (kein natives Capability System)',
  'Content: Custom Post Types (analysis, media_entry)',
  'Views: Blade Templates',
  'JS: Vanilla JS (AJAX basiert)',
  'Security: Nonce (teilweise implementiert)',
  'Routing: WordPress Templates + Redirects',
  'State: user_meta + URL Parameter',
  '================',
  '',
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
console.log('Dashboard + Media Entry Modul exportiert nach ' + filename);

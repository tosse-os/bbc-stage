const fs = require('fs');
const path = require('path');

const themeRoot = path.resolve(__dirname, '..');

const viewsBasePath = path.join(themeRoot, 'resources', 'views');
const appPath = path.join(themeRoot, 'app');
const appHelpersPath = path.join(appPath, 'Helpers');
const resourcesCssPath = path.join(themeRoot, 'resources', 'css');
const resourcesJsPath = path.join(themeRoot, 'resources', 'js');

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

const layoutsPath = path.join(viewsBasePath, 'layouts');
const pagesPath = path.join(viewsBasePath, 'pages');
const partialsPath = path.join(viewsBasePath, 'partials');
const sectionsPath = path.join(viewsBasePath, 'sections');

if (fs.existsSync(pagesPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(pagesPath)
      .filter(f => f.endsWith('.blade.php'))
      .filter(f => !path.basename(f).startsWith('page-dashboard'))
  );
}

if (fs.existsSync(layoutsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(layoutsPath).filter(f => f.endsWith('.blade.php'))
  );
}

if (fs.existsSync(partialsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(partialsPath).filter(f => f.endsWith('.blade.php'))
  );
}

if (fs.existsSync(sectionsPath)) {
  collectedFiles = collectedFiles.concat(
    getFilesRecursive(sectionsPath).filter(f => f.endsWith('.blade.php'))
  );
}

if (fs.existsSync(viewsBasePath)) {
  fs.readdirSync(viewsBasePath, { withFileTypes: true })
    .filter(e => e.isFile())
    .filter(e => e.name.endsWith('.blade.php'))
    .filter(e => !e.name.startsWith('page-dashboard'))
    .forEach(e => {
      collectedFiles.push(path.join(viewsBasePath, e.name));
    });
}

if (fs.existsSync(appPath)) {
  fs.readdirSync(appPath, { withFileTypes: true })
    .filter(e => e.isFile())
    .filter(e => !e.name.toLowerCase().startsWith('analysis'))
    .filter(e => !e.name.startsWith('Dashboard'))
    .forEach(e => {
      collectedFiles.push(path.join(appPath, e.name));
    });
}

if (fs.existsSync(appHelpersPath)) {
  collectedFiles = collectedFiles.concat(getFilesRecursive(appHelpersPath));
}

if (fs.existsSync(resourcesCssPath)) {
  collectedFiles = collectedFiles.concat(getFilesRecursive(resourcesCssPath));
}

if (fs.existsSync(resourcesJsPath)) {
  collectedFiles = collectedFiles.concat(getFilesRecursive(resourcesJsPath));
}

collectedFiles = [...new Set(collectedFiles)].sort();

const relativeFiles = collectedFiles.map(file =>
  path.relative(themeRoot, file)
);

console.log('--- Landingpage Export ---');
console.log('Anzahl Dateien:', relativeFiles.length);
console.log('');
relativeFiles.forEach(file => console.log(' - ' + file));
console.log('--------------------------');

const output = [
  '=== LANDINGPAGE EXPORT ===',
  'Anzahl Dateien: ' + relativeFiles.length,
  '',
  'Dateistruktur:',
  ...relativeFiles.map(f => '- ' + f),
  '',
  '==========================',
  '',
  ...collectedFiles.map(file => {
    const content = fs.readFileSync(file, 'utf8');
    return `===== ${path.relative(themeRoot, file)} =====\n\n${content}\n`;
  })
].join('\n');

const filename = 'exportedLandingPageCode.txt';
fs.writeFileSync(filename, output);

console.log('');
console.log('Landingpage-Code exportiert nach ' + filename);

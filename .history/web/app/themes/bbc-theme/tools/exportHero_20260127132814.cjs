// exportHero.js
const fs = require('fs');
const path = require('path');

const viewsBasePath = path.resolve(__dirname, '../resources/views');

function getAllBladeFiles(dir) {
  let results = [];
  const list = fs.readdirSync(dir, { withFileTypes: true });

  list.forEach(entry => {
    const fullPath = path.join(dir, entry.name);

    if (entry.isDirectory()) {
      results = results.concat(getAllBladeFiles(fullPath));
    }

    if (entry.isFile() && entry.name.endsWith('.blade.php')) {
      results.push(fullPath);
    }
  });

  return results;
}

let files = [];

const layoutsPath = path.join(viewsBasePath, 'layouts');
const partialsPath = path.join(viewsBasePath, 'partials');

if (fs.existsSync(layoutsPath)) {
  files = files.concat(getAllBladeFiles(layoutsPath));
}

if (fs.existsSync(partialsPath)) {
  files = files.concat(getAllBladeFiles(partialsPath));
}

const rootBlades = fs.readdirSync(viewsBasePath, { withFileTypes: true })
  .filter(entry => entry.isFile() && entry.name.endsWith('.blade.php'))
  .map(entry => path.join(viewsBasePath, entry.name));

files = files.concat(rootBlades);

const output = files.map(file => {
  const content = fs.readFileSync(file, 'utf-8');
  return `===== ${file.replace(viewsBasePath + path.sep, '')} =====\n\n${content}\n`;
}).join('\n\n');

const filename = 'exported-views-blades.txt';
fs.writeFileSync(filename, output);
console.log('Dateien exportiert nach ' + filename);

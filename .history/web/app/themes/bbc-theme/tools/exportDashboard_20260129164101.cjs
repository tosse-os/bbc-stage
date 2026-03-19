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

if (!fs.existsSync(viewsBasePath)) {
  console.error('resources/views nicht gefunden');
  process.exit(1);
}

let files = getAllBladeFiles(viewsBasePath);

files.sort((a, b) => {
  const order = ['layouts', 'dashboard'];
  const aIndex = order.findIndex(p => a.includes(path.sep + p + path.sep));
  const bIndex = order.findIndex(p => b.includes(path.sep + p + path.sep));

  if (aIndex !== bIndex) {
    return (aIndex === -1 ? 99 : aIndex) - (bIndex === -1 ? 99 : bIndex);
  }

  return a.localeCompare(b);
});

const output = files.map(file => {
  const relativePath = file.replace(viewsBasePath + path.sep, '');
  const content = fs.readFileSync(file, 'utf-8');

  return `===== ${relativePath} =====\n\n${content}\n`;
}).join('\n\n');

const filename = 'exportedDashboard.txt';
fs.writeFileSync(filename, output);
console.log('Dateien exportiert nach ' + filename);

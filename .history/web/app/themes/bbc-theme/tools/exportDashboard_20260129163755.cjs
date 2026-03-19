const fs = require('fs');
const path = require('path');

const viewsBasePath = path.resolve(__dirname, '../resources/views');
const outputFile = path.resolve(__dirname, '../exported-dashboard-views.txt');

function getAllBladeFiles(dir) {
  let results = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  entries.forEach(entry => {
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

let allFiles = getAllBladeFiles(viewsBasePath);

allFiles.sort((a, b) => {
  const order = ['layouts', 'dashboard'];
  const aIndex = order.findIndex(p => a.includes(path.sep + p + path.sep));
  const bIndex = order.findIndex(p => b.includes(path.sep + p + path.sep));

  if (aIndex !== bIndex) {
    return (aIndex === -1 ? 99 : aIndex) - (bIndex === -1 ? 99 : bIndex);
  }

  return a.localeCompare(b);
});

const output = allFiles.map(file => {
  const relativePath = file.replace(viewsBasePath + path.sep, '');
  const content = fs.readFileSync(file, 'utf8');

  return [
    '===== ' + relativePath + ' =====',
    '',
    content,
    ''
  ].join('\n');
}).join('\n\n');

fs.writeFileSync(outputFile, output);
console.log('Export fertig:', outputFile);

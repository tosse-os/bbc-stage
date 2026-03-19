const fs = require('fs');
const path = require('path');

const viewsBasePath = path.resolve(__dirname, '../resources/views');

const DASHBOARD_PATHS = [
  path.join(viewsBasePath, 'dashboard'),
  path.join(viewsBasePath, 'layouts', 'dashboard.blade.php'),
];

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

let files = [];

/* dashboard/** */
if (fs.existsSync(DASHBOARD_PATHS[0])) {
  files = files.concat(getAllBladeFiles(DASHBOARD_PATHS[0]));
}

/* layouts/dashboard.blade.php */
if (fs.existsSync(DASHBOARD_PATHS[1])) {
  files.push(DASHBOARD_PATHS[1]);
}

files = [...new Set(files)].sort();

const output = files.map(file => {
  const relativePath = file.replace(viewsBasePath + path.sep, '');
  const content = fs.readFileSync(file, 'utf-8');

  return `===== ${relativePath} =====\n\n${content}\n`;
}).join('\n\n');

const filename = 'exportedDashboardCode.txt';
fs.writeFileSync(filename, output);
console.log('Dashboard-Views exportiert nach ' + filename);

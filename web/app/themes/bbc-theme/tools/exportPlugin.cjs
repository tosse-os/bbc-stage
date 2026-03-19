// exportPlugin.cjs
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '../../../plugins/gsm-references');

function getAllFiles(dir) {
  let list = [];
  fs.readdirSync(dir).forEach(name => {
    const full = path.join(dir, name);
    const stat = fs.statSync(full);
    if (stat.isDirectory()) {
      list = list.concat(getAllFiles(full));
    } else {
      list.push(full);
    }
  });
  return list;
}

const files = getAllFiles(ROOT);

const output = files
  .map(file => {
    const content = fs.readFileSync(file, 'utf-8');
    const relative = path.relative(ROOT, file);
    return `===== ${relative} =====\n\n${content}\n`;
  })
  .join('\n\n');

const filename = path.resolve(__dirname, 'gsm-references-export.txt');
fs.writeFileSync(filename, output);

console.log('Plugin exportiert nach ' + filename);

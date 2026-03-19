// exportFiles.js
const fs = require('fs');
const path = require('path');

function getScssFiles(dir) {
  return fs.readdirSync(dir).flatMap(file => {
    const full = path.join(dir, file);
    return fs.statSync(full).isDirectory()
      ? getScssFiles(full)
      : full.endsWith('.scss') ? [full] : [];
  });
}

const baseFiles = [
  '../package.json',
  '../resources/css/app.css',
  '../resources/js/app.js',
  //'../resources/js/contact-modal.js',
  '../tailwind.config.js',
  '../package.json',
  '../vite.config.js',
];

//const scssFiles = getScssFiles('../resources/scss');

const files = [...baseFiles];

const output = files.map(file => {
  const content = fs.readFileSync(file, 'utf-8');
  return `===== ${file} =====\n\n${content}\n`;
}).join('\n\n');

const filename = 'exported-config.txt';
fs.writeFileSync(filename, output);
console.log('Dateien exportiert nach ' + filename);

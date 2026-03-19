// exportFiles.js
const fs = require('fs');
const path = require('path');

const baseFiles = [
  '../resources/views/partials/hero.blade.php',
  '../resources/views/front-page.blade.php',
  '../resources/js/app.js',
  '../functions.php',
];


const files = [...baseFiles];

const output = files.map(file => {
  const content = fs.readFileSync(file, 'utf-8');
  return `===== ${file} =====\n\n${content}\n`;
}).join('\n\n');

const filename = 'exported-hero.txt';
fs.writeFileSync(filename, output);
console.log('Dateien exportiert nach ' + filename);

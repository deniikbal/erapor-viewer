// Font Converter Utility for jsPDF
// This script helps convert TTF fonts to base64 format for jsPDF

// Instructions:
// 1. Download DejaVu Sans fonts from https://dejavu-fonts.github.io/
// 2. Place TTF files in public/fonts/dejavu-sans/
// 3. Use online converter or Node.js script to convert to base64
// 4. Save the converted fonts in this directory

// Example structure after conversion:
/*
resources/fonts/dejavu-sans/
├── DejaVuSans-normal.js
├── DejaVuSans-bold.js  
├── DejaVuSansCondensed-normal.js
└── DejaVuSansCondensed-bold.js
*/

// Each converted file should look like this:
/*
// DejaVuSans-normal.js
export const DejaVuSansNormal = {
  fontName: 'DejaVuSans',
  fontStyle: 'normal',
  fontData: 'data:font/truetype;charset=utf-8;base64,AAABAA...' // base64 data
};
*/

console.log('Font converter utility loaded');
console.log('Please place your DejaVu Sans TTF files in public/fonts/dejavu-sans/');
console.log('Then convert them to base64 format for jsPDF usage');
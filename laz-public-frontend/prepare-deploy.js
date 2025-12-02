const fs = require('fs');
const path = require('path');

function copyDir(src, dest) {
    if (!fs.existsSync(dest)) {
        fs.mkdirSync(dest, { recursive: true });
    }
    
    const entries = fs.readdirSync(src, { withFileTypes: true });

    for (const entry of entries) {
        const srcPath = path.join(src, entry.name);
        const destPath = path.join(dest, entry.name);

        if (entry.isDirectory()) {
            copyDir(srcPath, destPath);
        } else {
            fs.copyFileSync(srcPath, destPath);
        }
    }
}

console.log('🏗️  Preparing standalone build for deployment...');

const standaloneDir = path.join(__dirname, '.next', 'standalone');
const staticSrc = path.join(__dirname, '.next', 'static');
const staticDest = path.join(standaloneDir, '.next', 'static');
const publicSrc = path.join(__dirname, 'public');
const publicDest = path.join(standaloneDir, 'public');

if (!fs.existsSync(standaloneDir)) {
    console.error('❌ Error: .next/standalone directory not found. Did you run "next build" with output: "standalone"?');
    process.exit(1);
}

// 1. Copy .next/static
if (fs.existsSync(staticSrc)) {
    console.log('📂 Copying static assets...');
    copyDir(staticSrc, staticDest);
} else {
    console.warn('⚠️  Warning: .next/static not found.');
}

// 2. Copy public folder
if (fs.existsSync(publicSrc)) {
    console.log('📂 Copying public assets...');
    copyDir(publicSrc, publicDest);
} else {
    console.warn('⚠️  Warning: public folder not found.');
}

console.log('✅ Build prepared in .next/standalone');
console.log('👉 Upload the contents of ".next/standalone" to your hosting root.');

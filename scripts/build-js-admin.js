'use strict';

const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');

// Files concatenated in order — matches original gulp build
const sources = [
    'assets/js/source/admin/simple-locator-admin.modals.js',
	'assets/js/source/admin/simple-locator-admin.general-settings.js',
	'assets/js/source/admin/simple-locator-admin.post-type.js',
	'assets/js/source/admin/simple-locator-admin.post-edit.js',
	'assets/js/source/admin/simple-locator-admin.default-map.js',
	'assets/js/source/admin/simple-locator-admin.results-display.js',
	'assets/js/source/admin/simple-locator-admin.map-display.js',
	'assets/js/source/admin/simple-locator-admin.search-history.js',
	'assets/js/source/admin/simple-locator-admin.import-test.js',
	'assets/js/source/admin/simple-locator-admin.import-upload.js',
	'assets/js/source/admin/simple-locator-admin.import-column-map.js',
	'assets/js/source/admin/simple-locator-admin.import-import.js',
	'assets/js/source/admin/simple-locator-admin.listing-map.js',
	'assets/js/source/admin/simple-locator-admin.export-templates.js',
	'assets/js/source/admin/simple-locator-admin.quick-edit.js',
	'assets/js/source/admin/simple-locator-admin.factory.js'
];

async function buildJSAdmin() {
    const combined = sources
        .map(f => fs.readFileSync(path.join(ROOT, f), 'utf8'))
        .join('\n');

    const result = await esbuild.transform(combined, {
        minify: true,
    });

    fs.writeFileSync(path.join(ROOT, 'assets/js/simple-locator-admin.min.js'), result.code);
    console.log('Built admin scripts');
}

buildJSAdmin().catch(err => {
    console.error(err.message || err);
    process.exit(1);
});

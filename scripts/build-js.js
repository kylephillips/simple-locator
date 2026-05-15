'use strict';

const esbuild = require('esbuild');
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');

// Files concatenated in order — matches original gulp build
const sources = [
	'assets/js/source/simple-locator.functions-deprecated.js',
	'assets/js/source/simple-locator.utilities.js',
	'assets/js/source/simple-locator.single-location.js',
	'assets/js/source/simple-locator.form.js',
	'assets/js/source/simple-locator.geocoder.js',
	'assets/js/source/simple-locator.results-map-non-ajax.js',
	'assets/js/source/simple-locator.results-map.js',
	'assets/js/source/simple-locator.results-list.js',
	'assets/js/source/simple-locator.infowindow-open.js',
	'assets/js/source/simple-locator.geolocation.js',
	'assets/js/source/simple-locator.places-autocomplete.js',
	'assets/js/source/simple-locator.default-map.js',
	'assets/js/source/simple-locator.all-locations.js',
	'assets/js/source/simple-locator.errors.js',
	'assets/js/source/simple-locator.factory.js'
];

async function buildJS() {
	const combined = sources
		.map(f => fs.readFileSync(path.join(ROOT, f), 'utf8'))
		.join('\n');

	const result = await esbuild.transform(combined, {
		minify: true,
	});

	fs.writeFileSync(path.join(ROOT, 'assets/js/simple-locator.min.js'), result.code);
	console.log('Built front end scripts');
}

buildJS().catch(err => {
	console.error(err.message || err);
	process.exit(1);
});

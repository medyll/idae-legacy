

self.addEventListener('install', function(event) {
	console.log('red')
	event.waitUntil(
		// fetchStuffAndInitDatabases();
	);
});

self.addEventListener('activate', function(event) {
	// You're good to go!
});


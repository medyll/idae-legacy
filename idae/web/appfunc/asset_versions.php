<?php
declare(strict_types=1);
/**
 * Per-file cache-busting for the bag.js asset loader.
 *
 * Date: 2026-08-07
 *
 * main_bag.js used to append one `?v=<Date.now()>` to every script/stylesheet
 * on every page load — a fresh query string each time defeats both the
 * browser HTTP cache and bag.js's own IndexedDB blob cache, so every visit
 * re-downloads the entire ~90-file boot regardless of whether anything
 * changed. This builds a {relative_path: mtime} manifest instead: a file's
 * cache-buster only changes when the file's content does, so bag.js serves
 * unchanged files from its cache and the network only sees what actually
 * changed since the last deploy.
 */

/**
 * Recursively collects {relative_path: mtime} for files with the given
 * extensions under each of $subdirs (relative to $baseDir).
 *
 * @param string $baseDir Absolute path to resolve $subdirs against (SITEPATH).
 * @param string[] $subdirs Directories to scan, relative to $baseDir (e.g. 'javascript', 'css').
 * @param string[] $extensions File extensions to include, without the dot (e.g. 'js', 'css').
 * @return array<string, int> Map of forward-slash relative path (matching how
 *   main_bag.js references files, e.g. "javascript/app/app.js") to Unix mtime.
 */
function build_asset_version_manifest(string $baseDir, array $extensions, array $subdirs = []): array
{
	$manifest = [];
	$extensions = array_map('strtolower', $extensions);

	foreach ($subdirs as $subdir) {
		$root = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR . $subdir;
		if (!is_dir($root)) {
			continue;
		}

		try {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
			);
		} catch (Exception $e) {
			error_log('build_asset_version_manifest: cannot scan ' . $root . ' — ' . $e->getMessage());
			continue;
		}

		foreach ($iterator as $file) {
			if (!$file->isFile()) {
				continue;
			}
			$ext = strtolower($file->getExtension());
			if (!in_array($ext, $extensions, true)) {
				continue;
			}

			$relative = substr($file->getPathname(), strlen(rtrim($baseDir, '/\\')) + 1);
			$relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
			$mtime = $file->getMTime();
			if ($mtime !== false) {
				$manifest[$relative] = $mtime;
			}
		}
	}

	return $manifest;
}

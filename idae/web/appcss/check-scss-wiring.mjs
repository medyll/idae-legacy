// check-scss-wiring.mjs — fails the CSS build when a partial in scss/ is not
// reachable from main.scss via @use/@import/@forward.
// Context: index.php only loads dist/main.css; a partial missing from the
// @use graph silently disappears from the app (see 2026-07-14 windowGui bug).
// Modified: 2026-07-14
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const SCSS_ROOT = path.join(path.dirname(fileURLToPath(import.meta.url)), 'scss');
const ENTRY = 'main.scss';

// Partials that are intentionally not wired into main.scss.
const IGNORE = [
    'css/_flex-old.scss',      // superseded by css/_flex.scss, kept for reference
    'skin/_skin-seven.scss',   // alternate skin, not active
    // Swiper vendor base: was never wired into less/main.less either; only
    // _swiper-override.scss ships. Remove from IGNORE if base styles are needed.
    'vendor/swiper/_core.scss',
    'vendor/swiper/_effects.scss',
    'vendor/swiper/_mixins.scss',
    'vendor/swiper/_navigation-f7.scss',
    'vendor/swiper/_navigation.scss',
    'vendor/swiper/_preloader-f7.scss',
    'vendor/swiper/_preloader.scss',
    'vendor/swiper/_scrollbar.scss',
    'vendor/swiper/_swiper.scss',
    'vendor/swiper/_zoom.scss',
];

function listScssFiles(dir) {
    const out = [];
    for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) out.push(...listScssFiles(p));
        else if (e.name.endsWith('.scss')) out.push(p);
    }
    return out;
}

// Resolve a @use/@import target the way dart-sass does: relative to the
// importing file, trying `_name.scss`, `name.scss`, `name/_index.scss`.
function resolveTarget(fromFile, target) {
    const base = path.resolve(path.dirname(fromFile), target);
    const dir = path.dirname(base);
    const name = path.basename(base);
    const candidates = [
        path.join(dir, `_${name}.scss`),
        path.join(dir, `${name}.scss`),
        path.join(base, '_index.scss'),
        path.join(base, 'index.scss'),
    ];
    return candidates.find(fs.existsSync) ?? null;
}

const USE_RE = /@(?:use|import|forward)\s+["']([^"']+)["']/g;

const reached = new Set();
const queue = [path.join(SCSS_ROOT, ENTRY)];
while (queue.length) {
    const file = queue.pop();
    const key = path.normalize(file);
    if (reached.has(key)) continue;
    reached.add(key);
    const src = fs.readFileSync(file, 'utf8');
    for (const m of src.matchAll(USE_RE)) {
        if (m[1].startsWith('sass:')) continue; // built-in modules
        const resolved = resolveTarget(file, m[1]);
        if (resolved) queue.push(resolved);
        else console.warn(`WARN: ${path.relative(SCSS_ROOT, file)} references unresolvable "${m[1]}"`);
    }
}

const all = listScssFiles(SCSS_ROOT);
const ignored = new Set(IGNORE.map(f => path.normalize(path.join(SCSS_ROOT, f))));
const orphans = all.filter(f => {
    const key = path.normalize(f);
    return !reached.has(key) && !ignored.has(key);
});

if (orphans.length) {
    console.error('SCSS wiring check FAILED — partials not reachable from main.scss:');
    for (const f of orphans) console.error('  - ' + path.relative(SCSS_ROOT, f).replace(/\\/g, '/'));
    console.error('Add a @use to main.scss (or list the file in IGNORE inside check-scss-wiring.mjs).');
    process.exit(1);
}
console.log(`SCSS wiring check OK — ${reached.size} files reachable, ${IGNORE.length} ignored.`);

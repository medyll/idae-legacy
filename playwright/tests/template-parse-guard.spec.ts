/**
 * Every template's inline JavaScript must parse.
 *
 * Companion to template-api-guard.spec.ts. That one asks "does the API this
 * template calls exist at runtime"; this one asks the cheaper question first:
 * "is this even JavaScript". Four templates failed it on 2026-08-11, three of
 * them for the same reason:
 *
 *     loadModule('app_document/app_document'_liste_spy', ...)
 *
 * A string literal immediately followed by an identifier. A `<script>` block
 * containing that never executes at all -- the browser discards the whole
 * block. document_liste_drop.php polls document_liste_spy.php every 30s and
 * *both* files carried the broken quote, so both ends of the document-list
 * live refresh had been dead for as long as the code existed. Nobody noticed,
 * because a parse error in an inline block is silent unless you have the
 * console open on that exact screen.
 *
 * No browser involved: this reads files and shells out to `node --check`.
 * It lives in the Playwright suite so it runs with everything else.
 *
 * Limits worth knowing. PHP short-echo tags become a placeholder identifier
 * and PHP blocks are stripped, so a template that opens a brace inside
 * `<?php if ?>` and closes it in the `else` will read as unbalanced here. No
 * such case exists in the tree today; if one appears, add it to ALLOWED with
 * a note rather than weakening the check.
 */
import { test, expect } from '@playwright/test';
import { readFileSync, readdirSync, statSync, writeFileSync } from 'node:fs';
import { execFileSync } from 'node:child_process';
import { join, resolve, sep, dirname } from 'node:path';
import { tmpdir } from 'node:os';

const WEB_ROOT = resolve(process.cwd(), '../idae/web');
const TEMPLATE_EXT = ['.php', '.latte', '.tpl'];

/** Third-party trees and compiled output — not ours to fix. */
const SKIP_DIRS = new Set(['vendor', 'adodb', 'flotr', 'node_modules', '.git', 'nbproject', 'cache']);

/** Templates knowingly exempt. Empty, and meant to stay that way. */
const ALLOWED = new Set<string>([]);

function walk(dir: string, out: string[] = []): string[] {
  for (const entry of readdirSync(dir)) {
    if (SKIP_DIRS.has(entry)) continue;
    const full = join(dir, entry);
    let st;
    try { st = statSync(full); } catch { continue; }
    if (st.isDirectory()) walk(full, out);
    else if (TEMPLATE_EXT.some((e) => entry.endsWith(e))) out.push(full);
  }
  return out;
}

/** Inline <script> bodies, PHP stripped, joined into one checkable unit. */
function inlineJs(source: string): string {
  // Skip a leading PHP docblock: several files describe themselves as
  // "outputs <script> ..." in prose, which would otherwise be extracted.
  const afterOpen = source.indexOf('?>');
  const scan = afterOpen === -1 ? source : source.slice(afterOpen + 2);

  // Skip <script src=...> — external files, nothing inline to check.
  const blocks = [...scan.matchAll(/<script(?![^>]*\bsrc=)[^>]*>([\s\S]*?)<\/script\s*>/gi)]
    .map((m) => m[1]);
  if (!blocks.length) return '';

  return blocks.join('\n;\n')
    .replace(/<\?php[\s\S]*?\?>/g, '')
    .replace(/<\?=[\s\S]*?\?>/g, 'PH')
    .replace(/<\?[\s\S]*?\?>/g, '');
}

test('template parse guard: every template\'s inline JavaScript parses', () => {
  const probe = join(tmpdir(), 'idae-template-parse-guard.js');
  const failures: string[] = [];
  let checked = 0;

  for (const file of walk(WEB_ROOT)) {
    const rel = file.slice(WEB_ROOT.length + 1).split(sep).join('/');
    if (ALLOWED.has(rel)) continue;

    let source: string;
    try { source = readFileSync(file, 'utf8'); } catch { continue; }
    if (!source.includes('<script')) continue;

    const js = inlineJs(source);
    if (!js.trim()) continue;

    checked++;
    writeFileSync(probe, js, 'utf8');
    try {
      execFileSync(process.execPath, ['--check', probe], { stdio: 'pipe' });
    } catch (e: any) {
      const stderr = String(e.stderr ?? '');
      const line = stderr.split('\n').find((l) => /Error/i.test(l)) ?? stderr.trim();
      failures.push(`  ${rel}\n      ${line.trim().slice(0, 160)}`);
    }
  }

  expect(checked, 'walked the tree but found no inline script — the walk is broken')
    .toBeGreaterThan(50);

  expect(
    failures.length,
    `inline <script> that does not parse (the browser discards the whole block):\n${failures.join('\n')}`
  ).toBe(0);
});

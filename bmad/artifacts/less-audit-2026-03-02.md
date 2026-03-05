# S1-07 — LESS File Audit

**Date:** 2026-03-02  
**Story:** S1-07, Sprint 01

---

## Import Tree

```
appcss/less/main.less  (@location_fragment = 'css')
├── @import "css/vars.less"             — design tokens
├── @import "css/app_fabric.less"       — mixin library
├── @import "css/style.less"            — base utility styles
│   ├── @import "vars.less"             (re-import relative to css/)
│   └── @import "app_fabric.less"       (re-import relative to css/)
├── @import "css/table.less"            — table layout styles
│   ├── @import "vars.less"
│   └── @import "app_fabric.less"
└── @import "css/css/global-tokens.less"  ⚠️ BUG (see below)

Other entry points (standalone, reference vars/app_fabric internally):
├── appcss/less/appsite.less
├── appcss/less/datatable.less
├── appcss/less/interface.less
├── appcss/less/notifier.less
├── appcss/less/pikaday.less
├── appcss/less/sortable.less
├── appcss/less/swiper-override.less
├── appcss/less/skin/skin_default.less
├── appcss/less/skin/skin_seven.less
└── appcss/less/app_window_gui/app_window_gui.less
```

---

## `@{location_fragment}` Usages in `main.less`

| Line | Expression | Resolved path (location_fragment = 'css') | Status |
|------|-----------|-------------------------------------------|--------|
| 1 | `@location_fragment: 'css'` | variable definition | OK |
| 3 | `@import "@{location_fragment}/vars.less"` | `css/vars.less` | ✅ valid |
| 5 | `@import "@{location_fragment}/app_fabric.less"` | `css/app_fabric.less` | ✅ valid |
| 6 | `@import "@{location_fragment}/style.less"` | `css/style.less` | ✅ valid |
| 8 | `@import "@{location_fragment}/table.less"` | `css/table.less` | ✅ valid |
| 204 | `@import "@{location_fragment}/css/global-tokens.less"` | `css/css/global-tokens.less` | ⚠️ **BUG** |

### ⚠️ BUG — line 204
`@import "@{location_fragment}/css/global-tokens.less"` resolves to `css/css/global-tokens.less` which does not exist.  
The file is at `css/global-tokens.less`. The correct import should be:
```less
@import "@{location_fragment}/global-tokens.less";
```
**Fix target:** Sprint 02 (full LESS migration or direct main.less correction).

---

## Mixins in `app_fabric.less`

All mixins are zero-argument parameterless helpers:

| Mixin | Output |
|-------|--------|
| `.border4()` | `border: 1px solid @border_color` |
| `.borderb()` | `border-bottom: 1px solid @border_color` |
| `.borderl()` | `border-left: 1px solid @border_color` |
| `.borderr()` | `border-right: 1px solid @border_color` |
| `.bordert()` | `border-top: 1px solid @border_color` |
| `.bordertb()` | shorthand 4-side (top+bottom only) |
| `.borderu()` | border bottom/left/right |
| `.bordertlb()` | border top/left/bottom |
| `.margin()` | `margin: 0.25em` |
| `.marginb()` | `margin-bottom: 0.25em` |
| `.margint()` | `margin-top: 0.25em` |
| `.margin_more()` | `margin: 0.5em` |
| `.margin_moreb()` | `margin-bottom: 0.5em` |
| `.margin_moret()` | `margin-top: 0.5em` |
| `.padding()` | `padding: 0.25em` |
| `.paddingt()` | `padding-top: 0.25em` |
| `.paddingb()` | `padding-bottom: 0.25em` |
| `.paddingl()` | `padding-left: 0.25em` |
| `.paddingr()` | `padding-right: 0.25em` |
| `.padding_more()` | `padding: 0.5em` |
| `.padding_morel()` | `padding-left: 0.5em` |
| `.padding_morer()` | `padding-right: 0.5em` |
| `.absolute()` | `position: absolute` |
| `.alignright()` | `text-align: right` |
| `.alignleft()` | `text-align: left` |
| `.aligntop()` | `vertical-align: text-top` |
| `.alignmiddle()` | `vertical-align: middle` |
| `.alignbottom()` | `vertical-align: text-bottom` |

---

## Sprint 02 LESS Backlog

- Fix `global-tokens.less` import bug in `main.less`
- Port remaining LESS files (appsite, datatable, interface, notifier, pikaday, sortable, swiper-override, skins, app_window_gui)
- Replace dynamic `@{location_fragment}` pattern with static SCSS `@use` imports

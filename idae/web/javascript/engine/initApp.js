/**
 * Modified: 2026-08-11
 *
 * This used to install an Ajax.Responders pair on top of `new
 * selfObservers('body')`: an onCreate that aborted the previous in-flight
 * request of the same `onlyLatestOfClass`, and an onComplete that ran
 * afterAjaxCall() on the response container and fired 'content:loaded' on it.
 *
 * Both were removed with the Ajax namespace itself. They were unreachable
 * long before that: Prototype's
 * Responders only fire for requests created through Ajax.Request/Ajax.Updater,
 * and the phase 5 migrations left no `new Ajax.*` anywhere outside vendor/ and
 * flotr/'s own bundled Prototype 1.6. Nothing set `onlyLatestOfClass` either --
 * it appeared in this file and nowhere else, so the abort branch never had a
 * producer to abort.
 *
 * Nothing is lost with them:
 *   - 'content:loaded' is fired directly by the native replacements that took
 *     over the request paths (app_socket.js, app_window.js, engine/methods.js).
 *   - afterAjaxCall() is called from those same places.
 * The request-deduplication behaviour has no replacement because it had no
 * callers to serve.
 */
initApp = function () {

    new selfObservers('body');

}

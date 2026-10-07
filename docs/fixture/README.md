# Screenshot fixture

These are documentation fixtures, excluded from the plugin package by `docs export-ignore`. **Use only on a disposable local site**, never on production. `setup.php` changes test options and theme; the integration test temporarily exercises migration/uninstall and restores its saved options. No real contact destinations are used or followed.

Captured with `@wp-playground/cli` 3.1.57, its bundled SQLite adapter, PHP 8.3.33, WordPress 6.9 from the official `WordPress/WordPress` GitHub tag (`ec24ee6087dad52052c7d8a11d50c24c9ba89a3b`), Playwright and system Chromium. WordPress.org downloads were blocked in the execution environment; the official GitHub source mirror supplied the fixture. This is real WordPress/PHP rendering, not an HTML recreation of the plugin or an AI-generated screenshot.

To reproduce in a separate working directory:

1. Install the pinned Playground CLI and Playwright (`npm install @wp-playground/cli@3.1.57 playwright`). Provide Chromium at `/usr/bin/chromium`, or adjust `executablePath` in the capture script.
2. Download/extract official WordPress 6.9. Mount that directory to `/wordpress` **before install** and the unchanged plugin repo to `/wordpress/wp-content/plugins/tavoos-floating-call-button`. Start Playground with `server --wp=6.9 --php=8.3 --port=9400 --workers=1 --wordpress-install-mode=install-from-existing-files --blueprint=PATH/blueprint.json --login`. Add `--mount-before-install=WP_DIRECTORY:/wordpress` and `--mount=REPO_DIRECTORY:/wordpress/wp-content/plugins/tavoos-floating-call-button`.
3. After installation, copy `theme/` to the fixture's `wp-content/themes/tavoos-demo/`, `offline.php` to `wp-content/mu-plugins/`, and `setup.php` / `extra-tests.php` to the fixture root as `fixture-setup.php` / `fixture-extra-tests.php`.
4. Copy the repository's Persian MO to `wp-content/languages/plugins/`. For `fixture-tests.php`, copy `tools/test-wordpress-release.php` into the fixture root and replace its `/var/www/html/wp-load.php` include with `__DIR__.'/wp-load.php'`; add an administrator capability check before executing its tests.
5. Set `TAVOOS_REPO` to the absolute repository path and run `capture.mjs` where Playwright is installed. It captures into `docs/screenshots/1.3.1/`. Run the extra tests through the authenticated local fixture URL; do not expose the fixture to the internet.

The fixture blocks external WordPress HTTP requests and the browser capture blocks nonlocal requests. `offline.php` also selects the fixture locale and RTL direction because the full WordPress Persian core language pack was unavailable. **Plugin strings use the actual repository MO; core menus remain English.** Saved contact labels are explicitly authored in each language, not automatically translated by the plugin. The frontend admin bar is disabled by the fixture. No plugin markup or styles are modified for screenshots.

English and Persian plugin screenshots cover all four tabs, image/SVG controls, the real Media Library dialog, desktop/mobile frontend and custom positioning. The media dialog screenshot demonstrates opening it; an actual file upload was not tested. Page selection, icon source and position previews shown during capture may be unsaved demonstrations. The initial sample configuration is persisted by the fixture's setup code.

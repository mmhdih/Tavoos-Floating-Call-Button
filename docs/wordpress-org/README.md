# WordPress.org listing publication

This package updates the existing 1.3.1 listing; it does not release a new plugin version.

- `assets/` maps to the **top-level SVN assets directory**, never the plugin runtime `assets/` directory.
- `readme.txt` is the effective listing metadata for both SVN `trunk/readme.txt` and `tags/1.3.1/readme.txt`, following WordPress.org's stable-readme rules.
- Banner PNGs are exactly 772×250 and 1544×500; icon PNGs are exactly 128×128 and 256×256. The editable banner source is outside the published asset directory.
- Screenshots 1–13 match the 13 numbered readme captions. Four `-fa_IR` variants use the real Persian fixture captures. All originals are preserved under `docs/screenshots/1.3.1/`.
- The existing `Tested up to` header is retained from the published readme; this listing work does not claim additional compatibility testing.

## Scope and release checksums

The original Git release/tag, release ZIP, executable files, versions and `.github/releases/1.3.1.json` remain unchanged. The publication action updates **only readme metadata inside the existing SVN stable tag**, plus SVN listing assets. Updating this readme may change WordPress.org's regenerated ZIP/readme checksums without a version bump. Listing assets stay outside the ZIP.

The original 12-file release manifest remains historical proof of the reviewed release. After this metadata update, its original readme hash will intentionally differ from the live SVN readme; the other 11 package hashes must still match. Do not run the old 1.3.1 release workflow to overwrite this metadata or alter its manifest to hide the difference. `tools/wordpress-listing.py verify` uses the separately approved listing readme hash and checks every non-readme release file.

## Publication workflow

`.github/workflows/wordpress-listing.yml` has a dedicated branch/path trigger and optional manual dispatch. A request with `publish: false` performs only a credential-free read/staging check. After inspecting that successful run, a request with `publish: true` publishes the exact approved files.

The script verifies current SVN trunk/stable code and readme hashes before staging. It refuses unreviewed remote readme/code differences, preserves existing SVN assets, validates the staged change list and sets PNG MIME handling. Only the publishing step receives the existing `SVN_USERNAME` / `SVN_PASSWORD` secrets. It uses reviewed 10up action 2.2.0 pinned to `2480306f6f693672726d08b5917ea114cb2825f7`, with `IGNORE_OTHER_FILES=true`. GitHub permissions are Contents read only. Concurrency is shared with the existing 1.3.1 deployment workflow.

Post-publication checks re-read SVN, compare all asset/readme hashes, check PNG MIME types and the unchanged tag list, and verify all 11 non-readme files in trunk and the stable tag. Public API/page/CDN checks report cache propagation separately from successful SVN publication. WordPress.org image caching can take time; an old public image is not by itself a failed SVN deployment.

## References

- [WordPress.org plugin assets](https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/)
- [10up readme/assets update action](https://github.com/10up/action-wordpress-plugin-asset-update/tree/2480306f6f693672726d08b5917ea114cb2825f7)

این انتشار فقط توضیحات، تصاویر و readme صفحه وردپرس را به‌روز می‌کند. نسخه و کد اجرایی ۱.۳.۱ ثابت می‌ماند. تغییر readme در تگ پایدار می‌تواند هش ZIP وردپرس را عوض کند؛ فایل‌های اجرایی، تگ گیت و ZIP انتشار گیت‌هاب تغییر نمی‌کنند.

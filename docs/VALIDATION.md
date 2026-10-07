# Validation and screenshot evidence

Date: 2026-10-07. Baseline: `cd6789fe443fe59e8b5a2c5967212c30799a8528`, published plugin version 1.3.1. The default remote branch was rechecked during this task and still pointed to that commit before the docs commit was prepared.

## Passed

| Check | Evidence / scope |
| --- | --- |
| Existing WordPress integration suite | **20/20** checks on real WordPress 6.9 / PHP 8.3.33 / Playground SQLite: sanitization, escaped text, message encoding, migration precedence, reactivation and uninstall isolation. [Log](validation/integration.txt). Only the local fixture include path and an admin guard were adapted. |
| Additional source checks | **20/20**: all 12 documented channel URL outputs, full-URL message bypass, 18 selectable icons, PHP parser validation of all 6 runtime PHP files. [Log](validation/extra.txt). |
| Browser smoke checks | Chromium: menu open, `aria-expanded`, Escape closes and restores focus; add/configure/remove a WhatsApp row with confirmation; real Persian heading assertion; RTL direction. [Log](validation/browser.txt). |
| Screenshots | 18 real captures; desktop viewport 1440×1080 (full-page heights vary), mobile viewport 390×844, scale 1. [Fixture and reproduction notes](fixture/README.md). |
| Translation checks | **151/151** entries; extracted PHP/header keys match staged POT/PO; no empty or fuzzy strings; printf placeholders preserved; MO round-trip, UTF-8/domain/language headers and Persian plural rule pass. `python3 tools/check-translations.py`. |
| JavaScript syntax | `node --check assets/js/admin.js` and `node --check assets/js/frontend.js`. |
| Release preservation | SHA-256 comparison confirms **all 12 reviewed package files unchanged** against `.github/releases/1.3.1.json`; guarded `git archive` build verifies the same files after commit. Runtime PHP/CSS/JS, `readme.txt`, `languages/` POT and workflows are unchanged. |
| Documentation/assets | Relative links and image targets checked; SVG XML parsed; PNGs opened; selected desktop, mobile, Persian admin and WhatsApp screenshots visually inspected; `git diff --check`. |

## Not run / limitations

- No new release, SVN write, tag mutation, binary replacement, production-site access or real calls/messages.
- No PHP 7.2 / WordPress 5.6 matrix, MySQL/MariaDB regression run, WooCommerce installation, multisite migration, Safari/Firefox, physical-device messaging or screen-reader/WCAG audit.
- No GNU `msgfmt`/WP-CLI `i18n make-pot`, WordPress Plugin Check or PHPCS execution in this task. PHP parser and custom catalog checks are the checks actually performed; they are not substitutes for claiming those tools ran.
- Persian wording was preserved/reviewed programmatically and rendered, not independently approved by a human translator or WordPress.org translation editor. Other translations are not claimed.
- Full WordPress Persian core translations were unavailable. The fixture explicitly supplies locale/RTL direction, loads the real plugin MO, and authors its saved demo labels in Persian. English core menus in Persian screenshots are expected.
- Browser captures demonstrate controls and preview states; exhaustive saved-form behavior, drag reordering, image uploads, all color combinations and every display rule were not exercised. Existing integration tests cover the Settings API sanitization path.
- Current WordPress.org availability/version was reported verified in the originating task, but direct WordPress.org revalidation was blocked from this environment. This report does not claim a fresh live version check.

## Screenshot index

| File | What it demonstrates |
| --- | --- |
| [01](screenshots/1.3.1/01-plugins-en.png) | Installed plugin / Settings link |
| [02](screenshots/1.3.1/02-channels-en.png) | Channels, toggles, order handles and Add |
| [03](screenshots/1.3.1/03-channel-fields-en.png) | Expanded channel fields and icons |
| [04](screenshots/1.3.1/04-appearance-en.png) | Button/menu appearance and preview |
| [05](screenshots/1.3.1/05-svg-en.png) | Custom SVG code source |
| [06](screenshots/1.3.1/06-image-en.png) | Image URL and media chooser |
| [07](screenshots/1.3.1/07-media-en.png) | Actual WordPress media dialog |
| [08](screenshots/1.3.1/08-position-en.png) | Presets, offsets, breakpoint, z-index |
| [09](screenshots/1.3.1/09-custom-position-en.png) | Percentage placement controls |
| [10](screenshots/1.3.1/10-display-en.png) | General options and page targeting |
| [11](screenshots/1.3.1/11-desktop-en.png) | Open menu, desktop English |
| [12](screenshots/1.3.1/12-mobile-en.png) | Open menu, mobile English |
| [13](screenshots/1.3.1/13-channels-fa.png) | Persian catalog and RTL admin |
| [14](screenshots/1.3.1/14-appearance-fa.png) | Persian appearance settings |
| [15](screenshots/1.3.1/15-desktop-fa.png) | Persian frontend, desktop |
| [16](screenshots/1.3.1/16-mobile-fa.png) | Persian frontend, mobile |
| [17](screenshots/1.3.1/17-custom-mobile-fa.png) | Percentage placement, mobile Persian |
| [18](screenshots/1.3.1/18-whatsapp-en.png) | Added WhatsApp row and prefilled message field |

خلاصه: ۴۰ بررسی PHP/وردپرس، آزمون‌های مرورگر و بررسی فنی ۱۵۱ مدخل ترجمه انجام شد. تصاویر از وردپرس واقعی‌اند. حداقل نسخه‌ها، ووکامرس، چندسایته، صفحه‌خوان و تأیید انسانی ترجمه آزمایش/تأیید نشده‌اند. بسته منتشرشده ۱.۳.۱ دست‌نخورده است؛ این تغییرات انتشار نسخه جدید نیست.

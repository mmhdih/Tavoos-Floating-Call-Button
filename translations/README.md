# Translation status / وضعیت ترجمه

Source: plugin 1.3.1 at `cd6789fe443fe59e8b5a2c5967212c30799a8528`.

| Locale | Coverage | Review / distribution |
| --- | --- | --- |
| English (source) | All source UI strings | No English PO is necessary |
| Persian / `fa_IR` | 151 of 151 extracted entries, including two unchanged URL headers | Existing Persian wording preserved, map hint aligned with source, URL entries completed; automated checks and fixture rendering, **not independent human linguistic approval** |

No other language is claimed as translated. Saved channel titles, subtitles, messages and the accessibility label are site content; changing the WordPress language does not translate those saved values. Edit them yourself. WordPress and theme strings have separate language packs.

زبان اصلی انگلیسی است و کاتالوگ فارسی همه ۱۵۱ مدخل استخراج‌شده را پوشش می‌دهد. این بررسی فنی و نمایش آزمایشی است؛ تأیید مستقل مترجم انسانی یا تأیید تیم ترجمه وردپرس محسوب نمی‌شود. عنوان، زیرعنوان، متن پیام و برچسب دسترسی‌پذیری ذخیره‌شده را باید خودتان ترجمه کنید. ترجمه زبان دیگری ادعا نمی‌شود.

## Use on a test site

Copy `tavoos-floating-call-button-fa_IR.mo` (and optionally its editable `.po`) into `wp-content/languages/plugins/`. Select Persian in **Settings → General → Site Language**; a user's own dashboard language can override the admin locale. Reload the page. Back up custom translations: WordPress language updates may replace files in this shared directory.

برای آزمایش، فایل `.mo` فارسی (و در صورت نیاز `.po`) را در `wp-content/languages/plugins/` قرار دهید و زبان سایت را فارسی کنید. زبان اختصاصی کاربر می‌تواند زبان پیشخوان را تغییر دهد. از ترجمه سفارشی نسخه پشتیبان داشته باشید؛ به‌روزرسانی بسته زبان ممکن است آن را جایگزین کند.

Automatic delivery depends on the [WordPress.org translation project](https://translate.wordpress.org/projects/wp-plugins/tavoos-floating-call-button/) and its approval/pack-generation process. This branch does not submit or approve translations there. A complete local PO does not establish that a live language pack exists.

## Update and validate

```sh
python3 tools/build-translations.py
python3 tools/check-translations.py
```

The builder extracts the PHP gettext calls used by this plugin and header metadata, retains Persian translations and compiles MO. It now stages the POT in **translations/**. The original `languages/` POT is frozen by the 1.3.1 release manifest. Promote a reviewed POT only as part of separately approved next-release work; do not rebuild or retag 1.3.1.

The extractor supports the plugin's current singular, single-quoted gettext calls. If future code adds plural/context/double-quoted or JavaScript gettext calls, extend extraction or use WordPress WP-CLI `i18n make-pot`; do not assume this regex extractor supports them. The validator checks current source/canonical catalog agreement, placeholders, metadata, completeness and MO round-trip. GNU gettext `msgfmt --check --check-format` is recommended when available.

خروجی POT جدید در پوشه `translations/` است تا فایل بسته منتشرشده ۱.۳.۱ تغییر نکند. انتقال آن به `languages/` فقط همراه انتشار بعدی و بررسی جداگانه انجام می‌شود. این شاخه نسخه جدیدی منتشر نمی‌کند.

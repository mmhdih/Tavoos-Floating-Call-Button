# Developer reference / راهنمای توسعه‌دهنده

This documents the hooks in published 1.3.1. Add site-specific code in a separate site plugin or child theme, not in the distributed plugin. این فیلترها مربوط به نسخه ۱.۳.۱ هستند؛ کد اختصاصی را در افزونه مستقل سایت یا پوسته فرزند بگذارید.

| Filter | Arguments | Purpose |
| --- | --- | --- |
| `tavoos_fcb_should_display` | `bool $show, array $settings` | Final page display decision; cached per request |
| `tavoos_fcb_channels` | `array $channels, array $settings` | Enabled, nonempty channels with built `url` values |
| `tavoos_fcb_channel_url` | `string $url, array $channel` | Built URL before output escaping |
| `tavoos_fcb_channel_types` | `array $types` | Channel definitions: label, icon, bg, color, placeholder, hint, message_label, new_tab |
| `tavoos_fcb_icons` | `array $icons` | Icons with label and `path` in 24×24 coordinates, or full `svg` |

Example: exclude the WooCommerce cart while preserving the existing display decision.

```php
add_filter( 'tavoos_fcb_should_display', function ( $show ) {
    return ( function_exists( 'is_cart' ) && is_cart() ) ? false : $show;
} );
```

Custom channel types do not automatically gain a URL builder: use `tavoos_fcb_channel_url` when their destination rules differ from the default HTTPS behavior. Keep filtered URLs valid and channels well formed. Output escaping still applies.

نوع کانال جدید خودکار سازنده لینک اختصاصی ندارد؛ در صورت نیاز از فیلتر URL استفاده کنید. پاک‌سازی خروجی همچنان انجام می‌شود.

| Path | Role |
| --- | --- |
| `tavoos-floating-call-button.php` | Bootstrap, legacy-copy guard, version |
| `includes/class-tavoos-fcb-options.php` | Defaults, channel types, sanitization, migration |
| `includes/class-tavoos-fcb-admin.php` | Settings API, admin UI and localized JS labels |
| `includes/class-tavoos-fcb-frontend.php` | Page matching, URLs, CSS values, rendering |
| `includes/icons.php` | Icon registry and SVG allowlist |
| `assets/` | Runtime CSS/JS; unchanged by this docs branch |
| `languages/` | Frozen 1.3.1 release POT |
| `translations/` | Review-stage POT and Persian PO/MO; excluded from package |
| `docs/` | Guides, original brand assets, screenshot evidence; excluded from package |
| `tools/` | Translation checks and existing release/integration tooling; excluded from package |
| `uninstall.php` | Deletes this plugin’s option on uninstall |

The current release is guarded by `.github/releases/1.3.1.json`. Do not change that manifest, published tags, SVN or release binaries as part of documentation work. See [release procedure](../tools/RELEASING.md) for separately authorized releases and [translation status](../translations/README.md) for next-release staging.

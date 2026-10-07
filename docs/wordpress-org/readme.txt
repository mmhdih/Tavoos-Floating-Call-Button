=== Tavoos Floating Call Button ===
Contributors: mmhdih
Tags: call button, whatsapp, telegram, floating button, contact
Requires at least: 5.6
Tested up to: 7.1
Requires PHP: 7.2
Stable tag: 1.3.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add a floating contact menu with 12 channel types, flexible display rules, custom icons, mobile controls and RTL support.

== Description ==

Tavoos Floating Call Button gives your WordPress visitors one place to choose how to contact you. Add your destinations, choose the button and menu appearance, and decide where it appears. No API key is required. Links open the visitor's browser or installed app; the plugin does not send messages itself.

[English illustrated guide](https://github.com/mmhdih/Tavoos-Floating-Call-Button/blob/HEAD/docs/guide-en.md) | [راهنمای تصویری فارسی](https://github.com/mmhdih/Tavoos-Floating-Call-Button/blob/HEAD/docs/guide-fa.md)

= Contact channels =

* 12 built-in types: phone, WhatsApp, Telegram, Instagram, email, SMS, Eitaa, Bale, Rubika, LinkedIn, map and custom links.
* Add, disable, remove or drag channels into your preferred order. Each channel has its own title, optional subtitle, icon and colors.
* Enter an international phone number for phone/WhatsApp/SMS, an address for email, a username for supported social channels, or a full destination URL.
* Optional prefilled WhatsApp/SMS message and email subject. Persian and Arabic digits are converted to Latin digits.
* Optional direct-link mode when exactly one enabled channel with a nonempty destination remains.

= Icons and appearance =

* 18 selectable preset icons, custom SVG code sanitized through an allowlist, or an image URL/Media Library image. The internal close icon is not a selectable preset.
* Solid background or two-color gradient; separate icon, card, title, subtitle and border colors.
* Separate desktop/mobile button sizes (30-150 px); default sizes are 60/54 px.
* Optional pulse animation and a live settings preview. Frontend animation respects the visitor's reduced-motion preference.
* The purple screenshots are configured examples. The installed plugin's default button remains gold.

= Position, mobile and display rules =

* Eight preset positions: bottom right/left/center, middle right/left and top right/left/center.
* Custom percentage placement with a draggable preview point or numeric horizontal/vertical inputs.
* Separate desktop/mobile edge distances, a configurable mobile breakpoint (default 1024 px), and z-index.
* Show on all pages, only selected pages, or all pages except selected ones. Target the front page, selected pages, or individual post/product IDs.
* Desktop/mobile visibility switches and automatic or forced right-to-left/left-to-right menu direction.
* Frontend CSS and JavaScript load on eligible pages; the frontend has no jQuery dependency. Device hiding uses CSS, not server-side access control.

= Accessibility, translations and limits =

The menu toggle has an accessible label and expanded state. Escape closes the menu and restores focus; clicking outside closes it. The links come before the toggle in keyboard order: after opening it, Shift+Tab reaches them. Focus does not automatically enter the menu. Admin drag reordering has no dedicated keyboard reorder control. Check contrast, zoom and keyboard behavior with your theme; no WCAG certification is claimed.

English is the source language. A complete Persian catalog is available in the repository for review and local installation. Automatic WordPress.org language packs depend on translation approval; their availability is not guaranteed. Saved titles, subtitles, messages and accessibility labels need your own translation. Core and theme translations are separate. [Translation instructions and review status](https://github.com/mmhdih/Tavoos-Floating-Call-Button/blob/HEAD/translations/README.md).

There is no chat inbox, click analytics, agent routing, business-hours schedule, chatbot or message delivery service. Many channels or long labels can exceed the viewport; test the open menu on small screens. There is no built-in menu scrolling or full collision detection. Third-party app behavior and availability are outside the plugin's control.

= راهنمای کوتاه فارسی =

از منوی «دکمه تماس»، مقصد حداقل یک کانال فعال را وارد کنید و «ذخیره تنظیمات» را بزنید. تب‌های کانال‌های ارتباطی، ظاهر دکمه، موقعیت و نمایش و عمومی برای تنظیم همه امکانات در دسترس‌اند. رنگ بنفش تصاویر صرفاً نمونه است؛ رنگ پیش‌فرض طلایی باقی می‌ماند.

افزونه خودش پیام ارسال نمی‌کند و نیازی به کلید API ندارد. ترجمه فارسی مخزن بررسی فنی شده است، اما تأیید مستقل مترجم انسانی یا نصب خودکار بسته زبان ادعا نمی‌شود. متن‌های ذخیره‌شده را خودتان ترجمه کنید. [آموزش کامل فارسی](https://github.com/mmhdih/Tavoos-Floating-Call-Button/blob/HEAD/docs/guide-fa.md).

= About the screenshots =

All screenshots are from a disposable WordPress 6.9 / PHP 8.3 fixture using the unchanged plugin code. The full core Persian language pack was unavailable: localized captures load the actual plugin catalog and explicitly set fixture RTL direction; core menus remain English. Demo contacts are not live support destinations.

= For developers =

Filters: `tavoos_fcb_should_display`, `tavoos_fcb_channels`, `tavoos_fcb_channel_url`, `tavoos_fcb_channel_types` and `tavoos_fcb_icons`. Use a separate site plugin or child theme for customizations. [Hook reference](https://github.com/mmhdih/Tavoos-Floating-Call-Button/blob/HEAD/docs/developers.md).

Designed by [Mahdi Habibi / Tavoos Web](https://tavoosweb.ir/).

== Installation ==

1. Search for "Tavoos Floating Call Button" in Plugins > Add New, or upload its installable ZIP through Plugins > Add New > Upload Plugin. For manual installation use `wp-content/plugins/tavoos-floating-call-button/`.
2. Activate the plugin and open Call Button in the dashboard. Settings require an administrator with `manage_options`.
3. In Contact channels, expand a row or select a type and click Add. Enter its destination, title and optional subtitle/message. Empty or disabled channels do not appear on the site.
4. In Button appearance, choose the icon source, colors, desktop/mobile sizes and pulse setting. Set card colors below the live preview.
5. In Position, select a preset or percentage coordinates, then check edge distances, mobile breakpoint and z-index.
6. In Display & general, enable the button, choose devices and page rules, then set direct-link behavior, accessible label and text direction.
7. Click Save settings. Check the public page on desktop and mobile, including the open menu and keyboard navigation.

== Frequently Asked Questions ==

= Why does no button appear? =

The initial destinations are empty. Save at least one enabled channel with a nonempty destination. Check the global Status switch, desktop/mobile widths, selected-page rules and page cache. Your theme must call `wp_footer()`. With "Only selected pages" and no selections, no pages match.

= What should I enter for each channel? =

Use an international number such as `+12025550123` for phone/SMS and `12025550123` for WhatsApp; use your own real number, not this example. Enter `hello@example.com` for email, a username such as `example` for Telegram/Instagram/Eitaa/Bale/Rubika, and a full URL for LinkedIn company pages, maps or custom links. A bare LinkedIn username becomes a personal-profile URL. The plugin cannot infer a missing country code.

= Why is my prefilled message missing? =

A full value beginning with a URI scheme bypasses automatic link construction, including message/subject appending. Use a bare number/address or supply the URL's encoded query parameters yourself. SMS prefill support varies between apps. No message is sent automatically.

= Can I use an uploaded image or SVG? =

Yes: choose Image, enter an image URL or open the Media Library and select an allowed file. Transparent PNG or WebP works well. The plugin does not enable SVG file uploads that WordPress rejects. Pasting custom SVG code is a separate option; unsupported elements and unsafe attributes are removed. Use `fill="currentColor"` for a single-color SVG. Raster images and the multicolor Rubika preset retain their own colors.

= Can I hide the button on specific pages or devices? =

Choose all pages, only selected pages, or exclusions. Select the front page/pages or enter comma-separated content IDs. Product IDs target individual products, not every product archive. Category/tag/search archives have no dedicated selector; custom rules can use the display filter. Mobile/tablet means a viewport at or below the configured breakpoint; zero treats all widths as desktop.

= What if the button or menu overlaps something? =

Adjust the position and mobile edge distances to clear sticky navigation. Increase z-index if the button is behind another element. Percentage positioning uses the same coordinates on desktop/mobile and ignores edge offsets. Reduce channel count or label length if the menu extends outside the viewport.

= Why does the dashboard remain English? =

Check the site and user languages and the installed plugin language pack. Repository Persian PO/MO files can be installed manually following the translation guide. A user language can override the dashboard locale. Core menus and theme text require their own translations; saved channel text is not translated automatically.

= Does the plugin collect messages or require an account? =

It has no built-in message storage, analytics or messaging API connection. A click navigates to a configured external service or device handler. External image URLs can also make visitor requests to their hosts. Service terms and availability still apply.

= How do I upgrade from Floating Call Button 1.2 or earlier? =

Back up your database first. Install Tavoos Floating Call Button, deactivate the older copy, open the new settings and verify migrated channels and display rules, then test the public button. Only after verifying migration should you delete the older copy. If both copies are active, the new copy may pause with a notice. Older custom `fcb_*` filters need the `tavoos_fcb_*` names.

= What happens when I deactivate or delete it? =

Deactivation preserves settings. Deleting this plugin removes its `tavoos_fcb_options` option. There is no built-in settings export/import or recycle bin. Back up before deletion.

== Screenshots ==

1. The real 1.3.1 contact menu on a desktop WordPress fixture, with a configured purple button and example destinations.
2. The same menu in a 390-pixel mobile viewport. Mobile size and edge distance can be configured separately.
3. Contact channels: enable/disable switches, drag handles, remove buttons and Add controls.
4. Expanded channel settings: destination, title, subtitle, new-tab behavior, icon source and colors.
5. Adding a WhatsApp channel with its optional prefilled message field.
6. Button appearance: preset icons, solid/gradient colors, sizes, pulse, card colors and live preview.
7. Position: eight presets, desktop/mobile offsets, breakpoint and z-index.
8. Custom percentage placement using a preview point or numeric W/H coordinates.
9. Display and general settings, including device visibility, accessible label, direction and page targeting.
10. Custom SVG code input for the main button; only allowed SVG features are retained.
11. Image URL input and the Choose from Media Library control.
12. The actual WordPress Media Library dialog opened from the icon chooser.
13. Installed plugin entry and its Settings link in WordPress.

== Changelog ==

= 1.3.1 =
* Updated the Plugin URI to the Tavoos Web plugins page.
* Clarified the display-mode field annotation for Plugin Check.
* Fixed the upgrade instructions so legacy settings are migrated before deleting the older plugin.
* Removing this plugin no longer deletes settings owned by an older Floating Call Button copy.
* Preserved literal backslashes when saving settings through the WordPress Settings API.

= 1.3.0 =
* Renamed to Tavoos Floating Call Button; the slug and text domain are now `tavoos-floating-call-button`.
* Translations now come from translate.wordpress.org; the plugin ships only the translation template.
* If the older "Floating Call Button" copy is active, this plugin stays idle and shows a notice instead of loading twice.

= 1.2.0 =
* All code prefixes are now at least four characters (`tavoos_fcb_`). Settings saved by earlier versions are migrated automatically.
* The source strings are now in English, with a bundled Persian (fa_IR) translation.
* Filters were renamed from `fcb_*` to `tavoos_fcb_*`.

= 1.1.0 =
* Logo icons for Eitaa, Bale and Rubika.
* Preset icons can be multicolor.

= 1.0.0 =
* First release.

== Upgrade Notice ==

= 1.3.1 =
Maintenance release. When upgrading from 1.2 or earlier, deactivate the old copy, activate this plugin and verify your settings before deleting the old copy.

= 1.3.0 =
The plugin was renamed and now installs in a new folder. Install it, deactivate the old "Floating Call Button" plugin, then activate this one. Open Call Button and verify your saved settings before deleting the old copy.

= 1.2.0 =
Filters were renamed from fcb_* to tavoos_fcb_*. Update any custom code that uses them.

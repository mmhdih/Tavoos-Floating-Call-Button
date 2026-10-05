<?php
/**
 * Plugin Name:       Tavoos Floating Call Button
 * Plugin URI:        https://tavoosweb.ir/free-wordpress-plugins/
 * Description:       Adds a floating contact button to all or selected pages. Clicking it opens your contact channels: phone, WhatsApp, Telegram, Instagram, email and more.
 * Version:           1.3.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            Mahdi Habibi | Tavoos Web
 * Author URI:        https://tavoosweb.ir/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tavoos-floating-call-button
 * Domain Path:       /languages
 *
 * @package Tavoos_Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Version 1.2 was published under the slug "floating-call-button" and declares the same
 * classes. If that copy (or 1.1 and earlier) is loaded, stay idle and ask for it to be
 * deactivated instead of redeclaring everything. Its settings are copied to this
 * version's own option right away, because deleting the old copy removes its option.
 */
if ( defined( 'TAVOOS_FCB_VERSION' ) || defined( 'FCB_VERSION' ) ) {
	add_action(
		'init',
		function () {
			foreach ( array( 'tavoos_fcb_settings', 'fcb_settings' ) as $tavoos_fcb_legacy_name ) {
				$tavoos_fcb_legacy = get_option( $tavoos_fcb_legacy_name );
				if ( is_array( $tavoos_fcb_legacy ) && false === get_option( 'tavoos_fcb_options' ) ) {
					add_option( 'tavoos_fcb_options', $tavoos_fcb_legacy );
				}
			}
		}
	);
	add_action(
		'admin_notices',
		function () {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'Tavoos Floating Call Button is paused because an older copy of the plugin (Floating Call Button) is active. Deactivate and delete the older copy; your settings are kept.', 'tavoos-floating-call-button' ) . '</p></div>';
		}
	);
	return;
}

define( 'TAVOOS_FCB_VERSION', '1.3.0' );
define( 'TAVOOS_FCB_FILE', __FILE__ );
define( 'TAVOOS_FCB_DIR', plugin_dir_path( __FILE__ ) );
define( 'TAVOOS_FCB_URL', plugin_dir_url( __FILE__ ) );
define( 'TAVOOS_FCB_OPTION', 'tavoos_fcb_options' );

require_once TAVOOS_FCB_DIR . 'includes/icons.php';
require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-options.php';
require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-frontend.php';

if ( is_admin() ) {
	require_once TAVOOS_FCB_DIR . 'includes/class-tavoos-fcb-admin.php';
	Tavoos_FCB_Admin::init();
}

Tavoos_FCB_Frontend::init();

add_action( 'plugins_loaded', array( 'Tavoos_FCB_Options', 'maybe_migrate' ) );
register_activation_hook( __FILE__, array( 'Tavoos_FCB_Options', 'activate' ) );

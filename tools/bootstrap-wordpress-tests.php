<?php
$_SERVER['HTTP_HOST'] = parse_url( getenv( 'DEV_SITE_URL' ), PHP_URL_HOST );
$_SERVER['REQUEST_URI'] = '/';
define( 'WP_INSTALLING', true );
require '/var/www/html/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
add_filter( 'pre_wp_mail', '__return_true' );
if ( ! is_blog_installed() ) {
	wp_install( 'Tavoos release tests', 'developer', 'developer@example.test', false, '', getenv( 'DEV_ADMIN_PASSWORD' ) );
}
update_option( 'home', getenv( 'DEV_SITE_URL' ) );
update_option( 'siteurl', getenv( 'DEV_SITE_URL' ) );
$result = activate_plugin( 'tavoos-floating-call-button/tavoos-floating-call-button.php' );
if ( is_wp_error( $result ) ) {
	throw new RuntimeException( $result->get_error_message() );
}
$settings = Tavoos_FCB_Options::get();
$settings['channels'][0]['value'] = '+15551234567';
update_option( TAVOOS_FCB_OPTION, $settings );
echo "Disposable WordPress test site initialized\n";

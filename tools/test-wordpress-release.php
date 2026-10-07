<?php
/** Integration regressions against a disposable installed WordPress site. */
$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';

$checks = 0;
function release_check( $condition, $message ) {
	global $checks;
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	++$checks;
	echo "PASS: $message\n";
}

$keys = array( 'tavoos_fcb_options', 'tavoos_fcb_settings', 'fcb_settings', 'tavoos_release_unrelated' );
$saved = array();
foreach ( $keys as $key ) {
	$saved[ $key ] = get_option( $key );
}
try {
	release_check( TAVOOS_FCB_VERSION === '1.3.1', 'runtime version is 1.3.1' );
	$settings = Tavoos_FCB_Options::defaults();
	$settings['aria_label'] = 'Contact \\ team';
	$settings['channels'][0]['value'] = '+۹۸۹۱۲۱۲۳۴۵۶۷';
	$settings['channels'][0]['title'] = 'Team \\ support';
	$settings['channels'][0]['subtitle'] = 'Path C:\\Support';
	$settings['channels'][0]['message'] = "Line \\ one\nLine \\ two";
	$clean = Tavoos_FCB_Options::sanitize( $settings );
	release_check( $clean['aria_label'] === $settings['aria_label'], 'literal backslash in accessibility label' );
	foreach ( array( 'title', 'subtitle', 'message' ) as $field ) {
		release_check( $clean['channels'][0][ $field ] === $settings['channels'][0][ $field ], "literal backslashes in channel $field" );
	}
	register_setting( 'tavoos_release_test', TAVOOS_FCB_OPTION, array( 'sanitize_callback' => array( 'Tavoos_FCB_Options', 'sanitize' ) ) );
	update_option( TAVOOS_FCB_OPTION, $settings );
	$stored = get_option( TAVOOS_FCB_OPTION );
	release_check( $stored['aria_label'] === $settings['aria_label'], 'registered Settings API preserves backslash' );
	release_check( $stored['channels'][0]['message'] === $settings['channels'][0]['message'], 'registered Settings API preserves multiline message' );
	unregister_setting( 'tavoos_release_test', TAVOOS_FCB_OPTION );
	release_check( Tavoos_FCB_Frontend::build_url( $clean['channels'][0] ) === 'tel:+989121234567', 'Persian phone digits normalize to tel URL' );
	$whatsapp = Tavoos_FCB_Options::channel_defaults( 'whatsapp', array( 'value' => '989121234567', 'message' => 'Hello \\ team' ) );
	$url = Tavoos_FCB_Frontend::build_url( $whatsapp );
	parse_str( parse_url( $url, PHP_URL_QUERY ), $query );
	release_check( $query['text'] === $whatsapp['message'], 'WhatsApp URL preserves encoded message backslash' );
	release_check( Tavoos_FCB_Frontend::build_url( Tavoos_FCB_Options::channel_defaults( 'phone', array( 'value' => '' ) ) ) === '', 'empty channel does not produce a URL' );
	$malicious = $settings;
	$malicious['aria_label'] = '<script>alert(1)</script>Contact';
	$malicious['icon_type'] = 'svg';
	$malicious['icon_svg'] = '<svg onload="alert(1)"><script>alert(1)</script><path d="M0 0h1"/></svg>';
	$safe = Tavoos_FCB_Options::sanitize( $malicious );
	release_check( strpos( $safe['aria_label'], '<script' ) === false, 'text input strips script tags' );
	release_check( strpos( $safe['icon_svg'], 'onload' ) === false && strpos( $safe['icon_svg'], '<script' ) === false, 'custom SVG strips executable content' );
	$legacy = $settings;
	$legacy['aria_label'] = 'Migrated \\ settings';
	delete_option( TAVOOS_FCB_OPTION );
	update_option( 'tavoos_fcb_settings', $legacy );
	update_option( 'fcb_settings', array( 'aria_label' => 'Older' ) );
	Tavoos_FCB_Options::activate();
	release_check( get_option( TAVOOS_FCB_OPTION ) === $legacy, 'activation migrates newest legacy settings unchanged' );
	release_check( get_option( 'tavoos_fcb_settings' ) === false && get_option( 'fcb_settings' ) === false, 'migration removes legacy options only after copying' );
	update_option( 'tavoos_fcb_settings', array( 'aria_label' => 'Updated old copy' ) );
	Tavoos_FCB_Options::maybe_migrate();
	release_check( get_option( TAVOOS_FCB_OPTION )['aria_label'] === 'Updated old copy', 'latest legacy settings replace paused-copy snapshot' );
	Tavoos_FCB_Options::activate();
	release_check( get_option( TAVOOS_FCB_OPTION )['aria_label'] === 'Updated old copy', 'reactivation preserves existing settings' );
	update_option( 'tavoos_fcb_settings', array( 'keep' => '1.2' ) );
	update_option( 'fcb_settings', array( 'keep' => '1.1' ) );
	update_option( 'tavoos_release_unrelated', 'keep' );
	define( 'WP_UNINSTALL_PLUGIN', 'tavoos-floating-call-button/tavoos-floating-call-button.php' );
	require TAVOOS_FCB_DIR . 'uninstall.php';
	release_check( get_option( TAVOOS_FCB_OPTION ) === false, 'uninstall deletes only its own settings' );
	release_check( get_option( 'tavoos_fcb_settings' ) === array( 'keep' => '1.2' ), 'uninstall preserves 1.2 settings' );
	release_check( get_option( 'fcb_settings' ) === array( 'keep' => '1.1' ), 'uninstall preserves 1.1 settings' );
	release_check( get_option( 'tavoos_release_unrelated' ) === 'keep', 'uninstall preserves unrelated option' );
} finally {
	unregister_setting( 'tavoos_release_test', TAVOOS_FCB_OPTION );
	foreach ( $saved as $key => $value ) {
		delete_option( $key );
		if ( false !== $value ) {
			add_option( $key, $value );
		}
	}
}
echo "Completed $checks integration checks on WordPress " . get_bloginfo( 'version' ) . ' / PHP ' . PHP_VERSION . "\n";

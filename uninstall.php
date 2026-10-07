<?php
/**
 * Remove the plugin settings when the plugin is deleted.
 *
 * @package Tavoos_Floating_Call_Button
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'tavoos_fcb_options' );
// Legacy options belong to older plugin copies; migration removes them after copying.

<?php
/**
 * Plugin settings: defaults, reading, migration and sanitization.
 *
 * @package Tavoos_Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tavoos_FCB_Options {

	/**
	 * Preset button positions.
	 *
	 * @return array<string, string>
	 */
	public static function positions() {
		return array(
			'bottom-right'  => __( 'Bottom right', 'tavoos-floating-call-button' ),
			'bottom-left'   => __( 'Bottom left', 'tavoos-floating-call-button' ),
			'bottom-center' => __( 'Bottom center', 'tavoos-floating-call-button' ),
			'middle-right'  => __( 'Middle right', 'tavoos-floating-call-button' ),
			'middle-left'   => __( 'Middle left', 'tavoos-floating-call-button' ),
			'top-right'     => __( 'Top right', 'tavoos-floating-call-button' ),
			'top-left'      => __( 'Top left', 'tavoos-floating-call-button' ),
			'top-center'    => __( 'Top center', 'tavoos-floating-call-button' ),
			'custom'        => __( 'Custom (percent)', 'tavoos-floating-call-button' ),
		);
	}

	/**
	 * Contact channel types and the defaults of each.
	 *
	 * Extend with the `tavoos_fcb_channel_types` filter.
	 *
	 * @return array<string, array>
	 */
	public static function channel_types() {
		return apply_filters(
			'tavoos_fcb_channel_types',
			array(
				'phone'     => array(
					'label'         => __( 'Phone call', 'tavoos-floating-call-button' ),
					'icon'          => 'phone',
					'bg'            => '#FFF1C2',
					'color'         => '#2B2118',
					'placeholder'   => '+15551234567',
					'hint'          => __( 'Phone number, preferably with the country code.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 0,
				),
				'whatsapp'  => array(
					'label'         => __( 'WhatsApp message', 'tavoos-floating-call-button' ),
					'icon'          => 'whatsapp',
					'bg'            => '#25D366',
					'color'         => '#FFFFFF',
					'placeholder'   => '15551234567',
					'hint'          => __( 'WhatsApp number with the country code, without + or leading zeros (e.g. 15551234567).', 'tavoos-floating-call-button' ),
					'message_label' => __( 'Default message', 'tavoos-floating-call-button' ),
					'new_tab'       => 1,
				),
				'telegram'  => array(
					'label'         => __( 'Telegram', 'tavoos-floating-call-button' ),
					'icon'          => 'telegram',
					'bg'            => '#229ED9',
					'color'         => '#FFFFFF',
					'placeholder'   => 'username',
					'hint'          => __( 'Telegram username (without @) or a full link.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'instagram' => array(
					'label'         => __( 'Instagram', 'tavoos-floating-call-button' ),
					'icon'          => 'instagram',
					'bg'            => '#E1306C',
					'color'         => '#FFFFFF',
					'placeholder'   => 'username',
					'hint'          => __( 'Instagram username or a full link.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'email'     => array(
					'label'         => __( 'Email', 'tavoos-floating-call-button' ),
					'icon'          => 'email',
					'bg'            => '#EA4335',
					'color'         => '#FFFFFF',
					'placeholder'   => 'info@example.com',
					'hint'          => __( 'Email address.', 'tavoos-floating-call-button' ),
					'message_label' => __( 'Default email subject', 'tavoos-floating-call-button' ),
					'new_tab'       => 0,
				),
				'sms'       => array(
					'label'         => __( 'SMS', 'tavoos-floating-call-button' ),
					'icon'          => 'sms',
					'bg'            => '#4CAF50',
					'color'         => '#FFFFFF',
					'placeholder'   => '+15551234567',
					'hint'          => __( 'Number that receives the SMS.', 'tavoos-floating-call-button' ),
					'message_label' => __( 'Default SMS text', 'tavoos-floating-call-button' ),
					'new_tab'       => 0,
				),
				'eitaa'     => array(
					'label'         => __( 'Eitaa', 'tavoos-floating-call-button' ),
					'icon'          => 'eitaa',
					'bg'            => '#EE7D23',
					'color'         => '#FFFFFF',
					'placeholder'   => 'username',
					'hint'          => __( 'Eitaa username or a full link.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'bale'      => array(
					'label'         => __( 'Bale', 'tavoos-floating-call-button' ),
					'icon'          => 'bale',
					'bg'            => '#35A99A',
					'color'         => '#FFFFFF',
					'placeholder'   => 'username',
					'hint'          => __( 'Bale username or a full link.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'rubika'    => array(
					'label'         => __( 'Rubika', 'tavoos-floating-call-button' ),
					'icon'          => 'rubika',
					'bg'            => '#F4F1FA',
					'color'         => '#6A3FA0',
					'placeholder'   => 'username',
					'hint'          => __( 'Rubika username or a full link.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'linkedin'  => array(
					'label'         => __( 'LinkedIn', 'tavoos-floating-call-button' ),
					'icon'          => 'linkedin',
					'bg'            => '#0A66C2',
					'color'         => '#FFFFFF',
					'placeholder'   => 'https://www.linkedin.com/company/...',
					'hint'          => __( 'Full link to your LinkedIn page.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'location'  => array(
					'label'         => __( 'Address on map', 'tavoos-floating-call-button' ),
					'icon'          => 'location',
					'bg'            => '#34A853',
					'color'         => '#FFFFFF',
					'placeholder'   => 'https://maps.google.com/...',
					'hint'          => __( 'Link to Google Maps or another map service.', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
				'custom'    => array(
					'label'         => __( 'Custom link', 'tavoos-floating-call-button' ),
					'icon'          => 'link',
					'bg'            => '#607D8B',
					'color'         => '#FFFFFF',
					'placeholder'   => 'https://example.com',
					'hint'          => __( 'Any link (https:, tel:, mailto: and so on).', 'tavoos-floating-call-button' ),
					'message_label' => '',
					'new_tab'       => 1,
				),
			)
		);
	}

	/**
	 * A channel filled with the defaults of its type.
	 *
	 * @param string $type Channel type.
	 * @param array  $args Values that override the defaults.
	 * @return array
	 */
	public static function channel_defaults( $type = 'custom', $args = array() ) {
		$types = self::channel_types();
		$t     = isset( $types[ $type ] ) ? $types[ $type ] : $types['custom'];

		return wp_parse_args(
			$args,
			array(
				'enabled'    => 1,
				'type'       => isset( $types[ $type ] ) ? $type : 'custom',
				'title'      => $t['label'],
				'subtitle'   => '',
				'value'      => '',
				'message'    => '',
				'new_tab'    => $t['new_tab'],
				'icon_type'  => 'preset',
				'icon'       => $t['icon'],
				'icon_svg'   => '',
				'icon_img'   => '',
				'icon_bg'    => $t['bg'],
				'icon_color' => $t['color'],
			)
		);
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			// General.
			'enabled'           => 1,
			'single_direct'     => 0,
			'aria_label'        => __( 'Contact us', 'tavoos-floating-call-button' ),
			'direction'         => 'auto',

			// Display rules.
			'display_mode'      => 'all',
			'pages'             => array(),
			'front_page'        => 0,
			'post_ids'          => '',
			'show_desktop'      => 1,
			'show_mobile'       => 1,

			// Position.
			'position'          => 'bottom-right',
			'offset_x'          => 22,
			'offset_y'          => 24,
			'custom_x'          => 95,
			'custom_y'          => 90,
			'mobile_breakpoint' => 1024,
			'mobile_offset_x'   => 14,
			'mobile_offset_y'   => 78,
			'z_index'           => 9990,

			// Main button appearance.
			'size'              => 60,
			'mobile_size'       => 54,
			'icon_type'         => 'preset',
			'icon'              => 'phone',
			'icon_svg'          => '',
			'icon_img'          => '',
			'bg_color'          => '#F5B301',
			'bg_color2'         => '#FFD34D',
			'icon_color'        => '#2B2118',
			'pulse'             => 1,

			// Menu appearance.
			'card_bg'           => '#FFFFFF',
			'card_text'         => '#2B2118',
			'card_subtext'      => '#8A7D70',
			'card_border'       => '#F5EAD0',

			// Channels.
			'channels'          => array(
				self::channel_defaults( 'phone', array( 'subtitle' => __( 'Call us now', 'tavoos-floating-call-button' ) ) ),
				self::channel_defaults(
					'whatsapp',
					array(
						'subtitle' => __( 'Quick replies from our team', 'tavoos-floating-call-button' ),
						'message'  => __( 'Hello, I am contacting you from your website.', 'tavoos-floating-call-button' ),
					)
				),
				self::channel_defaults( 'telegram', array( 'enabled' => 0 ) ),
			),
		);
	}

	/**
	 * Current settings merged with the defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$saved = get_option( TAVOOS_FCB_OPTION );
		if ( ! is_array( $saved ) ) {
			return self::defaults();
		}
		return array_merge( self::defaults(), $saved );
	}

	/**
	 * Store the default settings on activation.
	 */
	public static function activate() {
		self::maybe_migrate();
		if ( false === get_option( TAVOOS_FCB_OPTION ) ) {
			add_option( TAVOOS_FCB_OPTION, self::defaults() );
		}
	}

	/**
	 * Option names used by earlier versions, newest first.
	 *
	 * 1.2 (slug floating-call-button) used `tavoos_fcb_settings`, 1.1 and earlier `fcb_settings`.
	 * Their uninstall scripts delete these options, so the settings are copied to
	 * TAVOOS_FCB_OPTION before an old copy can be deleted.
	 *
	 * @return string[]
	 */
	public static function legacy_options() {
		return array( 'tavoos_fcb_settings', 'fcb_settings' );
	}

	/**
	 * Take over the settings of an earlier version, once.
	 *
	 * The newest old option wins, even over a copy made while this plugin was idle next to
	 * the old one, because the old plugin was in use until now. The old options are then
	 * deleted. (While an old copy is active, the main plugin file only fills in a missing
	 * option, without using this class.)
	 *
	 * Hooked to `plugins_loaded`, so it takes no arguments.
	 */
	public static function maybe_migrate() {
		$copied = false;
		foreach ( self::legacy_options() as $legacy_name ) {
			$legacy = get_option( $legacy_name );
			if ( false === $legacy ) {
				continue;
			}
			if ( ! $copied && is_array( $legacy ) ) {
				update_option( TAVOOS_FCB_OPTION, $legacy );
				$copied = true;
			}
			delete_option( $legacy_name );
		}
	}

	/**
	 * Sanitize the settings form input.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$d   = self::defaults();
		$in  = is_array( $input ) ? $input : array();
		$out = array();

		// The Settings API passes unslashed input to sanitize callbacks.
		// Checkboxes.
		foreach ( array( 'enabled', 'single_direct', 'front_page', 'show_desktop', 'show_mobile', 'pulse' ) as $key ) {
			$out[ $key ] = empty( $in[ $key ] ) ? 0 : 1;
		}

		$out['aria_label'] = isset( $in['aria_label'] ) ? sanitize_text_field( $in['aria_label'] ) : $d['aria_label'];
		$out['direction']  = self::choice( $in, 'direction', array( 'auto', 'rtl', 'ltr' ), $d['direction'] );

		$out['display_mode'] = self::choice( $in, 'display_mode', array( 'all', 'include', 'exclude' ), 'all' );
		$out['pages']        = isset( $in['pages'] ) && is_array( $in['pages'] ) ? array_values( array_filter( array_map( 'absint', $in['pages'] ) ) ) : array();
		$out['post_ids']     = isset( $in['post_ids'] ) ? implode( ', ', self::parse_ids( $in['post_ids'] ) ) : '';

		$out['position'] = self::choice( $in, 'position', array_keys( self::positions() ), $d['position'] );

		$numbers = array(
			'offset_x'          => array( 0, 500 ),
			'offset_y'          => array( 0, 500 ),
			'custom_x'          => array( 0, 100 ),
			'custom_y'          => array( 0, 100 ),
			'mobile_breakpoint' => array( 0, 3000 ),
			'mobile_offset_x'   => array( 0, 500 ),
			'mobile_offset_y'   => array( 0, 500 ),
			'z_index'           => array( 0, 2147483647 ),
			'size'              => array( 30, 150 ),
			'mobile_size'       => array( 30, 150 ),
		);
		foreach ( $numbers as $key => $range ) {
			$out[ $key ] = self::number( $in, $key, $range[0], $range[1], $d[ $key ] );
		}

		$out = array_merge( $out, self::sanitize_icon( $in, $d['icon'] ) );

		$colors = array( 'bg_color', 'icon_color', 'card_bg', 'card_text', 'card_subtext', 'card_border' );
		foreach ( $colors as $key ) {
			$color       = isset( $in[ $key ] ) ? sanitize_hex_color( $in[ $key ] ) : '';
			$out[ $key ] = $color ? $color : $d[ $key ];
		}
		// The second gradient color is optional (empty means a solid color).
		$out['bg_color2'] = isset( $in['bg_color2'] ) ? (string) sanitize_hex_color( $in['bg_color2'] ) : '';

		$out['channels'] = array();
		if ( isset( $in['channels'] ) && is_array( $in['channels'] ) ) {
			foreach ( $in['channels'] as $channel ) {
				if ( is_array( $channel ) ) {
					$out['channels'][] = self::sanitize_channel( $channel );
				}
			}
		}

		return $out;
	}

	/**
	 * Sanitize one channel.
	 *
	 * @param array $c Raw channel input.
	 * @return array
	 */
	protected static function sanitize_channel( $c ) {
		$types = self::channel_types();
		$type  = isset( $c['type'], $types[ $c['type'] ] ) ? $c['type'] : 'custom';
		$def   = self::channel_defaults( $type );

		$icon_bg    = isset( $c['icon_bg'] ) ? sanitize_hex_color( $c['icon_bg'] ) : '';
		$icon_color = isset( $c['icon_color'] ) ? sanitize_hex_color( $c['icon_color'] ) : '';

		return array_merge(
			array(
				'enabled'    => empty( $c['enabled'] ) ? 0 : 1,
				'type'       => $type,
				'title'      => isset( $c['title'] ) ? sanitize_text_field( $c['title'] ) : '',
				'subtitle'   => isset( $c['subtitle'] ) ? sanitize_text_field( $c['subtitle'] ) : '',
				// sanitize_text_field() would strip %xx sequences from links, so only tags and whitespace are removed.
				'value'      => isset( $c['value'] ) ? trim( preg_replace( '/[\r\n\t ]+/', ' ', wp_strip_all_tags( $c['value'] ) ) ) : '',
				'message'    => isset( $c['message'] ) ? sanitize_textarea_field( $c['message'] ) : '',
				'new_tab'    => empty( $c['new_tab'] ) ? 0 : 1,
				'icon_bg'    => $icon_bg ? $icon_bg : $def['icon_bg'],
				'icon_color' => $icon_color ? $icon_color : $def['icon_color'],
			),
			self::sanitize_icon( $c, $def['icon'] )
		);
	}

	/**
	 * Sanitize the icon fields (shared by the main button and channels).
	 *
	 * @param array  $in           Input.
	 * @param string $default_icon Default icon key.
	 * @return array
	 */
	protected static function sanitize_icon( $in, $default_icon ) {
		$icons = tavoos_fcb_get_icons();

		return array(
			'icon_type' => self::choice( $in, 'icon_type', array( 'preset', 'svg', 'image' ), 'preset' ),
			'icon'      => isset( $in['icon'], $icons[ $in['icon'] ] ) ? $in['icon'] : $default_icon,
			'icon_svg'  => isset( $in['icon_svg'] ) ? tavoos_fcb_sanitize_svg( $in['icon_svg'] ) : '',
			'icon_img'  => isset( $in['icon_img'] ) ? esc_url_raw( trim( $in['icon_img'] ) ) : '',
		);
	}

	/**
	 * Parse a list of post IDs into integers.
	 *
	 * @param string|array $ids IDs.
	 * @return int[]
	 */
	public static function parse_ids( $ids ) {
		if ( is_array( $ids ) ) {
			$ids = implode( ',', $ids );
		}
		$ids = self::latin_digits( (string) $ids );
		return array_values( array_unique( array_filter( array_map( 'absint', preg_split( '/[\s,،]+/u', $ids ) ) ) ) );
	}

	/**
	 * Convert Persian and Arabic digits to Latin digits.
	 *
	 * @param string $str String.
	 * @return string
	 */
	public static function latin_digits( $str ) {
		return strtr(
			(string) $str,
			array(
				'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing
				'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
				'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
				'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
			)
		);
	}

	/**
	 * A value restricted to a list of allowed choices.
	 *
	 * @param array  $in      Input.
	 * @param string $key     Key.
	 * @param array  $allowed Allowed values.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	protected static function choice( $in, $key, $allowed, $fallback ) {
		return isset( $in[ $key ] ) && in_array( $in[ $key ], $allowed, true ) ? $in[ $key ] : $fallback;
	}

	/**
	 * A number clamped to a range.
	 *
	 * @param array     $in      Input.
	 * @param string    $key     Key.
	 * @param int|float $min     Minimum.
	 * @param int|float $max     Maximum.
	 * @param int|float $fallback Fallback.
	 * @return int|float
	 */
	protected static function number( $in, $key, $min, $max, $fallback ) {
		if ( ! isset( $in[ $key ] ) || '' === trim( (string) $in[ $key ] ) ) {
			return $fallback;
		}
		$value = (float) self::latin_digits( $in[ $key ] );
		$value = max( $min, min( $max, $value ) );
		return ( floor( $value ) == $value ) ? (int) $value : round( $value, 2 ); // phpcs:ignore Universal.Operators.StrictComparisons
	}
}

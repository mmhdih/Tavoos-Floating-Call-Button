<?php
/**
 * Settings page in the WordPress dashboard.
 *
 * @package Tavoos_Floating_Call_Button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin settings page.
 */
class Tavoos_FCB_Admin {

	const SLUG  = 'tavoos-floating-call-button';
	const GROUP = 'tavoos_fcb_settings_group';

	/**
	 * Hook suffix of the settings page.
	 *
	 * @var string
	 */
	protected static $hook = '';

	/**
	 * Register admin hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( TAVOOS_FCB_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Add the menu item.
	 */
	public static function menu() {
		self::$hook = add_menu_page(
			__( 'Tavoos Floating Call Button', 'tavoos-floating-call-button' ),
			__( 'Call Button', 'tavoos-floating-call-button' ),
			'manage_options',
			self::SLUG,
			array( __CLASS__, 'render_page' ),
			'dashicons-phone',
			81
		);
	}

	/**
	 * Register the setting.
	 */
	public static function register() {
		register_setting(
			self::GROUP,
			TAVOOS_FCB_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'Tavoos_FCB_Options', 'sanitize' ),
				'default'           => Tavoos_FCB_Options::defaults(),
			)
		);
	}

	/**
	 * "Settings" link on the Plugins screen.
	 *
	 * @param array $links Action links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::SLUG ) ) . '">' . esc_html__( 'Settings', 'tavoos-floating-call-button' ) . '</a>' );
		return $links;
	}

	/**
	 * "Designed by" link under the plugin description on the Plugins screen.
	 *
	 * @param array  $links Row meta links.
	 * @param string $file  Plugin file.
	 * @return array
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( TAVOOS_FCB_FILE ) === $file ) {
			$links[] = sprintf(
				/* translators: %s: author name */
				esc_html__( 'Designed by %s', 'tavoos-floating-call-button' ),
				'<a href="https://tavoosweb.ir/" target="_blank" rel="noopener">' . esc_html__( 'Mahdi Habibi | Tavoos Web', 'tavoos-floating-call-button' ) . '</a>'
			);
		}
		return $links;
	}

	/**
	 * Enqueue admin assets on the settings page only.
	 *
	 * @param string $hook Current admin page.
	 */
	public static function assets( $hook ) {
		if ( $hook !== self::$hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'tavoos-fcb-admin', TAVOOS_FCB_URL . 'assets/css/admin.css', array(), TAVOOS_FCB_VERSION );
		wp_enqueue_script( 'tavoos-fcb-admin', TAVOOS_FCB_URL . 'assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable', 'wp-color-picker' ), TAVOOS_FCB_VERSION, true );

		$icons = array();
		foreach ( array_keys( tavoos_fcb_get_icons() ) as $key ) {
			$icons[ $key ] = tavoos_fcb_preset_icon_svg( $key );
		}

		wp_localize_script(
			'tavoos-fcb-admin',
			'tavoosFcbAdmin',
			array(
				'option' => TAVOOS_FCB_OPTION,
				'icons'  => $icons,
				'types'  => Tavoos_FCB_Options::channel_types(),
				'i18n'   => array(
					'confirmRemove' => __( 'Remove this channel?', 'tavoos-floating-call-button' ),
					'mediaTitle'    => __( 'Choose an icon', 'tavoos-floating-call-button' ),
					'mediaButton'   => __( 'Use as icon', 'tavoos-floating-call-button' ),
					'untitled'      => __( 'Untitled', 'tavoos-floating-call-button' ),
					'noValue'       => __( 'No value entered', 'tavoos-floating-call-button' ),
				),
			)
		);
	}

	/* ------------------------------------------------------------------ */
	/* Helpers                                                             */
	/* ------------------------------------------------------------------ */

	/**
	 * Field name for a setting key.
	 *
	 * @param string $key Setting key.
	 * @return string
	 */
	protected static function name( $key ) {
		return TAVOOS_FCB_OPTION . '[' . $key . ']';
	}

	/**
	 * Print a switch-style checkbox.
	 *
	 * @param string $name    Field name.
	 * @param mixed  $checked Current value.
	 * @param string $label   Label.
	 * @param string $class   Extra CSS class.
	 */
	protected static function checkbox( $name, $checked, $label, $class = '' ) {
		?>
		<label class="tavoos-fcb-switch <?php echo esc_attr( $class ); ?>">
			<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( ! empty( $checked ) ); ?>>
			<span class="tavoos-fcb-switch__ui" aria-hidden="true"></span>
			<span><?php echo esc_html( $label ); ?></span>
		</label>
		<?php
	}

	/**
	 * Print a number input.
	 *
	 * @param array     $s    Settings.
	 * @param string    $key  Setting key.
	 * @param string    $unit Unit shown after the field.
	 * @param int|float $min  Minimum.
	 * @param int|float $max  Maximum.
	 * @param int|float $step Step.
	 */
	protected static function number( $s, $key, $unit, $min, $max, $step = 1 ) {
		?>
		<span class="tavoos-fcb-num"><input type="number" id="tavoos-fcb-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( self::name( $key ) ); ?>" value="<?php echo esc_attr( $s[ $key ] ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" step="<?php echo esc_attr( $step ); ?>" class="small-text"> <span class="tavoos-fcb-num__unit"><?php echo esc_html( $unit ); ?></span></span>
		<?php
	}

	/**
	 * Print a color picker input.
	 *
	 * @param string $name     Field name.
	 * @param string $value    Current value.
	 * @param string $fallback Default color.
	 * @param string $id       Optional element ID.
	 */
	protected static function color( $name, $value, $fallback = '', $id = '' ) {
		?>
		<input type="text" class="tavoos-fcb-color" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $fallback ); ?>"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
		<?php
	}

	/**
	 * Icon picker (preset, custom SVG or image).
	 *
	 * @param string $prefix Base field name, e.g. tavoos_fcb_options or tavoos_fcb_options[channels][0].
	 * @param array  $item   Current values.
	 */
	protected static function icon_field( $prefix, $item ) {
		$source  = isset( $item['icon_type'] ) ? $item['icon_type'] : 'preset';
		$sources = array(
			'preset' => __( 'Preset icon', 'tavoos-floating-call-button' ),
			'svg'    => __( 'Custom SVG code', 'tavoos-floating-call-button' ),
			'image'  => __( 'Image (upload)', 'tavoos-floating-call-button' ),
		);
		?>
		<div class="tavoos-fcb-icon-field" data-source="<?php echo esc_attr( $source ); ?>">
			<div class="tavoos-fcb-seg">
				<?php foreach ( $sources as $key => $label ) : ?>
					<label><input type="radio" class="tavoos-fcb-icon-source" name="<?php echo esc_attr( $prefix . '[icon_type]' ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $source, $key ); ?>><span><?php echo esc_html( $label ); ?></span></label>
				<?php endforeach; ?>
			</div>

			<div class="tavoos-fcb-icon-panel tavoos-fcb-icon-panel--preset">
				<div class="tavoos-fcb-icon-grid">
					<?php foreach ( tavoos_fcb_get_icons() as $key => $icon ) : ?>
						<?php
						if ( 'close' === $key ) {
							continue;
						}
						?>
						<label class="tavoos-fcb-icon-opt" title="<?php echo esc_attr( $icon['label'] ); ?>">
							<input type="radio" class="tavoos-fcb-icon-preset" name="<?php echo esc_attr( $prefix . '[icon]' ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $item['icon'], $key ); ?>>
							<span><?php tavoos_fcb_echo_icon( tavoos_fcb_preset_icon_svg( $key ) ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="tavoos-fcb-icon-panel tavoos-fcb-icon-panel--svg">
				<textarea class="large-text code tavoos-fcb-icon-svg" rows="4" dir="ltr" name="<?php echo esc_attr( $prefix . '[icon_svg]' ); ?>" placeholder="&lt;svg viewBox=&quot;0 0 24 24&quot;&gt;...&lt;/svg&gt;"><?php echo esc_textarea( $item['icon_svg'] ); ?></textarea>
				<p class="description">
					<?php
					printf(
						/* translators: %s: the SVG attribute fill="currentColor" */
						esc_html__( 'Paste the full SVG code. To follow the "Icon color" setting, use %s.', 'tavoos-floating-call-button' ),
						'<code>fill="currentColor"</code>'
					);
					?>
				</p>
			</div>

			<div class="tavoos-fcb-icon-panel tavoos-fcb-icon-panel--image">
				<div class="tavoos-fcb-media">
					<input type="url" class="regular-text tavoos-fcb-icon-img" dir="ltr" name="<?php echo esc_attr( $prefix . '[icon_img]' ); ?>" value="<?php echo esc_attr( $item['icon_img'] ); ?>" placeholder="https://">
					<button type="button" class="button tavoos-fcb-upload"><?php esc_html_e( 'Choose from Media Library', 'tavoos-floating-call-button' ); ?></button>
				</div>
				<p class="description"><?php esc_html_e( 'PNG, SVG or WebP with a transparent background works best.', 'tavoos-floating-call-button' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * One channel row in the repeater.
	 *
	 * @param string|int $index Row index.
	 * @param array      $c     Channel.
	 * @param bool       $open  Whether the row starts expanded.
	 */
	protected static function channel_row( $index, $c, $open = false ) {
		$types  = Tavoos_FCB_Options::channel_types();
		$type   = isset( $types[ $c['type'] ] ) ? $types[ $c['type'] ] : $types['custom'];
		$prefix = TAVOOS_FCB_OPTION . '[channels][' . $index . ']';
		$id     = 'tavoos-fcb-ch-' . $index;
		$class  = 'tavoos-fcb-ch' . ( $open ? ' is-open' : '' ) . ( empty( $c['enabled'] ) ? ' is-disabled' : '' );
		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<div class="tavoos-fcb-ch__head">
				<span class="tavoos-fcb-ch__handle dashicons dashicons-menu" title="<?php esc_attr_e( 'Drag to reorder', 'tavoos-floating-call-button' ); ?>"></span>
				<span class="tavoos-fcb-ch__preview" style="<?php echo esc_attr( '--ic-bg:' . $c['icon_bg'] . ';--ic-color:' . $c['icon_color'] ); ?>"><?php tavoos_fcb_echo_icon( tavoos_fcb_render_icon( $c ) ); ?></span>
				<button type="button" class="tavoos-fcb-ch__summary" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">
					<strong class="tavoos-fcb-ch__title"><?php echo esc_html( $c['title'] ? $c['title'] : __( 'Untitled', 'tavoos-floating-call-button' ) ); ?></strong>
					<span class="tavoos-fcb-ch__meta"><span class="tavoos-fcb-ch__type"><?php echo esc_html( $type['label'] ); ?></span> · <span class="tavoos-fcb-ch__value" dir="ltr"><?php echo esc_html( $c['value'] ? $c['value'] : __( 'No value entered', 'tavoos-floating-call-button' ) ); ?></span></span>
				</button>
				<?php self::checkbox( $prefix . '[enabled]', $c['enabled'], __( 'Enabled', 'tavoos-floating-call-button' ), 'tavoos-fcb-ch__enabled' ); ?>
				<button type="button" class="button-link tavoos-fcb-ch__toggle" aria-label="<?php esc_attr_e( 'Expand or collapse', 'tavoos-floating-call-button' ); ?>"><span class="dashicons dashicons-arrow-down-alt2"></span></button>
				<button type="button" class="button-link tavoos-fcb-ch__remove" aria-label="<?php esc_attr_e( 'Remove channel', 'tavoos-floating-call-button' ); ?>" title="<?php esc_attr_e( 'Remove', 'tavoos-floating-call-button' ); ?>"><span class="dashicons dashicons-trash"></span></button>
			</div>

			<div class="tavoos-fcb-ch__body">
				<div class="tavoos-fcb-grid">
					<div class="tavoos-fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-type"><?php esc_html_e( 'Channel type', 'tavoos-floating-call-button' ); ?></label>
						<select id="<?php echo esc_attr( $id ); ?>-type" class="tavoos-fcb-ch__type-select" name="<?php echo esc_attr( $prefix . '[type]' ); ?>">
							<?php foreach ( $types as $key => $t ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $c['type'], $key ); ?>><?php echo esc_html( $t['label'] ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="tavoos-fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-value"><?php esc_html_e( 'Number / username / link', 'tavoos-floating-call-button' ); ?></label>
						<input id="<?php echo esc_attr( $id ); ?>-value" type="text" dir="ltr" class="regular-text tavoos-fcb-ch__value-input" name="<?php echo esc_attr( $prefix . '[value]' ); ?>" value="<?php echo esc_attr( $c['value'] ); ?>" placeholder="<?php echo esc_attr( $type['placeholder'] ); ?>">
						<p class="description tavoos-fcb-ch__hint"><?php echo esc_html( $type['hint'] ); ?></p>
					</div>
					<div class="tavoos-fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-title"><?php esc_html_e( 'Title', 'tavoos-floating-call-button' ); ?></label>
						<input id="<?php echo esc_attr( $id ); ?>-title" type="text" class="regular-text tavoos-fcb-ch__title-input" name="<?php echo esc_attr( $prefix . '[title]' ); ?>" value="<?php echo esc_attr( $c['title'] ); ?>">
					</div>
					<div class="tavoos-fcb-field">
						<label for="<?php echo esc_attr( $id ); ?>-subtitle"><?php esc_html_e( 'Subtitle (optional)', 'tavoos-floating-call-button' ); ?></label>
						<input id="<?php echo esc_attr( $id ); ?>-subtitle" type="text" class="regular-text tavoos-fcb-ch__subtitle-input" name="<?php echo esc_attr( $prefix . '[subtitle]' ); ?>" value="<?php echo esc_attr( $c['subtitle'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. Quick replies from our team', 'tavoos-floating-call-button' ); ?>">
					</div>
					<div class="tavoos-fcb-field tavoos-fcb-field--full tavoos-fcb-ch__msg"<?php echo $type['message_label'] ? '' : ' hidden'; ?>>
						<label for="<?php echo esc_attr( $id ); ?>-message" class="tavoos-fcb-ch__msg-label"><?php echo esc_html( $type['message_label'] ? $type['message_label'] : __( 'Default text', 'tavoos-floating-call-button' ) ); ?></label>
						<textarea id="<?php echo esc_attr( $id ); ?>-message" class="large-text" rows="2" name="<?php echo esc_attr( $prefix . '[message]' ); ?>"><?php echo esc_textarea( $c['message'] ); ?></textarea>
					</div>
					<div class="tavoos-fcb-field tavoos-fcb-field--full">
						<?php self::checkbox( $prefix . '[new_tab]', $c['new_tab'], __( 'Open link in a new tab', 'tavoos-floating-call-button' ), 'tavoos-fcb-ch__newtab' ); ?>
					</div>
					<div class="tavoos-fcb-field tavoos-fcb-field--full">
						<span class="tavoos-fcb-label"><?php esc_html_e( 'Icon', 'tavoos-floating-call-button' ); ?></span>
						<?php self::icon_field( $prefix, $c ); ?>
					</div>
					<div class="tavoos-fcb-field">
						<span class="tavoos-fcb-label"><?php esc_html_e( 'Icon background color', 'tavoos-floating-call-button' ); ?></span>
						<?php self::color( $prefix . '[icon_bg]', $c['icon_bg'], $type['bg'] ); ?>
					</div>
					<div class="tavoos-fcb-field">
						<span class="tavoos-fcb-label"><?php esc_html_e( 'Icon color', 'tavoos-floating-call-button' ); ?></span>
						<?php self::color( $prefix . '[icon_color]', $c['icon_color'], $type['color'] ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/* ------------------------------------------------------------------ */
	/* Page                                                                */
	/* ------------------------------------------------------------------ */

	/**
	 * Render the settings page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$s     = Tavoos_FCB_Options::get();
		$types = Tavoos_FCB_Options::channel_types();
		$tabs  = array(
			'channels'   => array( 'dashicons-share', __( 'Contact channels', 'tavoos-floating-call-button' ) ),
			'appearance' => array( 'dashicons-art', __( 'Button appearance', 'tavoos-floating-call-button' ) ),
			'position'   => array( 'dashicons-move', __( 'Position', 'tavoos-floating-call-button' ) ),
			'display'    => array( 'dashicons-visibility', __( 'Display & general', 'tavoos-floating-call-button' ) ),
		);
		?>
		<div class="wrap tavoos-fcb-wrap">
			<div class="tavoos-fcb-header">
				<div class="tavoos-fcb-header__logo"><?php tavoos_fcb_echo_icon( tavoos_fcb_preset_icon_svg( 'phone' ) ); ?></div>
				<div>
					<h1><?php esc_html_e( 'Tavoos Floating Call Button', 'tavoos-floating-call-button' ); ?></h1>
					<p class="tavoos-fcb-header__credit">
						<?php
						printf(
							/* translators: 1: author name linked to the author website, 2: plugin version number */
							esc_html__( 'Designed by %1$s · Version %2$s', 'tavoos-floating-call-button' ),
							'<a href="https://tavoosweb.ir/" target="_blank" rel="noopener">' . esc_html__( 'Mahdi Habibi | Tavoos Web', 'tavoos-floating-call-button' ) . '</a>',
							esc_html( TAVOOS_FCB_VERSION )
						);
						?>
					</p>
				</div>
			</div>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" class="tavoos-fcb-form">
				<?php settings_fields( self::GROUP ); ?>

				<nav class="nav-tab-wrapper tavoos-fcb-tabs">
					<?php foreach ( $tabs as $key => $tab ) : ?>
						<a href="#<?php echo esc_attr( $key ); ?>" class="nav-tab" data-tab="<?php echo esc_attr( $key ); ?>"><span class="dashicons <?php echo esc_attr( $tab[0] ); ?>"></span> <?php echo esc_html( $tab[1] ); ?></a>
					<?php endforeach; ?>
				</nav>

				<?php /* ======================= Channels ======================= */ ?>
				<section class="tavoos-fcb-tab" data-tab="channels">
					<div class="tavoos-fcb-card">
						<h2><?php esc_html_e( 'Contact channels', 'tavoos-floating-call-button' ); ?></h2>
						<p class="description">
							<?php
							printf(
								/* translators: %s: drag handle icon */
								esc_html__( 'The ways to reach you that appear when the button is clicked. Drag %s to change the order. Channels without a value are not shown on the site.', 'tavoos-floating-call-button' ),
								'<span class="dashicons dashicons-menu"></span>'
							);
							?>
						</p>

						<div id="tavoos-fcb-channels" class="tavoos-fcb-channels">
							<?php
							foreach ( array_values( (array) $s['channels'] ) as $i => $channel ) {
								self::channel_row( $i, Tavoos_FCB_Options::channel_defaults( $channel['type'], $channel ) );
							}
							?>
						</div>

						<p class="tavoos-fcb-empty" <?php echo $s['channels'] ? 'hidden' : ''; ?>><?php esc_html_e( 'No channels yet.', 'tavoos-floating-call-button' ); ?></p>

						<div class="tavoos-fcb-add">
							<label for="tavoos-fcb-add-type"><?php esc_html_e( 'Add a new channel:', 'tavoos-floating-call-button' ); ?></label>
							<select id="tavoos-fcb-add-type">
								<?php foreach ( $types as $key => $t ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $t['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
							<button type="button" class="button button-secondary" id="tavoos-fcb-add-channel"><span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add', 'tavoos-floating-call-button' ); ?></button>
						</div>
					</div>

					<script type="text/html" id="tmpl-tavoos-fcb-channel">
						<?php self::channel_row( '__INDEX__', Tavoos_FCB_Options::channel_defaults( 'custom' ), true ); ?>
					</script>
				</section>

				<?php /* ======================= Appearance ======================= */ ?>
				<section class="tavoos-fcb-tab" data-tab="appearance">
					<div class="tavoos-fcb-card tavoos-fcb-card--split">
						<div>
							<h2><?php esc_html_e( 'Main button', 'tavoos-floating-call-button' ); ?></h2>
							<table class="form-table" role="presentation">
								<tr>
									<th scope="row"><?php esc_html_e( 'Button icon', 'tavoos-floating-call-button' ); ?></th>
									<td><?php self::icon_field( TAVOOS_FCB_OPTION, $s ); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Background color', 'tavoos-floating-call-button' ); ?></th>
									<td><?php self::color( self::name( 'bg_color' ), $s['bg_color'], '#F5B301', 'tavoos-fcb-bg-color' ); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Second color (gradient)', 'tavoos-floating-call-button' ); ?></th>
									<td>
										<?php self::color( self::name( 'bg_color2' ), $s['bg_color2'], '#FFD34D', 'tavoos-fcb-bg-color2' ); ?>
										<p class="description"><?php esc_html_e( 'Leave empty for a solid color.', 'tavoos-floating-call-button' ); ?></p>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Icon color', 'tavoos-floating-call-button' ); ?></th>
									<td><?php self::color( self::name( 'icon_color' ), $s['icon_color'], '#2B2118', 'tavoos-fcb-icon-color' ); ?></td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Button size', 'tavoos-floating-call-button' ); ?></th>
									<td>
										<label><?php esc_html_e( 'Desktop:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'size', 'px', 30, 150 ); ?></label>
										&nbsp; <label><?php esc_html_e( 'Mobile:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'mobile_size', 'px', 30, 150 ); ?></label>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php esc_html_e( 'Animation', 'tavoos-floating-call-button' ); ?></th>
									<td><?php self::checkbox( self::name( 'pulse' ), $s['pulse'], __( 'Pulse ring around the button', 'tavoos-floating-call-button' ) ); ?></td>
								</tr>
							</table>
						</div>
						<div class="tavoos-fcb-preview-box">
							<span class="tavoos-fcb-label"><?php esc_html_e( 'Preview', 'tavoos-floating-call-button' ); ?></span>
							<div class="tavoos-fcb-preview" id="tavoos-fcb-preview" dir="ltr">
								<div class="tavoos-fcb-preview__anchor" id="tavoos-fcb-preview-anchor">
									<div class="tavoos-fcb-preview__menu" id="tavoos-fcb-preview-menu" dir="<?php echo esc_attr( is_rtl() ? 'rtl' : 'ltr' ); ?>"></div>
									<span class="tavoos-fcb-preview__btn" id="tavoos-fcb-preview-btn"></span>
								</div>
							</div>
							<p class="description"><?php esc_html_e( 'The preview follows the position chosen in the Position tab.', 'tavoos-floating-call-button' ); ?></p>
						</div>
					</div>

					<div class="tavoos-fcb-card">
						<h2><?php esc_html_e( 'Channel menu appearance', 'tavoos-floating-call-button' ); ?></h2>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Card background', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::color( self::name( 'card_bg' ), $s['card_bg'], '#FFFFFF', 'tavoos-fcb-card-bg' ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Title color', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::color( self::name( 'card_text' ), $s['card_text'], '#2B2118', 'tavoos-fcb-card-text' ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Subtitle color', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::color( self::name( 'card_subtext' ), $s['card_subtext'], '#8A7D70', 'tavoos-fcb-card-subtext' ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Card border color', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::color( self::name( 'card_border' ), $s['card_border'], '#F5EAD0', 'tavoos-fcb-card-border' ); ?></td>
							</tr>
						</table>
					</div>
				</section>

				<?php /* ======================= Position ======================= */ ?>
				<section class="tavoos-fcb-tab" data-tab="position">
					<div class="tavoos-fcb-card">
						<h2><?php esc_html_e( 'Button position', 'tavoos-floating-call-button' ); ?></h2>
						<div class="tavoos-fcb-positions">
							<?php foreach ( Tavoos_FCB_Options::positions() as $key => $label ) : ?>
								<label class="tavoos-fcb-pos">
									<input type="radio" name="<?php echo esc_attr( self::name( 'position' ) ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $s['position'], $key ); ?>>
									<span class="tavoos-fcb-pos__screen tavoos-fcb-pos__screen--<?php echo esc_attr( $key ); ?>"><i></i></span>
									<span class="tavoos-fcb-pos__label"><?php echo esc_html( $label ); ?></span>
								</label>
							<?php endforeach; ?>
						</div>

						<div class="tavoos-fcb-pos-custom" data-show-when="custom">
							<h3><?php esc_html_e( 'Custom position (percent)', 'tavoos-floating-call-button' ); ?></h3>
							<p class="description"><?php esc_html_e( 'Click the screen below or drag the dot, or type the values. 0% is the left/top edge and 100% is the right/bottom edge of the screen.', 'tavoos-floating-call-button' ); ?></p>
							<div class="tavoos-fcb-pos-custom__wrap">
								<div class="tavoos-fcb-pos-custom__screen" id="tavoos-fcb-custom-screen" dir="ltr"><span id="tavoos-fcb-custom-dot"></span></div>
								<div>
									<p><label><?php esc_html_e( 'Horizontal (W) from left:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'custom_x', '%', 0, 100, 0.5 ); ?></label></p>
									<p><label><?php esc_html_e( 'Vertical (H) from top:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'custom_y', '%', 0, 100, 0.5 ); ?></label></p>
								</div>
							</div>
						</div>

						<table class="form-table" role="presentation" data-hide-when="custom">
							<tr>
								<th scope="row"><?php esc_html_e( 'Distance from edge (desktop)', 'tavoos-floating-call-button' ); ?></th>
								<td>
									<label><?php esc_html_e( 'Horizontal:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'offset_x', 'px', 0, 500 ); ?></label>
									&nbsp; <label><?php esc_html_e( 'Vertical:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'offset_y', 'px', 0, 500 ); ?></label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Distance from edge (mobile)', 'tavoos-floating-call-button' ); ?></th>
								<td>
									<label><?php esc_html_e( 'Horizontal:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'mobile_offset_x', 'px', 0, 500 ); ?></label>
									&nbsp; <label><?php esc_html_e( 'Vertical:', 'tavoos-floating-call-button' ); ?> <?php self::number( $s, 'mobile_offset_y', 'px', 0, 500 ); ?></label>
									<p class="description"><?php esc_html_e( 'For example, increase the vertical distance if your theme has a bottom bar on mobile.', 'tavoos-floating-call-button' ); ?></p>
								</td>
							</tr>
						</table>

						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Mobile breakpoint', 'tavoos-floating-call-button' ); ?></th>
								<td>
									<?php self::number( $s, 'mobile_breakpoint', 'px', 0, 3000 ); ?>
									<p class="description"><?php esc_html_e( 'Screens this wide or narrower count as mobile.', 'tavoos-floating-call-button' ); ?></p>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'z-index', 'tavoos-floating-call-button' ); ?></th>
								<td>
									<?php self::number( $s, 'z_index', '', 0, 2147483647 ); ?>
									<p class="description"><?php esc_html_e( 'Increase this if the button appears behind other parts of your site.', 'tavoos-floating-call-button' ); ?></p>
								</td>
							</tr>
						</table>
					</div>
				</section>

				<?php /* ======================= Display & general ======================= */ ?>
				<section class="tavoos-fcb-tab" data-tab="display">
					<div class="tavoos-fcb-card">
						<h2><?php esc_html_e( 'General settings', 'tavoos-floating-call-button' ); ?></h2>
						<table class="form-table" role="presentation">
							<tr>
								<th scope="row"><?php esc_html_e( 'Status', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::checkbox( self::name( 'enabled' ), $s['enabled'], __( 'Show the button on the site', 'tavoos-floating-call-button' ) ); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Devices', 'tavoos-floating-call-button' ); ?></th>
								<td>
									<?php self::checkbox( self::name( 'show_desktop' ), $s['show_desktop'], __( 'Desktop', 'tavoos-floating-call-button' ) ); ?>
									<?php self::checkbox( self::name( 'show_mobile' ), $s['show_mobile'], __( 'Mobile and tablet', 'tavoos-floating-call-button' ) ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php esc_html_e( 'Single channel', 'tavoos-floating-call-button' ); ?></th>
								<td><?php self::checkbox( self::name( 'single_direct' ), $s['single_direct'], __( 'When only one channel is enabled, link the button straight to it (no menu)', 'tavoos-floating-call-button' ) ); ?></td>
							</tr>
							<tr>
								<th scope="row"><label for="tavoos-fcb-aria"><?php esc_html_e( 'Button accessibility label', 'tavoos-floating-call-button' ); ?></label></th>
								<td><input id="tavoos-fcb-aria" type="text" class="regular-text" name="<?php echo esc_attr( self::name( 'aria_label' ) ); ?>" value="<?php echo esc_attr( $s['aria_label'] ); ?>"></td>
							</tr>
							<tr>
								<th scope="row"><label for="tavoos-fcb-direction"><?php esc_html_e( 'Menu text direction', 'tavoos-floating-call-button' ); ?></label></th>
								<td>
									<select id="tavoos-fcb-direction" name="<?php echo esc_attr( self::name( 'direction' ) ); ?>">
										<option value="auto" <?php selected( $s['direction'], 'auto' ); ?>><?php esc_html_e( 'Automatic (site language)', 'tavoos-floating-call-button' ); ?></option>
										<option value="rtl" <?php selected( $s['direction'], 'rtl' ); ?>><?php esc_html_e( 'Right to left', 'tavoos-floating-call-button' ); ?></option>
										<option value="ltr" <?php selected( $s['direction'], 'ltr' ); ?>><?php esc_html_e( 'Left to right', 'tavoos-floating-call-button' ); ?></option>
									</select>
								</td>
							</tr>
						</table>
					</div>

					<div class="tavoos-fcb-card">
						<h2><?php esc_html_e( 'Where to show', 'tavoos-floating-call-button' ); ?></h2>
						<div class="tavoos-fcb-seg tavoos-fcb-seg--block">
							<?php
							$modes = array(
								'all'     => __( 'All pages', 'tavoos-floating-call-button' ),
								'include' => __( 'Only selected pages', 'tavoos-floating-call-button' ),
								'exclude' => __( 'All pages except the selected ones', 'tavoos-floating-call-button' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Display mode key, not a query argument.
							);
							foreach ( $modes as $key => $label ) :
								?>
								<label><input type="radio" name="<?php echo esc_attr( self::name( 'display_mode' ) ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $s['display_mode'], $key ); ?>><span><?php echo esc_html( $label ); ?></span></label>
							<?php endforeach; ?>
						</div>

						<div class="tavoos-fcb-targets" data-hide-mode="all">
							<p><?php self::checkbox( self::name( 'front_page' ), $s['front_page'], __( 'Site front page', 'tavoos-floating-call-button' ) ); ?></p>

							<span class="tavoos-fcb-label"><?php esc_html_e( 'Pages', 'tavoos-floating-call-button' ); ?></span>
							<input type="search" class="regular-text tavoos-fcb-page-search" placeholder="<?php esc_attr_e( 'Search pages…', 'tavoos-floating-call-button' ); ?>">
							<div class="tavoos-fcb-pages">
								<?php
								$pages = get_pages(
									array(
										'sort_column' => 'menu_order,post_title',
										'post_status' => array( 'publish', 'private', 'draft' ),
									)
								);
								if ( ! $pages ) {
									echo '<p class="description">' . esc_html__( 'There are no pages.', 'tavoos-floating-call-button' ) . '</p>';
								}
								foreach ( $pages as $page ) :
									$depth = count( get_post_ancestors( $page ) );
									$title = $page->post_title ? $page->post_title : __( '(no title)', 'tavoos-floating-call-button' );
									?>
									<label style="<?php echo esc_attr( '--depth:' . $depth ); ?>">
										<input type="checkbox" name="<?php echo esc_attr( self::name( 'pages' ) ); ?>[]" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( (int) $page->ID, array_map( 'intval', (array) $s['pages'] ), true ) ); ?>>
										<span><?php echo esc_html( $title ); ?></span>
										<?php if ( 'publish' !== $page->post_status ) : ?>
											<em>(<?php echo esc_html( get_post_status_object( $page->post_status )->label ); ?>)</em>
										<?php endif; ?>
									</label>
								<?php endforeach; ?>
							</div>

							<p>
								<label for="tavoos-fcb-post-ids" class="tavoos-fcb-label"><?php esc_html_e( 'IDs of posts, products or any other content', 'tavoos-floating-call-button' ); ?></label>
								<input id="tavoos-fcb-post-ids" type="text" dir="ltr" class="regular-text" name="<?php echo esc_attr( self::name( 'post_ids' ) ); ?>" value="<?php echo esc_attr( $s['post_ids'] ); ?>" placeholder="12, 45, 108">
								<span class="description"><?php esc_html_e( 'Separate IDs with commas.', 'tavoos-floating-call-button' ); ?></span>
							</p>
						</div>
					</div>
				</section>

				<div class="tavoos-fcb-submit">
					<?php submit_button( __( 'Save settings', 'tavoos-floating-call-button' ), 'primary large', 'submit', false ); ?>
				</div>
			</form>
		</div>
		<?php
	}
}

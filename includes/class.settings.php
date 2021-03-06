<?php

namespace AV\TheCatApi;

// Exit if accessed directly
defined("ABSPATH") or die();

class Settings {

	/**
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Settings constructor.
	 *
	 * @param $plugin Plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin   = $plugin;
		$this->settings = $this->settings();

		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'init_settings' ] );
	}

	/**
	 * Array of the settings
	 *
	 * @return array
	 */
	public function settings() {
		$settings = [
			'general_group' => [
				'sections' => [
					[
						'title'   => 'Settings API',
						'slug'    => 'section_general',
						'options' => [
							'api_key' => [
								'title'             => 'TheCatAPI.com API key',
								'render_callback'   => [ $this, 'fill_text_field' ],
								'sanitize_callback' => [ $this, 'sanitize_callback' ],
							],
						],
					],
				]
			]
		];

		return $settings;
	}

	public function add_options_page() {
		add_options_page( 'Settings TheCatAPI', 'TheCatAPI', 'manage_options', AV_TCA_PLUGIN_PREFIX . '_settings', function () {
			?>
            <div class="wrap">
                <h2><?php echo get_admin_page_title() ?></h2>
                <form action="options.php" method="POST">
					<?php
					settings_fields( 'general_group' );     // скрытые защитные поля
					do_settings_sections( 'settings_page' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
					submit_button();
					?>
                </form>
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr>
                        <th scope="row">Shortcode:</th>
                        <td>
                            <pre>[the-cat-api]</pre>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
			<?php
		} );
	}

	public function init_settings() {
		foreach ( $this->settings as $group_slug => $group ) {
			foreach ( $group['sections'] as $section ) {
				foreach ( $section['options'] as $opt_name => $option ) {
					$opt_name = AV_TCA_PLUGIN_PREFIX . '_' . $opt_name;
					register_setting( $group_slug, $opt_name, [
						'sanitize_callback' => $option['sanitize_callback'],
						'show_in_rest'      => false,
					] );
					add_settings_field( $opt_name, $option['title'], $option['render_callback'], 'settings_page', $section['slug'], $opt_name );
				}
				add_settings_section( $section['slug'], $section['title'], '', 'settings_page' );
			}
		}
	}

	/**
	 * Get settings option
	 *
	 * @param string $option_name
	 * @param string $default_value
	 *
	 * @return false|mixed|void
	 */
	public function getOption( $option_name, $default_value = '' ) {
		$option = get_option( AV_TCA_PLUGIN_PREFIX . '_' . $option_name );

		return $option ? $option : $default_value;
	}

	/**
	 * Add settings option
	 *
	 * @param string $option_name
	 * @param string $option_value
	 *
	 * @return bool
	 */
	public function addOption( $option_name, $option_value ) {
		return add_option( AV_TCA_PLUGIN_PREFIX . '_' . $option_name, $option_value );
	}

	/**
	 * Update settings option
	 *
	 * @param string $option_name
	 * @param string $option_value
	 *
	 * @return bool
	 */
	public function updateOption( $option_name, $option_value ) {
		return update_option( AV_TCA_PLUGIN_PREFIX . '_' . $option_name, $option_value );
	}

	/**
	 * Delete settings option
	 *
	 * @param string $option_name
	 *
	 * @return bool
	 */
	public function deleteOption( $option_name ) {
		return delete_option( AV_TCA_PLUGIN_PREFIX . '_' . $option_name );
	}

	/**
	 * @param $option_name
	 */
	function fill_text_field( $option_name ) {
		$val = get_option( $option_name );
		$val = $val ? $val : '';
		?>
        <input type="text" size="50" name="<?= $option_name; ?>" value="<?php echo esc_attr( $val ) ?>"/>
		<?php
	}

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function sanitize_callback( $value ) {
		if ( is_string( $value ) ) {
			return strip_tags( $value );
		}

		if ( is_numeric( $value ) ) {
			return intval( $value );
		}

		return $value;
	}
}
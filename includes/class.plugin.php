<?php

namespace AV\TheCatApi;

defined("ABSPATH") or die();

class Plugin {

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var API
	 */
	public $api;

	/**
	 * My Plugin constructor.
	 */
	public function __construct() {
		$this->settings = new Settings( $this );

		$api_key   = $this->settings->getOption( 'api_key' );
		$this->api = new Api( $api_key );

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_assets' ] );

		add_action( 'wp_ajax_av-thecatapi-get-breed', [ $this, 'get_breed' ] );
		add_action( 'wp_ajax_nopriv_av-thecatapi-get-breed', [ $this, 'get_breed' ] );

		add_shortcode( 'the-cat-api', [ $this, 'shortcode' ] );
	}

	public function wp_enqueue_assets() {
		wp_enqueue_script( AV_TCA_PLUGIN_PREFIX . '_js', AV_TCA_PLUGIN_URL . "/assets/script.js", [ 'jquery' ], '', true );
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	public function shortcode( $args ) {
		$args = shortcode_atts( [], $args );

		$breeds = get_transient( AV_TCA_PLUGIN_PREFIX . '_breeds' );
		if ( ! $breeds ) {
			$breeds = $this->api->get_breeds();
			set_transient( AV_TCA_PLUGIN_PREFIX . '_breeds', $breeds, WEEK_IN_SECONDS );
		}
		$args['breeds'] = $breeds;

		return $this->render_template( 'shortcode', $args );
	}

	public function get_breed() {
		check_ajax_referer( 'thecatapi', 'nonce' );

		if ( isset( $_POST['breed_id'] ) ) {
			$breed_id = $_POST['breed_id'];

			$bread  = $this->api->get_breed( $breed_id );
			$bread  = array_shift( $bread );
			$result = [
				'image'       => $bread['url'] ?? '',
				'description' => $bread['breeds'][0]['description'] ?? '',
			];
			wp_send_json_success( $result );
		}
	}

	/**
	 * Method renders layout template
	 *
	 * @param string $template_name Template name without ".php"
	 * @param array $args Template arguments
	 *
	 * @return false|string
	 */
	public static function render_template( $template_name, $args = [] ) {
		$template_name = apply_filters( AV_TCA_PLUGIN_PREFIX . '/template/name', $template_name, $args );

		$path = AV_TCA_PLUGIN_DIR . "/templates/$template_name.php";
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;

			return apply_filters( AV_TCA_PLUGIN_PREFIX . '/content/template', ob_get_clean(), $template_name, $args );
		} else {
			return apply_filters( AV_TCA_PLUGIN_PREFIX . '/message/template_not_found', 'This template does not exist!' );
		}
	}
}
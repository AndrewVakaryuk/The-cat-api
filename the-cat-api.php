<?php
/**
 * Plugin Name:       TheCat API (TCA)
 * Description:       Plagin for getting data from thecatapi.com
 * Version:           1.0.0
 * Author:            Andrii Vakariuk (AV)
 * Author URI:        https://github.com/AndrewVakaryuk
 */

// Exit if accessed directly
defined("ABSPATH") or die();

define( 'AV_TCA_PLUGIN_DIR', __DIR__ );
define( 'AV_TCA_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'AV_TCA_PLUGIN_URL', plugins_url( null, __FILE__ ) );
define( 'AV_TCA_PLUGIN_PREFIX', 'av_tca' );

require_once AV_TCA_PLUGIN_DIR . "/includes/class.plugin.php";
require_once AV_TCA_PLUGIN_DIR . "/includes/class.settings.php";
require_once AV_TCA_PLUGIN_DIR . "/includes/api/class.the-cat-api.php";

try {
	new AV\TheCatApi\Plugin();
} catch ( Exception $e ) {
	$mpn_plugin_error_func = function () use ( $e ) {
		$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'TheCat API', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $mpn_plugin_error_func );
	add_action( 'network_admin_notices', $mpn_plugin_error_func );
}

<?php
/**
 * Plugin Name:          Pushover Integration for WooCommerce
 * Plugin URI:           https://shopplugins.com/
 * Description:          Integrates WooCommerce with Pushover.net, a versatile push notification service that delivers real-time alerts to Android, iOS, and desktop devices.
 * Version:              1.0.20
 * Author:               Shop Plugins
 * Author URI:           https://shopplugins.com/
 * WC requires at least: 3.0
 * WC tested up to:      9.2
*/
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Required functions
 */
if ( class_exists( 'WC_Pushover' ) ) {
	return;
}

define( 'WC_PUSHOVER_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Adds plugin localization
 * Domain: et_automatic_updates
 *
 * @return void
 */
function wc_pushover_localization() {
	load_plugin_textdomain( 'wc_pushover', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'wc_pushover_localization' );

/**
 * Plugin activation check
 */
function wc_pushover_activation_check() {

	// Verify WooCommerce is installed and active
	$active_plugins = (array) get_option( 'active_plugins', array() );

	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( ! ( in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( 'This plugin requires WooCommerce to be installed and active.' );
	}

	// verify that SimpleXML library is available
	if ( ! function_exists( 'simplexml_load_string' ) ) {
		deactivate_plugins( basename( __FILE__ ) );
		wp_die( "Sorry, but you can't run this plugin, it requires the SimpleXML library installed on your server/hosting to function." );
	}

}
register_activation_hook( __FILE__, 'wc_pushover_activation_check' );

/**
 * wc_pushover_init function.
 *
 * @access public
 * @return void
 */
function wc_pushover_init() {
	include_once 'classes/class-wc-pushover.php';
}
add_action( 'woocommerce_integrations_init', 'wc_pushover_init' );

function add_pushover_integration( $integrations ) {
	$integrations[] = 'WC_Pushover';
	return $integrations;
}
add_filter( 'woocommerce_integrations', 'add_pushover_integration' );

/**
 * Declares WooCommerce HPOS compatibility.
 *
 * @return void
 */
function wc_pushover_hpos_compatible(): void {
	if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}
add_action( 'before_woocommerce_init', 'wc_pushover_hpos_compatible' );

/**
 * Plugin page links
 * @param array $links
 * @return array
 */
function wc_pushover_plugin_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=integration&section=pushover' ) . '">' . __( 'Settings', 'wc_pushover' ) . '</a>',
		'<a href="https://shopplugins.com/support">' . __( 'Support', 'wc_pushover' ) . '</a>',
		'<a href="https://wordpress.org/plugins/pushover-for-woocommerce/installation/">' . __( 'Docs', 'wc_pushover' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_pushover_plugin_links' );

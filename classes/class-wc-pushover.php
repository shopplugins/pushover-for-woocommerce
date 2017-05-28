<?php
/**
 * WC_Pushover class.
 */
/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Pushover extends WC_Integration {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	public function __construct() {

		$this->id                 = 'pushover';
		$this->method_title       = __( 'Pushover', 'wc_pushover' );
		$this->method_description = __( 'Pushover makes it easy to send real-time notifications to your Android and iOS devices.', 'wc_pushover' );

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables
		$this->enabled           = isset( $this->settings['enabled'] ) && $this->settings['enabled'] == 'yes' ? true : false;
		$this->site_api          = isset( $this->settings['site_api'] ) ? $this->settings['site_api'] : '';
		$this->user_api          = isset( $this->settings['user_api'] ) ? $this->settings['user_api'] : '';
		$this->device            = isset( $this->settings['device'] ) ? $this->settings['device'] : '';
		$this->priority          = isset( $this->settings['priority'] ) ? $this->settings['priority'] : '';
		$this->debug             = isset( $this->settings['debug'] ) && $this->settings['debug'] == 'yes' ? true : false;

		// Notices
		$this->notify_new_order  = isset( $this->settings['notify_new_order'] ) && $this->settings['notify_new_order'] == 'yes' ? true : false;
		$this->notify_free_order = isset( $this->settings['notify_free_order'] ) && $this->settings['notify_free_order'] == 'yes' ? true : false;
		$this->notify_backorder  = isset( $this->settings['notify_backorder'] ) && $this->settings['notify_backorder'] == 'yes' ? true : false;
		$this->notify_no_stock   = isset( $this->settings['notify_no_stock'] )  && $this->settings['notify_no_stock'] == 'yes' ? true : false;
		$this->notify_low_stock  = isset( $this->settings['notify_low_stock'] ) && $this->settings['notify_low_stock'] == 'yes' ? true : false;

		// Actions
		add_action( 'woocommerce_update_options_integration_pushover', array( &$this, 'process_admin_options') );
		add_action( 'init', array( $this, 'wc_pushover_init' ), 10 );

		if ( $this->notify_new_order )
			add_action( 'woocommerce_thankyou', array( $this, 'notify_new_order' ) );
		if ( $this->notify_backorder )
			add_action( 'woocommerce_product_on_backorder', array( $this, 'notify_backorder' ) );
		if ( $this->notify_no_stock )
			add_action( 'woocommerce_notify_no_stock', array( $this, 'notify_no_stock' ) );
		if ( $this->notify_low_stock )
			add_action( 'woocommerce_notify_low_stock', array( $this, 'notify_low_stock' ) );

	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {

		$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'wc_pushover' ),
				'label'       => __( 'Enable sending of notifications', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'site_api' => array(
				'title'       => __( 'Site API Token', 'wc_pushover' ),
				'description' => __( 'Get your token <a href="https://pushover.net/" target="_blank">here</a>', 'wc_pushover' ),
				'type'        => 'text',
				'default'     => '',
			),
			'user_api' => array(
				'title'       => __( 'User API Token', 'wc_pushover' ),
				'description' => __( '', 'wc_pushover' ),
				'type'        => 'text',
				'default'     => '',
			),
			'priority' => array(
				'title'       => __( 'Priority', 'wc_pushover' ),
				'description' => __( 'Set priority of message. <a href="https://pushover.net/api#priority">Priorities explained.</a>', 'wc_pushover' ),
				'type'        => 'select',
				'options'     => array(
									'-2' => __( '-2 Lowest Priority', 'wc_pushover'),
									'-1' => __( '-1 Low Priority', 'wc_pushover'),
									'0'  => __( '0 Normal', 'wc_pushover'),
									'1'  => __( '1 High', 'wc_pushover'),
									'2'  => __( '2 Emergency Priority', 'wc_pushover'),
								),
				'default'     => '0',
			),
			'device' => array(
				'title'       => __( 'Device', 'wc_pushover' ),
				'description' => __( 'Optional: Name of device to send notifications', 'wc_pushover' ),
				'type'        => 'text',
				'default'     => '',
			),
			'debug' => array(
				'title'       => __( 'Debug', 'wc_pushover' ),
				'description' => __( 'Enable debug logging', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notifications' => array(
				'title'       => __( 'Notifications', 'wc_pushover' ),
				'type'        => 'title',
			),
			'notify_new_order' => array(
				'title'       => __( 'New Order', 'wc_pushover' ),
				'label'       => __( 'Send notification when a new order is received.', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notify_free_order' => array(
				'title'       => __( 'Free Order', 'wc_pushover' ),
				'label'       => __( 'Send notification when an order totals $0.', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notify_backorder' => array(
				'title'       => __( 'Back Order', 'wc_pushover' ),
				'label'       => __( 'Send notification when a product is back ordered.', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notify_no_stock' => array(
				'title'       => __( 'No Stock', 'wc_pushover' ),
				'label'       => __( 'Send notification when a product has no stock.', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notify_low_stock' => array(
				'title'       => __( 'Low Stock', 'wc_pushover' ),
				'label'       => __( 'Send notification when a product hits the low stock.', 'wc_pushover' ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'test_button' => array(
				'type'        => 'test_button',
			),

		);

	} // End init_form_fields()

	/**
	 * Send notification when new order is received
	 *
	 * @access public
	 * @return void
	 */
	function wc_pushover_init() {

		if ( isset($_GET['wc_test']) && ($_GET['wc_test']==1)){
			$title   = __( 'Test Notification', 'wc_pushover');
			$message = sprintf(__( 'This is a test notification from %s', 'wc_pushover'), get_bloginfo('name'));
			$url     = get_admin_url();

			$this->send_notification( $title, $message, $url);

			wp_safe_redirect( get_admin_url() . 'admin.php?page=wc-settings&tab=integration&section=pushover' );
		}
	}

	/**
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param $order_id
	 * @return void
	 */
	function notify_new_order( $order_id ) {

		$order = new WC_Order( $order_id );
		$sent = get_post_meta( $order_id, '_pushover_new_order', true );
		if ( version_compare( WC()->version, '3.0.0', '>=' ) ){
			$items = $order->get_items();
			$names = array();
			foreach( $items as $item ){
				$names[] = $item->get_name();
			}
			$products = implode( ', ', $names );
		} else {
			$products = implode( ', ', wp_list_pluck( $order->get_items(), 'name' ) );
		}

		if ( ! $sent ) {
			// Send notifications if order total is greater than $0
			// Or if free order notification is enabled
			if ( 0 < absint( $order->order_total ) || $this->notify_free_order ) {
				$title   = sprintf( __( 'New Order %d', 'wc_pushover' ), $order_id );
				$message = sprintf(
					__( '%1$s ordered %2$s for %3$s ', 'wc_pushover' ),
					$order->billing_first_name . " " . $order->billing_last_name,
					$products,
					$this->pushover_get_currency_symbol() . $order->order_total
				);
				$url     = get_admin_url();

				$this->send_notification( $title, $message, $url );

				add_post_meta( $order_id, '_pushover_new_order', true );
			}
		}

	}

	/**
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param $args
	 * @return void
	 */
	function notify_backorder( $args ) {

		$product  = $args['product'];
		$order_id = $args['order_id'];
		$title    = sprintf( __( 'Product Backorder', 'wc_pushover' ), $order_id );
		$message  = sprintf( __( 'Product (#%d %s) is on backorder.', 'wc_pushover' ), $product->id, $product->get_title() );
		$url      = get_admin_url();

		$this->send_notification( $title, $message, $url );

	}

	/**
	 * notify_no_stock
	 *
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param WC_Product $product
	 * @return void
	 */
	function notify_no_stock( WC_Product $product ) {

		$title   = __( 'Product Out of Stock', 'wc_pushover' );
		$message = sprintf( __( 'Product %s %s is now out of stock.', 'wc_pushover' ), $product->get_id(), $product->get_title() );
		$url     = get_admin_url();

		$this->send_notification( $title, $message, $url );

	}

	/**
	 * notify_low_stock
	 *
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param WC_Product $product
	 * @return void
	 */
	function notify_low_stock( WC_Product $product ) {

		// get order details
		$title   = __( 'Product Low Stock', 'wc_pushover' );
		$message = sprintf( __( 'Product %s %s now has low stock.', 'wc_pushover' ), $product->get_id(), $product->get_title() );
		$url     = get_admin_url();

		$this->send_notification( $title, $message, $url );

	}

	/**
	 * send_notification
	 *
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param $title
	 * @param $message
	 * @param string $url
	 * @return void
	 */
	function send_notification( $title, $message, $url = '' ) {

		if ( ! class_exists( 'Pushover_Api' ) )
			include_once( 'class-pushover-api.php' );

		$pushover = new Pushover_Api();

		// check settings, if not return
		if ( ( '' == $this->site_api ) || ( '' == $this->user_api ) ) {
			$this->add_log( __('Site API or User API setting is missing.  Notification not sent.', 'wc_pushover') );
			return;
		}

		// Setup settings
		$pushover->setSiteApi( $this->site_api );
		$pushover->setUserApi( $this->user_api );
		if ( '' != $this->device ) {
			$pushover->setDevice( $this->device );
		}
		$pushover->setPriority( $this->priority );

		// Setup message
		$pushover->setTitle ( $title );
		$pushover->setMessage( $message );
		$pushover->setUrl( $url );
		$response = '';

		$this->add_log( __( 'Sending: ', 'wc_pushover' ) .
							"\nTitle: ". $title .
							"\nMessage: ". $message .
							"\nURL: " . $url .
						    "\nPriority: " . $this->priority );

		try {
			$response = $pushover->send();
			$this->add_log( __( 'Response: ', 'wc_pushover' ) . "\n" . print_r( $response,true ) );

		} catch ( Exception $e ) {
			$this->add_log( sprintf( __( 'Error: Caught exception from send method: %s', 'wc_pushover' ), $e->getMessage() ) );
		}
		
		$this->add_log( __('Pushover response', 'wc_pushover') .  "\n" . print_r($response,true) ); 

	}

	/**
	 * generate_test_button_html()
	 *
	 * @access public
	 */
	function generate_test_button_html() {
		ob_start();
		?>
		<tr id="service_options">
			<th scope="row" class="titledesc"><?php _e( 'Send Test', 'wc_pushover' ); ?></th>
			<td >
			<p><a href="<?php echo get_admin_url(); ?>admin.php?page=wc-settings&tab=integration&section=pushover&wc_test=1" class="button" ><?php _e( 'Send Test Notification', 'wc_pushover' ); ?></a></p>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * add_log
	 *
	 * @access public
	 * @param $message string
	 * @return void
	 */
	function add_log( $message ) {

		if ( ! $this->debug ) return;

		$time = date_i18n( 'm-d-Y @ H:i:s -' );
		$handle = fopen( WC_PUSHOVER_DIR . 'debug_pushover.log', 'a' );
		if ( $handle ) {
			fwrite( $handle, $time . ' ' . $message . "\n" );
			fclose( $handle );
		}

	}

	/**
	 * pushover_get_currency_symbol
	 *
	 * @access public
	 * @return string
	 * @since 1.0.2
	 */
	function pushover_get_currency_symbol() {
		$currency = get_woocommerce_currency();

		switch ( $currency ) {
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'AUD' :
			case 'CAD' :
			case 'MXN' :
			case 'NZD' :
			case 'HKD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '$';
				break;
			case 'EUR' :
				$currency_symbol = '€';
				break;
			case 'CNY' :
			case 'RMB' :
			case 'JPY' :
				$currency_symbol = '¥‎';
				break;
			case 'RUB' :
				$currency_symbol = 'руб.';
				break;
			case 'KRW' : $currency_symbol = '₩'; break;
			case 'TRY' : $currency_symbol = 'TL'; break;
			case 'NOK' : $currency_symbol = 'kr'; break;
			case 'ZAR' : $currency_symbol = 'R'; break;
			case 'CZK' : $currency_symbol = 'Kč'; break;
			case 'MYR' : $currency_symbol = 'RM'; break;
			case 'DKK' : $currency_symbol = 'kr'; break;
			case 'HUF' : $currency_symbol = 'Ft'; break;
			case 'IDR' : $currency_symbol = 'Rp'; break;
			case 'INR' : $currency_symbol = '₹'; break;
			case 'ILS' : $currency_symbol = '₪'; break;
			case 'PHP' : $currency_symbol = '₱'; break;
			case 'PLN' : $currency_symbol = 'zł'; break;
			case 'SEK' : $currency_symbol = 'kr'; break;
			case 'CHF' : $currency_symbol = 'CHF'; break;
			case 'TWD' : $currency_symbol = 'NT$'; break;
			case 'THB' : $currency_symbol = '฿'; break;
			case 'GBP' : $currency_symbol = '£'; break;
			case 'RON' : $currency_symbol = 'lei'; break;
			default    : $currency_symbol = ''; break;
		}

		return apply_filters( 'pushover_currency_symbol', $currency_symbol, $currency );
	}

} /* class WC_Pushover */
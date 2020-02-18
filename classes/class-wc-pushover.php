<?php
/**
 * WC_Pushover class.
 */
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Pushover extends WC_Integration {

	/**
	 * desc
	 *
	 * @var string
	 */
	public $site_api = '';

	/**
	 * desc
	 *
	 * @var string
	 */
	public $user_api = '';

	/**
	 * desc
	 *
	 * @var string
	 */
	public $device = '';

	/**
	 * desc
	 *
	 * @var string
	 */
	public $priority = '';

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $debug = false;

	/**
	 * desc
	 *
	 * @var string
	 */
	public $sound = '';

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $notify_new_order = false;

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $notify_free_order = false;

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $notify_backorder = false;

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $notify_no_stock = false;

	/**
	 * desc
	 *
	 * @var bool
	 */
	public $notify_low_stock = false;

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
		$this->enabled  = isset( $this->settings['enabled'] ) && 'yes' === $this->settings['enabled'] ? true : false;
		$this->site_api = isset( $this->settings['site_api'] ) ? $this->settings['site_api'] : '';
		$this->user_api = isset( $this->settings['user_api'] ) ? $this->settings['user_api'] : '';
		$this->device   = isset( $this->settings['device'] ) ? $this->settings['device'] : '';
		$this->priority = isset( $this->settings['priority'] ) ? $this->settings['priority'] : '';
		$this->debug    = isset( $this->settings['debug'] ) && 'yes' === $this->settings['debug'] ? true : false;
		$this->sound    = isset( $this->settings['sound'] ) ? $this->settings['sound'] : '';

		// Notices
		$this->notify_new_order  = isset( $this->settings['notify_new_order'] ) && 'yes' === $this->settings['notify_new_order'] ? true : false;
		$this->notify_free_order = isset( $this->settings['notify_free_order'] ) && 'yes' === $this->settings['notify_free_order'] ? true : false;
		$this->notify_backorder  = isset( $this->settings['notify_backorder'] ) && 'yes' === $this->settings['notify_backorder'] ? true : false;
		$this->notify_no_stock   = isset( $this->settings['notify_no_stock'] ) && 'yes' === $this->settings['notify_no_stock'] ? true : false;
		$this->notify_low_stock  = isset( $this->settings['notify_low_stock'] ) && 'yes' === $this->settings['notify_low_stock'] ? true : false;

		// Actions
		add_action( 'woocommerce_update_options_integration_pushover', array( &$this, 'process_admin_options' ) );
		add_action( 'init', array( $this, 'wc_pushover_init' ), 10 );

		if ( $this->notify_new_order ) {
			add_action( 'woocommerce_thankyou', array( $this, 'notify_new_order' ) );
		}
		if ( $this->notify_backorder ) {
			add_action( 'woocommerce_product_on_backorder', array( $this, 'notify_backorder' ) );
		}
		if ( $this->notify_no_stock ) {
			add_action( 'woocommerce_no_stock', array( $this, 'notify_no_stock' ) );
		}
		if ( $this->notify_low_stock ) {
			add_action( 'woocommerce_low_stock', array( $this, 'notify_low_stock' ) );
		}

	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'wc_pushover' ),
				'label'   => __( 'Enable sending of notifications', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'site_api'           => array(
				'title'       => __( 'API Token', 'wc_pushover' ),
				'description' => sprintf(
					'%s <a href="https://pushover.net/apps/clone/woocommerce" target="_blank">%s</a>',
					__( 'Create a token', 'wc_pushover' ),
					__( 'here', 'wc_pushover' )
				),
				'type'        => 'text',
				'default'     => '',
			),
			'user_api'           => array(
				'title'       => __( 'User Key', 'wc_pushover' ),
				'description' => sprintf(
					'%s <a href="https://pushover.net/dashboard" target="_blank">%s</a>',
					__( 'Find your user key', 'wc_pushover' ),
					__( 'here', 'wc_pushover' )
				),
				'type'        => 'text',
				'default'     => '',
			),
			'priority'           => array(
				'title'       => __( 'Priority', 'wc_pushover' ),
				'description' => sprintf(
					'%s <a href="https://pushover.net/api#priority" target="_blank">%s</a>',
					__( 'Set priority of message.', 'wc_pushover' ),
					__( 'Priorities explained.', 'wc_pushover' )
				),
				'type'        => 'select',
				'options'     => array(
					'-2' => __( '-2 Lowest Priority', 'wc_pushover' ),
					'-1' => __( '-1 Low Priority', 'wc_pushover' ),
					'0'  => __( '0 Normal', 'wc_pushover' ),
					'1'  => __( '1 High', 'wc_pushover' ),
					'2'  => __( '2 Emergency Priority', 'wc_pushover' ),
				),
				'default'     => '0',
			),
			'sound'              => array(
				'title'       => __( 'Notification Sound', 'wc_pushover' ),
				'description' => sprintf(
					'%s <a href="https://pushover.net/api#sounds" target="_blank">%s</a>',
					__( 'Select from', 'wc_pushover' ),
					__( 'here', 'wc_pushover' )
				),
				'type'        => 'text',
				'default'     => '',
			),
			'device'             => array(
				'title'       => __( 'Device', 'wc_pushover' ),
				'description' => __( 'Optional: Name of device to send notifications', 'wc_pushover' ),
				'type'        => 'text',
				'default'     => '',
			),
			'debug'              => array(
				'title'       => __( 'Debug', 'wc_pushover' ),
				'description' => sprintf( __( 'Enable debug logging. View log <a href="%s">here</a>.', 'wc_pushover' ), admin_url('admin.php?page=wc-status&tab=logs') ),
				'type'        => 'checkbox',
				'default'     => 'no',
			),
			'notifications'      => array(
				'title' => __( 'Notifications', 'wc_pushover' ),
				'type'  => 'title',
			),
			'notify_new_order'   => array(
				'title'   => __( 'New Order', 'wc_pushover' ),
				'label'   => __( 'Send notification when a new order is received.', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'notify_free_order'  => array(
				'title'   => __( 'Free Order', 'wc_pushover' ),
				'label'   => __( 'Send notification when an order totals $0.', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'notify_backorder'   => array(
				'title'   => __( 'Back Order', 'wc_pushover' ),
				'label'   => __( 'Send notification when a product is back ordered.', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'notify_no_stock'    => array(
				'title'   => __( 'No Stock', 'wc_pushover' ),
				'label'   => __( 'Send notification when a product has no stock.', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'notify_low_stock'   => array(
				'title'   => __( 'Low Stock', 'wc_pushover' ),
				'label'   => __( 'Send notification when a product hits the low stock.', 'wc_pushover' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'messages'           => array(
				'title' => __( 'Messages', 'wc_pushover' ),
				'type'  => 'title',
			),
			'title_new_order'    => array(
				'title'       => __( 'New Order', 'wc_pushover' ),
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Title', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'%s {Order Id}',
					__( 'New Order', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'message_new_order'  => array(
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Message', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'{First Name} {Last Name} %s {Products} %s {Currency Symbol}{Total}',
					__( 'ordered', 'wc_pushover' ),
					__( 'for', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'title_free_order'   => array(
				'title'       => __( 'Free Order', 'wc_pushover' ),
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Title', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'%s {Order Id}',
					__( 'New Order', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'message_free_order' => array(
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Message', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'{First Name} {Last Name} %s {Products} %s {Currency Symbol}{Total}',
					__( 'ordered', 'wc_pushover' ),
					__( 'for', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'title_backorder'    => array(
				'title'       => __( 'Back Order', 'wc_pushover' ),
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Title', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}, {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Product Backorder', 'wc_pushover' ),
				'css'         => 'width: 100%',
			),
			'message_backorder'  => array(
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Message', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {First Name}, {Last Name}, {Phone}, {Order Id}, {Products}, {Total}, {Currency}, {Currency Symbol}, {Payment Method}, {Order Status}, {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'%s (#{Product Id} {Product Name}) %s.',
					__( 'Product', 'wc_pushover' ),
					__( 'is on backorder', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'title_no_stock'     => array(
				'title'       => __( 'No Stock', 'wc_pushover' ),
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Title', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Product Out of Stock', 'wc_pushover' ),
				'css'         => 'width: 100%',
			),
			'message_no_stock'   => array(
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Message', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'%s (#{Product Id} {Product Name}) %s.',
					__( 'Product', 'wc_pushover' ),
					__( 'is now out of stock', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'title_low_stock'    => array(
				'title'       => __( 'Low Stock', 'wc_pushover' ),
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Title', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => __( 'Product Low Stock', 'wc_pushover' ),
				'css'         => 'width: 100%',
			),
			'message_low_stock'  => array(
				'description' => sprintf( '%s<br>%s:', __( 'Optional: Custom Message', 'wc_pushover' ), __( 'Fields', 'wc_pushover' ) ) . ' {Product Id}, {Product Name}, {Product Url}',
				'type'        => 'text',
				'default'     => '',
				'placeholder' => sprintf(
					'%s (#{Product Id} {Product Name}) %s.',
					__( 'Product', 'wc_pushover' ),
					__( 'now has low stock', 'wc_pushover' )
				),
				'css'         => 'width: 100%',
			),
			'test_button'        => array(
				'type' => 'test_button',
			),

		);

	} // End init_form_fields()

	/**
	 * Send notification when new order is received
	 *
	 * @access public
	 * @return void
	 */
	public function wc_pushover_init() {

		if ( isset( $_GET['wc_test'] ) && ( 1 === $_GET['wc_test'] ) ) {
			$title   = __( 'Test Notification', 'wc_pushover' );
			$message = sprintf( __( 'This is a test notification from %s', 'wc_pushover' ), get_bloginfo( 'name' ) );
			$url     = get_admin_url();

			$this->send_notification(
				array(
					'title'   => $title,
					'message' => $message,
					'url'     => $url,
				)
			);

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
	public function notify_new_order( $order_id ) {

		$order = new WC_Order( $order_id );
		$sent  = get_post_meta( $order_id, '_pushover_new_order', true );

		if ( ! $sent ) {

			$order_total = $order->get_total();
			// Send notifications if order total is greater than $0
			// Or if free order notification is enabled
			if ( 0 < absint( $order_total ) || $this->notify_free_order ) {

				$type  = 0 === absint( $order_total ) ? 'free_order' : 'new_order';
				$title = ! empty( $this->settings[ 'title_' . $type ] ) ? $this->replace_fields_custom_message( $this->settings[ 'title_' . $type ], $order ) : sprintf( __( 'New Order %d', 'wc_pushover' ), $order_id );

				$message = ! empty( $this->settings[ 'message_' . $type ] ) ? $this->replace_fields_custom_message( $this->settings[ 'message_' . $type ], $order ) : sprintf(
					__( '%1$s ordered %2$s for %3$s ', 'wc_pushover' ),
					$order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
					$this->get_ordered_products_string( $order ),
					$this->pushover_get_currency_symbol() . $order_total
				);

				$url     = get_admin_url().'post.php?post='.$order_id.'&action=edit';

				$args = array(
					'title'   => $title,
					'message' => $message,
					'url'     => $url,
				);

				if ( 'free_order' === $type ) {
					$this->send_notification( apply_filters( 'wc_pushover_notify_free_order', $args ) );
				} else {
					$this->send_notification( apply_filters( 'wc_pushover_notify_new_order', $args ) );
				}

				add_post_meta( $order_id, '_pushover_new_order', true );
			}
		}

	}

	/**
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param array  $args
	 * @return void
	 */
	public function notify_backorder( $args ) {

		$product  = $args['product'];
		$order_id = $args['order_id'];

		$title   = ! empty( $this->settings['title_backorder'] ) ? $this->replace_fields_custom_message( $this->settings['title_backorder'], new WC_Order( $args['order_id'] ), $product ) : sprintf( __( 'Product Backorder', 'wc_pushover' ), $order_id );
		$message = ! empty( $this->settings['message_backorder'] ) ? $this->replace_fields_custom_message( $this->settings['message_backorder'], new WC_Order( $args['order_id'] ), $product ) : sprintf( __( 'Product (#%1$d %2$s) is on backorder.', 'wc_pushover' ), $product->get_id(), $product->get_title() );

		$url = get_admin_url();

		$this->send_notification(
			apply_filters(
				'wc_pushover_notify_backorder', array(
					'title'   => $title,
					'message' => $message,
					'url'     => $url,
				)
			)
		);

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
	public function notify_no_stock( WC_Product $product ) {

		$title   = ! empty( $this->settings['title_no_stock'] ) ? $this->replace_fields_custom_message( $this->settings['title_no_stock'], null, $product ) : __( 'Product Out of Stock', 'wc_pushover' );
		$message = ! empty( $this->settings['message_no_stock'] ) ? $this->replace_fields_custom_message( $this->settings['message_no_stock'], null, $product ) : sprintf( __( 'Product (#%1$d %2$s) is now out of stock.', 'wc_pushover' ), $product->get_id(), $product->get_title() );
		$url     = get_admin_url();

		$this->send_notification(
			apply_filters(
				'wc_pushover_notify_no_stock', array(
					'title'   => $title,
					'message' => $message,
					'url'     => $url,
				)
			)
		);

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
	public function notify_low_stock( WC_Product $product ) {

		// get order details
		$title   = ! empty( $this->settings['title_low_stock'] ) ? $this->replace_fields_custom_message( $this->settings['title_low_stock'], null, $product ) : __( 'Product Low Stock', 'wc_pushover' );
		$message = ! empty( $this->settings['message_low_stock'] ) ? $this->replace_fields_custom_message( $this->settings['message_low_stock'], null, $product ) : sprintf( __( 'Product (#%1$d %2$s) now has low stock.', 'wc_pushover' ), $product->get_id(), $product->get_title() );
		$url     = get_admin_url();

		$this->send_notification(
			apply_filters(
				'wc_pushover_notify_low_stock', array(
					'title'   => $title,
					'message' => $message,
					'url'     => $url,
				)
			)
		);

	}

	/**
	 * Replaces the fields for custom messages and titles
	 *
	 * @param string $custom_string
	 * @param WC_Order $order
	 * @param WC_Product $product
	 *
	 * @return mixed|string
	 */
	protected function replace_fields_custom_message( $custom_string, $order = null, $product = null ) {

		if ( ! empty( $order ) ) {
			$custom_string = str_replace(
				array(
					'{First Name}',
					'{Last Name}',
					'{Phone}',
					'{Order Id}',
					'{Products}',
					'{Total}',
					'{Currency}',
					'{Currency Symbol}',
					'{Payment Method}',
					'{Order Status}',
				),
				array(
					$order->get_billing_first_name(),
					$order->get_billing_last_name(),
					$order->get_billing_phone(),
					$order->get_id(),
					$this->get_ordered_products_string( $order ),
					$order->get_total(),
					get_woocommerce_currency(),
					$this->pushover_get_currency_symbol(),
					$order->get_payment_method_title(),
					$order->get_status(),
				),
				$custom_string
			);
		}

		if ( ! empty( $product ) ) {
			$custom_string = str_replace(
				array(
					'{Product Id}',
					'{Product Name}',
					'{Product Url}',
				),
				array(
					$product->get_id(),
					$product->get_title(),
					get_permalink( $product->get_id() ),
				),
				$custom_string
			);
		}

		return $custom_string;

	}

	/**
	 * Get ordered products in string
	 *
	 * @param WC_Order $order
	 *
	 * @return string of products
	 */
	protected function get_ordered_products_string( $order ) {
		$items = $order->get_items();
		$names = array();
		foreach ( $items as $item ) {
			$names[] = $item->get_name();
		}
		$products = implode( ', ', $names );

		return $products;
	}

	/**
	 * send_notification
	 *
	 * Send notification when new order is received
	 *
	 * @access public
	 * @param $args {
	 *     @type string title   Push title
	 *     @type string message Push message
	 *     @type string url     URL of admin area
	 * }
	 * @return void
	 */
	public function send_notification( $args ) {

		if ( ! class_exists( 'Pushover_Api' ) ) {
			include_once 'class-pushover-api.php';
		}

		$pushover = new Pushover_Api();

		// check settings, if not return
		if ( ( '' === $this->site_api ) || ( '' === $this->user_api ) ) {
			$this->add_log( __( 'Site API or User API setting is missing.  Notification not sent.', 'wc_pushover' ) );
			return;
		}

		// Setup settings
		$pushover->setSiteApi( $this->site_api );
		$pushover->setUserApi( $this->user_api );
		if ( '' !== $this->device ) {
			$pushover->setDevice( $this->device );
		}
		$pushover->setPriority( $this->priority );
		$pushover->setSound( $this->sound );

		// Setup message
		$pushover->setTitle( $args['title'] );
		$pushover->setMessage( $args['message'] );
		$pushover->setUrl( $args['url'] );
		$response = '';

		$this->add_log(
			__( 'Sending: ', 'wc_pushover' ) .
							"\nTitle: " . $args['title'] .
							"\nMessage: " . $args['message'] .
							"\nURL: " . $args['url'] .
							"\nPriority: " . $this->priority .
			"\nSound: " . $this->sound
		);

		try {
			$response = $pushover->send();
			$this->add_log( __( 'Response: ', 'wc_pushover' ) . "\n" . print_r( $response, true ) );

		} catch ( Exception $e ) {
			$this->add_log( sprintf( __( 'Error: Caught exception from send method: %s', 'wc_pushover' ), $e->getMessage() ) );
		}

		$this->add_log( __( 'Pushover response', 'wc_pushover' ) . "\n" . print_r( $response, true ) );

	}

	/**
	 * generate_test_button_html()
	 *
	 * @access public
	 */
	public function generate_test_button_html() {
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
	private function add_log( $message ) {

		if ( ! $this->debug ) {
			return;
		}

		$logger = new WC_Logger();
		$logger->add('pushover-woocommerce', $message );

	}

	/**
	 * pushover_get_currency_symbol
	 *
	 * @access public
	 * @return string
	 * @since 1.0.2
	 */
	public function pushover_get_currency_symbol() {
		$currency = get_woocommerce_currency();

		switch ( $currency ) {
			case 'BRL':
				$currency_symbol = '&#82;&#36;';
				break;
			case 'AUD':
			case 'CAD':
			case 'MXN':
			case 'NZD':
			case 'HKD':
			case 'SGD':
			case 'USD':
				$currency_symbol = '$';
				break;
			case 'EUR':
				$currency_symbol = '€';
				break;
			case 'CNY':
			case 'RMB':
			case 'JPY':
				$currency_symbol = '¥‎';
				break;
			case 'RUB':
				$currency_symbol = 'руб.';
				break;
			case 'KRW':
				$currency_symbol = '₩';
				break;
			case 'TRY':
				$currency_symbol = 'TL';
				break;
			case 'NOK':
				$currency_symbol = 'kr';
				break;
			case 'ZAR':
				$currency_symbol = 'R';
				break;
			case 'CZK':
				$currency_symbol = 'Kč';
				break;
			case 'MYR':
				$currency_symbol = 'RM';
				break;
			case 'DKK':
				$currency_symbol = 'kr';
				break;
			case 'HUF':
				$currency_symbol = 'Ft';
				break;
			case 'IDR':
				$currency_symbol = 'Rp';
				break;
			case 'INR':
				$currency_symbol = '₹';
				break;
			case 'ILS':
				$currency_symbol = '₪';
				break;
			case 'PHP':
				$currency_symbol = '₱';
				break;
			case 'PLN':
				$currency_symbol = 'zł';
				break;
			case 'SEK':
				$currency_symbol = 'kr';
				break;
			case 'CHF':
				$currency_symbol = 'CHF';
				break;
			case 'TWD':
				$currency_symbol = 'NT$';
				break;
			case 'THB':
				$currency_symbol = '฿';
				break;
			case 'GBP':
				$currency_symbol = '£';
				break;
			case 'RON':
				$currency_symbol = 'lei';
				break;
			default:
				$currency_symbol = '';
				break;
		}

		return apply_filters( 'pushover_currency_symbol', $currency_symbol, $currency );
	}

} /* class WC_Pushover */

<?php
/*
Plugin Name: WooCommerce Widget
Plugin URI: https://github.com/zachhardesty7/woocommerce-widget
Description: WooCommerce Widget custom plugin & files
Author: Zach Hardesty
Author URI: http://zachhardesty.com
Version: 1.0.0

Copyright: © 2016 Zach Hardesty (email : hello@zachhardesty.com)
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
defined( 'ABSPATH' ) or exit;
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
add_action( 'plugins_loaded', 'zh_wc_widget' );

class ZH_WC_Widget {
	protected static $instance;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'catch_widget_query' ));
		add_filter( 'query_vars', array( $this, 'add_query_vars' ));
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'track_affiliate' ), 10, 2);
		add_action( 'init', array( $this, 'start_session' ), 1);
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ));
	}

	public function plugins_loaded() {
		// add affiliate column titles to the orders list table
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'render_affiliate_column' ), 15 );

		// add listable affiliate column content to the orders list table
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_affiliate_column_data' ), 5 );

		// make affiliate column sortable
		add_filter( 'manage_edit-shop_order_sortable_columns', array( $this, 'add_affiliate_sortable_columns' ) );

		// sort affiliate column
		add_action( 'pre_get_posts', array( $this, 'sort_by_affiliate'), 1 );

		// display orders by affiliate
		add_action('restrict_manage_posts', array( $this, 'display_orders_by_affiliate' ));

		// filter orders by affiliate
		add_filter('parse_query', array( $this, 'filter_orders_by_affiliate' ));

	}

	/**
	 * Display affiliate dropdown on admin orders page
	 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
	 */
	function display_orders_by_affiliate() {
		// TODO: find proper way of retrieving affiliates; likely through WPDB query

		// initialize array for affiliates visible on current page
		$visible_affiliates = array();

		// get all visible orders with an affiliate
		$orders = wc_get_orders(array(
			'limit'    => -1,
			'meta_key'     => 'affiliate'));

		// escape if no matching orders
		if(empty($orders))
			return;

		// push all order affiliates into array checking for duplicity
		foreach($orders as $order) :
			// cycle meta data to capture affiliate data
			foreach ( $order->get_data()['meta_data'] as $value ) {
				// push to array if not already in array
				if ($value->key == 'affiliate' && !in_array($value->value, $visible_affiliates)) {
					array_push($visible_affiliates, $value->value);
				}
			}
		endforeach;

		// if more affiliates now than session value, update session value
		if ( !isset($_SESSION['affiliates']) || (count($_SESSION['affiliates']) < count($visible_affiliates) ) ) {
			$_SESSION['affiliates'] = $visible_affiliates;
		}
		$affiliates = $_SESSION['affiliates'];

		// dropdown placeholder for no filter
		$options[] = sprintf('<option value="">%1$s</option>', __('All Affiliates', 'your-text-domain'));
		// create option for each affiliate, set selected if currently filtering affiliate
		foreach ($affiliates as $affiliate) {
			if ($affiliate == $_GET['affiliate']) {
				$options[] = sprintf('<option selected value="%1$s">%2$s</option>', esc_attr($affiliate), $affiliate);
			} else {
				$options[] = sprintf('<option value="%1$s">%2$s</option>', esc_attr($affiliate), $affiliate);
			}
		}

		// output dropdown filter menu
		echo '<select class="" id="affiliate" name="affiliate">';
		echo join("\n", $options);
		echo '</select>';
	}

	/**
	 * Filter posts by affiliate in admin
	 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
	 */
	function filter_orders_by_affiliate($query) {
		// if on admin order page and affiliate option received
		// set query vars to affiliate data
		$current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		if ( is_admin() &&
			'shop_order' == $current_page &&
			'edit.php' == $pagenow &&
			 isset( $_GET['affiliate'] ) &&
			 $_GET['affiliate'] != '' ) {

			$affiliate = $_GET['affiliate'];
			$query->query_vars['meta_key'] = 'affiliate';
			$query->query_vars['meta_value'] = $affiliate;
			$query->query_vars['meta_compare'] = '=';
		}
	}

	/**
	 * Add affiliate column
	 *
	 * @since 1.0
	 * @param array $columns associative array of column id to display name
	 * @return array of column id to display name
	 * @credit WC Admin Custom Order Fields Plugin
	 */
	public function render_affiliate_column( $columns ) {
			// TODO: reorder fields

			// get all columns up to but excluding the 'order_actions' column
			$new_columns = array();
			foreach ( $columns as $name => $value ) {
				if ( $name == 'order_actions' ) {
					prev( $columns );
					break;
				}
				$new_columns[ $name ] = $value;
			}
			// inject affiliate column
			$new_columns['affiliate'] = 'Affiliate';
			// add the 'order_actions' column, and any others
			foreach ( $columns as $name => $value ) {
				$new_columns[ $name ] = $value;
			}
			return $new_columns;
		}

	/**
	 * Display the value for the affiliate column
	 *
	 * @since 1.0
	 * @param string $column the column name
	 * @credit WC Admin Custom Order Fields Plugin
	 */
	public function render_affiliate_column_data( $column ) {
			global $post;
			if ($column == "affiliate") {
				// get WC order object from post ID
				$order_id = $post->ID;
				$order = $order_id ? wc_get_order( $order_id ) : null;
				// cycle meta data to capture affiliate data
				foreach ( $order->get_data()['meta_data'] as $value ) {
					if ($value->key == 'affiliate') {
						$affiliate = $value->value;
					}
				}
				if ( isset($affiliate) ) echo $affiliate;
			}
		}

	/**
	 * Make affiliate column sortable
	 *
	 * @since 1.0
	 * @param array $columns associative array of column name to id
	 * @return array of column name to id
	 * @credit WC Admin Custom Order Fields Plugin
	 */
	public function add_affiliate_sortable_columns( $columns ) {
		$columns[ 'affiliate' ] = 'affiliate';
		return $columns;
	}

	/**
		* Sort affiliate column
		*
		* @since 1.0
		* @param array $columns associative array of column id to display name
	 * @credit https://wpdreamer.com/2014/04/how-to-make-your-wordpress-admin-columns-sortable/
	 */
	function sort_by_affiliate( $query ) {
			 /**
				* We only want our code to run in the main WP query
				* AND if an orderby query variable is designated.
				*/
			 if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
					if ($orderby == 'affiliate') {
						// set our query's meta_key, which is used for custom fields
						$query->set( 'meta_key', 'affiliate' );
						/**
						* Tell the query to order by our custom field/meta_key's value
						*/
						$query->set( 'orderby', 'meta_value' );
					}
			 }
		}

	// echo get_post_meta( get_the_ID(), 'main-heading', true );

	// TODO: include a banner that is displayed on first install

	// enable php session global
	function start_session() {
    if(!session_id()) {
        session_start();
    }
	}

	function add_query_vars($aVars) {
		$aVars[] = "widget"; // represents the name of the current widget page as shown in the URL
		$aVars[] = "affiliate";
		$aVars[] = "category";
		$aVars[] = "columns";
		$aVars[] = "tag";
		$aVars[] = "product-id";
		$aVars[] = "ref-url";
		$aVars[] = "atc";
		$aVars[] = "cart-remove";
		return $aVars;
	}

	public function track_affiliate( $order_id, $posted ) {
		$order = wc_get_order( $order_id );
    $order->update_meta_data( 'affiliate', $_SESSION['affiliate'] );
    $order->save();
	}

	/**
	 * Catches our query variable. If it's there, we'll stop the
	 * rest of WordPress from loading and do our thing.
	 */
	public function catch_widget_query() {
		session_unset();
		session_destroy();
		// set persistent variable from snippet settings
		// define if undefined
		if ( !isset($_SESSION['affiliate']) || $_SESSION['affiliate'] != get_query_var('affiliate') ) {
			$_SESSION['affiliate'] = get_query_var('affiliate');
		}
		$affiliate = $_SESSION['affiliate'];
		// set if undefined or not empty string (capture changes)
		if ( !isset($_SESSION['columns']) || (get_query_var('columns') != "") ) {
			 $_SESSION['columns'] = get_query_var('columns');
		 }
		$columns = $_SESSION['columns'];

		/* If no 'widget' param found, escape to normal WP loading */
    if ( !get_query_var('widget') ) return;

    /* display widget page based on 'widget' param */
    if ( get_query_var('widget') == 'store' ) {
			include 'templates/widget-header.php';
			include 'templates/pages/store.php';
			include 'templates/widget-footer.php';
		}

		elseif ( get_query_var('widget') == 'product' ) {
			include 'templates/widget-header.php';
			include 'templates/pages/product.php';
			include 'templates/widget-footer.php';
		}

		elseif ( get_query_var('widget') == 'cart' ) {
			include 'templates/widget-header.php';
			include 'templates/pages/cart.php';
			include 'templates/widget-footer.php';
		}

		elseif ( get_query_var('widget') == 'checkout' ) {
			include 'templates/widget-header.php';
			// include 'templates/pages/checkout.php';
			?>
				<h1>YOU WILL BE REDIRECTED TO MPL WEBSITE WITHIN 5 SECONDS</h1>
				<a href="#" onclick="window.top.location.href = '<?php echo WC()->cart->get_checkout_url(); ?>'">Please click here if redirect does not happen</a>
			<?php
			include 'templates/widget-footer.php';
		}

		// if 'widget' value unmatched, display 404 page
		else include 'templates/widget-404.php';

    exit();
	}

}

/**
 * Returns the One True Instance of WC Widget.
 *
 * @return ZH_WC_Widget
 */
function zh_wc_widget() {
	return ZH_WC_Widget::instance();
}

zh_wc_widget();

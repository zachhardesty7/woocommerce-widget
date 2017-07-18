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
	}

	// echo get_post_meta( get_the_ID(), 'main-heading', true );

	// TODO: include a banner that is displayed on first install

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
		// set persistent variable from snippet settings
		// define if undefined
		if (!isset($_SESSION['affiliate'])) { $_SESSION['affiliate'] = get_query_var('affiliate'); }
		$affiliate = $_SESSION['affiliate'];
		// set if undefined or not empty string (capture changes)
		if (!isset($_SESSION['columns']) || (get_query_var('columns') != "")) { $_SESSION['columns'] = get_query_var('columns'); }
		$columns = $_SESSION['columns'];

		/* If no 'widget' parameter found, return */
    if (!get_query_var('widget')) return;

    /* 'widget' variable is set, export any content you like */
    if (get_query_var('widget') == 'store') {
			include 'templates/widget-header.php';

			$args = array(
        'status'         => array( 'draft', 'pending', 'private', 'publish' ),
        'type'           => array_merge( array_keys( wc_get_product_types() ) ),
        'category'       => $affiliate,
        'tag'            => array(),
        'limit'          => get_option( 'posts_per_page' ),
        'page'           => 1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'return'         => 'objects',
        'paginate'       => false,
    	);

			$products = wc_get_products( $args );


			 {
				?>

				<div class="cart row d-flex justify-content-end py-3">
					<a class="button" href="<?php echo WC()->cart->get_cart_url(); ?>?widget=cart">
						Cart <i class="fa fa-shopping-cart" aria-hidden="true"></i>
					</a>
				</div>

				<!-- Affiliate Courses -->
					<?php
						$i = 0;
						foreach ($products as $product) :
						if ($i == 0) {
							echo '<div class="row py-3">';
						} elseif ($i%$columns == 0) {
							echo '</div><div class="row py-3">';
						}
						?>
						<div class="col-<?php echo 12 / $columns ?> text-center">
							<a href="<?php echo $product->get_permalink(); ?>?widget=product&amp;product-id=<?php echo $product->get_id(); ?>" class="product-link">
								<p class="hvr-grow"><?php echo $product->get_image(); ?></p>
								<p class="product-title"><?php echo $product->get_title(); ?></p>
							</a>
							<p class="product-price">$<?php echo $product->get_price(); ?></p>
							<p><a href="<?php echo WC()->cart->get_cart_url(); ?>?widget=cart&amp;product-id=<?php echo $product->get_id();?>&amp;atc=true" class="button button-right" role="button">Add to Cart</a></p>
					</div>
						<?php
							$i++;
						endforeach;
						?>
				</div>
			</div>

			<div class="container-fluid">

		<?php
		$kn_products = wc_get_products( array( 'category' => "keynote", 'orderby' => 'title', 'order' => 'ASC' ) );
		?>
				<!-- Keynote Courses -->
				<h3>Keynote Courses</h3>
				<p>Choose a state and Keynote Course below to be redirected to the Keynote Series page</p>
				<!-- State dropdown selector w encoded data -->
				<form id="stateSelect" name="stateSelectDropdown">
					<p>
						<select id="stateSelectDropdown" name="state">
							<option value="">Select state:</option>
							<option value="ArkansasAssociationofRealtors">Arkansas</option>
							<option value="ConnecticutAssociationofRealtors" data-course-excl-post-id="12390">Connecticut</option>
							<option value="IllinoisRealtors" data-course-excl-post-id="12370,12390" data-course-gri-core="12391,12377,12395">Illinois</option>
							<option value="IowaRealtors">Iowa</option>
							<option value="KEYNOTESERIESOnline">Kansas</option>
							<option value="kreef" data-course-excl-post-id="12390,12391">Kentucky</option>
							<option value="mississippirealtorinstitute" data-course-excl-post-id="12390,12391">Mississippi</option>
							<option value="missouri">Missouri</option>
							<option value="Nebraska" data-course-excl-post-id="12390,12391">Nebraska</option>
							<option value="NorthCarolinaAssociationofRealtors" data-course-gri-core="12375,12391,12393,12395" data-course-gri-elective="12370,12373,12377,12397">North Carolina</option>
							<option value="SouthDakotaAssociationofRealtors">South Dakota</option>
							<option value="TexasAssociationofRealtors" data-course-excl-post-id="12391">Texas</option>
							<option value="VirginiaAssociationofREALTORS" data-course-excl-post-id="12373,12390,12391,12377,12393,12395,12397">Virginia</option>
							<option value="WashingtonREALTORS" data-course-excl-post-id="12390,12391">Washington</option>
							<option value="keynoteseriesprofessionaldevelopment">Other State</option>
						</select>
					</p>
				</form>

				<?php
					$i = 0;
					foreach ($kn_products as $kn_product) :
					if ($i == 0) {
						echo '<div class="row py-3">';
					} elseif ($i%$columns == 0) {
						echo '</div><div class="row py-3">';
					}
				?>
				<div class="col-<?php echo 12 / $columns ?> text-center">
					<a href="/#" class="kn-product-link" id="product-<?php echo $kn_product->get_id(); ?>" target="_blank">
						<p class="hvr-grow"><?php echo $kn_product->get_image(); ?></p>
						<p class="product-title"><?php echo $kn_product->get_title(); ?></p>
					</a>
			</div>
				<?php
					$i++;
					endforeach;
				?>
				</div>
				<?php


			}

			include 'templates/widget-footer.php';

		}



		elseif(get_query_var('widget') == 'product') {
			include 'templates/widget-header.php';
			$product = wc_get_product(get_query_var('product-id'));

			?>
			<div class="row d-flex justify-content-between">
				<div class="py-3">
					<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?widget=store" class="button button-left">Return to Store</a>
				</div>
				<div class="py-3">
					<a class="button" href="<?php echo WC()->cart->get_cart_url(); ?>?widget=cart">
						Cart <i class="fa fa-shopping-cart" aria-hidden="true"></i>
					</a>
				</div>
			</div>

			<div class="row" id="product-<?php echo $product->get_id(); ?>">
				<div class="float-left" style="padding-right: 15px" id="product-image-<?php echo $product->get_id(); ?>">
					<?php echo $product->get_image() ?>
				</div>
				<div class="product-summary">
					<h2 class="product-title"><?php esc_attr_e($product->get_title()); ?></h2>
					<p class="product-price">$<?php echo $product->get_price(); ?></p>
					<p class="product-short-description">
						<?php echo $product->get_short_description() ?>
					</p>
					<p><a href="<?php echo WC()->cart->get_cart_url(); ?>?widget=cart&amp;product-id=<?php echo $product->get_id();?>&amp;atc=true" class="button button-right" role="button">Add to Cart</a></p>
				</div>
			</div>
			<div class="product-description row">
				<h3>Description</h3>
				<?php echo $product->get_description(); ?>
			</div>


			<?php
			include 'templates/widget-footer.php';

		}



		elseif(get_query_var('widget') == 'cart') {
			include 'templates/widget-header.php';
			if (isset($_GET["atc"]) === true && $_GET["atc"] === "true") {
				WC()->cart->add_to_cart( get_query_var('product-id') );
			}
			if (isset($_GET["cart-remove"]) === true) {
				WC()->cart->remove_cart_item( get_query_var("cart-remove") );
			}

			// display cart
			?><h2>Cart</h2><?php
			$cart = WC()->cart->get_cart();

			if ($cart) {
			?>
				<div class="text-left">
					<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?widget=store" class="button button-left">Continue Shopping</a>
				</div>
				<table class="table">
			    <thead>
			      <tr>
			        <th></th>
			        <th></th>
			        <th>Product</th>
							<th>Price</th>
							<th>Quantity</th>
							<th>Total</th>
			      </tr>
			    </thead>
			    <tbody>
				<?php
					foreach ($cart as $cart_item_key => $cart_item) {
						$product = $cart_item['data'];
						$product_remove_url = WC()->cart->get_remove_url($cart_item_key);
						$product_url = $product->get_permalink();
						$product_image = $product->get_image(array(32, 32), $attr = array(), $placeholder = true);
						$product_title = $product->get_title();
						$product_price = '$' . $product->get_price($context = 'view');
						$product_quantity = $cart[$cart_item_key]['quantity'];
						$product_total = '$' . $cart[$cart_item_key]['line_total'];
						$product_id = $product->get_id();

						// TODO: ajax php call to remove item from cart, currently refreshes
						?>

						<tr id="<?php echo $cart_item_key ?>">
							<td><a class="cart-remove" href="<?php echo WC()->cart->get_cart_url(); ?>?widget=cart&amp;cart-remove=<?php echo $cart_item_key?>"><i class="fa fa-times cart-remove-icon" aria-hidden="true"></i></a></td>
							<td><a href="<?php echo $product_url ?>?widget=product&amp;product-id=<?php echo $product_id ?>"><?php echo $product_image ?></a></td>
							<td><a href="<?php echo $product_url ?>?widget=product&amp;product-id=<?php echo $product_id ?>"><?php echo $product_title ?></a></td>
							<td><?php echo $product_price; ?></td>
							<td><?php echo $product_quantity;; ?></td>
							<td><?php echo $product_total; ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
				</table>
			<?php

			// Added onclick redirect for parent website for now
			// TODO: time delayed click w loading symbol
			?>
			<div class="row d-flex justify-content-end">
				<div class="flex-column">
					<h3>Cart Totals</h3>
					<p>Subtotal: <?php echo WC()->cart->get_cart_subtotal(); ?><br>
					   Total: <?php echo WC()->cart->get_cart_total(); ?></p>

					<div class="checkout">
						<a class="button button-right" onclick="window.top.location.href = '<?php echo WC()->cart->get_checkout_url(); ?>'" href="<?php echo WC()->cart->get_checkout_url(); ?>?widget=checkout">Checkout</a>
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<p>Your cart is currently empty.</p>
			<div>
				<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?widget=store" class="button button-left">Return to Store</a>
			</div>
			<?php

		}
			include 'templates/widget-footer.php';
		}



		elseif(get_query_var('widget') == 'checkout') {
			include 'templates/widget-header.php';
			?>
			<h1>YOU WILL BE REDIRECTED TO MPL WEBSITE WITHIN 5 SECONDS</h1>
			<a href="#" onclick="window.top.location.href = '<?php echo WC()->cart->get_checkout_url(); ?>'">Please click here if redirect does not happen</a>
			<?php

			// below not executed due to redirect
			$cart = WC()->cart->get_cart();
			echo '<h2>Your Order</h2>';
			foreach ($cart as $cart_item_key => $cart_item) {
				$product = $cart_item['data'];
				$product_title = $product->get_title();
				$product_quantity = $cart[$cart_item_key]['quantity'];
				$product_total = '$' . $cart[$cart_item_key]['line_total'];

				?>

				<?php
				echo $product_title;
				echo $product_quantity;
				echo $product_total;
			}
			?>

			<p>Subtotal: <?php echo WC()->cart->get_cart_subtotal(); ?></p>
			<p>Total: <?php echo WC()->cart->get_cart_total(); ?></p>
			<?php
			include 'templates/widget-footer.php';
		}

			// TODO: non working stuff for checkout page

			// $order = new WC_Order;
			// d($order);


			// include woocommerce-braintree-payment-gateway.php;
			// run_WC_braintree_payment_gateway();



			// if (isset($_POST['isOrder']) && $_POST['isOrder'] == 1) {
			//     $address = array(
			//         'first_name' => $_POST['notes']['domain'],
			//         'last_name'  => '',
			//         'company'    => $_POST['customer']['company'],
			//         'email'      => $_POST['customer']['email'],
			//         'phone'      => $_POST['customer']['phone'],
			//         'address_1'  => $_POST['customer']['address'],
			//         'address_2'  => '',
			//         'city'       => $_POST['customer']['city'],
			//         'state'      => '',
			//         'postcode'   => $_POST['customer']['postalcode'],
			//         'country'    => 'NL'
			//     );
			//
			//     $order = wc_create_order();
			//     foreach ($_POST['product_order'] as $productId => $productOrdered) :
			//         $order->add_product( get_product( $productId ), 1 );
			//     endforeach;
			//
			//     $order->set_address( $address, 'billing' );
			//     $order->set_address( $address, 'shipping' );
			//
			//     $order->calculate_totals();
			//
			//     update_post_meta( $order->id, '_payment_method', 'ideal' );
			//     update_post_meta( $order->id, '_payment_method_title', 'iDeal' );
			//
			//     // Store Order ID in session so it can be re-used after payment failure
			//     WC()->session->order_awaiting_payment = $order->id;
			//
			//     // Process Payment
			//     $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
			//     $result = $available_gateways[ 'ideal' ]->process_payment( $order->id );
			//
			//     // Redirect to success/confirmation/payment page
			//     if ( $result['result'] == 'success' ) {
			//
			//         $result = apply_filters( 'woocommerce_payment_successful_result', $result, $order->id );
			//
			//         wp_redirect( $result['redirect'] );
			//         exit;
			//     }
			// }

			// d(wc_braintree());
			//
			// $gateway = wc_braintree();
			// d($gateway->get_gateway_ids());
			// d($gateway->get_gateway($gateway_id = null));
			// $order_id = absint( $_GET['order'] );
			// $order    = wc_get_order( $order_id );

			// d(WC_Shortcode_Checkout::get());
			//
			// d($order->get_formatted_order_total());
			// d($order->get_address($type = 'billing'));
			// d($order->get_payment_method($context = 'view'));
			// d($order->get_payment_method_title($context = 'view'));
			// d($order->get_items($types = 'line_item'));
			// d($order_id);
			// d($order);

			// $checkout_url = WC()->cart->get_checkout_url();
			// $payment_page = WC_Order::get_checkout_payment_url();
			// $thankyou_page = WC_Order::get_checkout_order_received_url( );

			// d($payment_page);
			// d($thankyou_page);

			// make ssl if needed
			// if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'yes' ) {
			// 	$payment_page = str_replace( 'http:', 'https:', $payment_page );
			// }
			// echo do_shortcode('[woocommerce_checkout]');


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

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
define( 'ZH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
add_action( 'plugins_loaded', 'zh_wc_widget' );

class ZH_WC_Widget {
	protected static $instance;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected $affiliate = 'test-category';

	public function __construct() {
		add_action( 'template_redirect', array($this, 'catch_widget_query' ));
		add_filter( 'query_vars', array($this, 'add_query_vars' ));
	}

	// TODO: include a banner that is displayed on first install

	function add_query_vars($aVars) {
		$aVars[] = "zh-widget"; // represents the name of the widget page as shown in the URL
		$aVars[] = "zh-affiliate";
		$aVars[] = "zh-category";
		$aVars[] = "zh-tag";
		$aVars[] = "zh-product-id";
		$aVars[] = "zh-ref-url";
		$aVars[] = "zh-atc";
		$aVars[] = "zh-cart-remove";
		return $aVars;
	}

	public function cartBackToAffiliateStorePage() {
		?>
			<div class="text-left">
				<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?zh-widget=store&amp;zh-affiliate=<?php echo $this->affiliate ?>" class="zh-button zh-button-left">Continue Shopping</a>
			</div>
		<?php
	}

	/**
	 * Catches our query variable. If it's there, we'll stop the
	 * rest of WordPress from loading and do our thing, whatever
	 * that may be.
	 */
	public function catch_widget_query()
	{

		global $woocommerce, $woocommerce_loop, $product, $post;


		/* If no 'zh-widget' parameter found, return */
    if(!get_query_var('zh-widget')) return;


    /* 'zh-widget' variable is set, export any content you like */
    if(get_query_var('zh-widget') == 'store') {
			include 'templates/widget-header.php';

			$args = array(
        'status'         => array( 'draft', 'pending', 'private', 'publish' ),
        'type'           => array_merge( array_keys( wc_get_product_types() ) ),
        'parent'         => null,
        'sku'            => '',
        'category'       => $this->affiliate,
        'tag'            => array(),
        'limit'          => get_option( 'posts_per_page' ),
        'offset'         => null,
        'page'           => 1,
        'include'        => array(),
        'exclude'        => array(),
        'orderby'        => 'title',
        'order'          => 'ASC',
        'return'         => 'objects',
        'paginate'       => false,
        'shipping_class' => array(),
    	);

			$zh_products = wc_get_products( $args );
			$i = 0;

			 {

				// REVIEW: cannot be implemented wo stealing phpsessid from keynoteseries
				?>
				<a href="#" id="kns-post" class="zh-button">Initiate POST</a>





				<div class="cart row d-flex justify-content-end py-3">
					<a class="zh-button" href="<?php echo WC()->cart->get_cart_url(); ?>?zh-widget=cart">
						Cart <i class="fa fa-shopping-cart" aria-hidden="true"></i>
					</a>
				</div>

				<div class="row">

						<?php foreach ($zh_products as $zh_product) : ?>
						<div class="col-3 text-center">
							<a href="<?php echo $zh_product->get_permalink(); ?>?zh-widget=product&amp;zh-product-id=<?php echo $zh_product->get_id(); ?>" class="product-link">
								<p><?php echo $zh_product->get_image(); ?></p>
								<h5 class="product-title"><?php echo $zh_product->get_title(); ?></h5>
							</a>
								<p class="product-price">$<?php echo $zh_product->get_price(); ?></p>
							<p><a href="<?php echo WC()->cart->get_cart_url(); ?>?zh-widget=cart&amp;zh-product-id=<?php echo $zh_product->get_id();?>&amp;zh-atc=true" class="zh-button zh-button-right" role="button">Add to Cart</a></p>
					</div>
						<?php
							$i++;
    					if ($i%4 == 0) echo '</div><div class="row">';
						endforeach;
						?>

				</div>

				<?php


			}

			include 'templates/widget-footer.php';

		}



		elseif(get_query_var('zh-widget') == 'product') {
			include 'templates/widget-header.php';
			$zh_product = wc_get_product(get_query_var('zh-product-id'));

			?>
			<div class="row d-flex justify-content-between">
				<div class="py-3">
					<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?zh-widget=store&amp;zh-affiliate=<?php echo $this->affiliate ?>" class="zh-button zh-button-left">Return to Store</a>
				</div>
				<div class="py-3">
					<a class="zh-button" href="<?php echo WC()->cart->get_cart_url(); ?>?zh-widget=cart">
						Cart <i class="fa fa-shopping-cart" aria-hidden="true"></i>
					</a>
				</div>
			</div>

			<div class="row" id="product-<?php echo $zh_product->get_id(); ?>">
				<div class="float-left" style="padding-right: 15px" id="product-image-<?php echo $zh_product->get_id(); ?>">
					<?php echo $zh_product->get_image() ?>
				</div>
				<div class="product-summary">
					<h2 class="product-title"><?php esc_attr_e($zh_product->get_title()); ?></h2>
					<p class="product-price">$<?php echo $zh_product->get_price(); ?></p>
					<p class="product-short-description">
						<?php echo $zh_product->get_short_description() ?>
					</p>
					<p><a href="<?php echo WC()->cart->get_cart_url(); ?>?zh-widget=cart&amp;zh-product-id=<?php echo $zh_product->get_id();?>&amp;zh-atc=true" class="zh-button zh-button-right" role="button">Add to Cart</a></p>
				</div>
			</div>
			<div class="product-description row">
				<h3>Description</h3>
				<?php echo $zh_product->get_description(); ?>
			</div>


			<?php
			include 'templates/widget-footer.php';

		}



		elseif(get_query_var('zh-widget') == 'cart') {
			include 'templates/widget-header.php';
			if (isset($_GET["zh-atc"]) === true && $_GET["zh-atc"] === "true") {
				WC()->cart->add_to_cart( get_query_var('zh-product-id') );
			}
			if (isset($_GET["zh-cart-remove"]) === true) {
				WC()->cart->remove_cart_item( get_query_var("zh-cart-remove") );
			}

			// display cart
			?><h2>Cart</h2><?php
			$cart = WC()->cart->get_cart();

			if ($cart) {
			echo self::cartBackToAffiliateStorePage();
?>
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
					$zh_product = $cart_item['data'];
					$zh_product_remove_url = WC()->cart->get_remove_url($cart_item_key);
					$zh_product_url = $zh_product->get_permalink();
					$zh_product_image = $zh_product->get_image(array(32, 32), $attr = array(), $placeholder = true);
					$zh_product_title = $zh_product->get_title();
					$zh_product_price = '$' . $zh_product->get_price($context = 'view');
					$zh_product_quantity = $cart[$cart_item_key]['quantity'];
					$zh_product_total = '$' . $cart[$cart_item_key]['line_total'];
					$zh_product_id = $zh_product->get_id();

					// TODO: ajax php call to remove item from cart, currently refreshes
					?>

					<tr id="<?php echo $cart_item_key ?>">
						<td><a class="zh-cart-remove" href="<?php echo WC()->cart->get_cart_url(); ?>?zh-widget=cart&amp;zh-cart-remove=<?php echo $cart_item_key?>"><i class="fa fa-times zh-cart-remove-icon" aria-hidden="true"></i></a></td>
						<td><a href="<?php echo $zh_product_url ?>?zh-widget=product&amp;zh-product-id=<?php echo $zh_product_id ?>"><?php echo $zh_product_image ?></a></td>
						<td><a href="<?php echo $zh_product_url ?>?zh-widget=product&amp;zh-product-id=<?php echo $zh_product_id ?>"><?php echo $zh_product_title ?></a></td>
						<td><?php echo $zh_product_price; ?></td>
						<td><?php echo $zh_product_quantity;; ?></td>
						<td><?php echo $zh_product_total; ?></td>
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
						<a class="zh-button zh-button-right" onclick="window.top.location.href = '<?php echo WC()->cart->get_checkout_url(); ?>'" href="<?php echo WC()->cart->get_checkout_url(); ?>?zh-widget=checkout">Checkout</a>
					</div>
				</div>
			</div>
			<?php
		} else {
			?>
			<p>Your cart is currently empty.</p>
			<div>
				<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?zh-widget=store&amp;zh-affiliate=<?php echo $this->affiliate ?>" class="zh-button zh-button-left">Return to Store</a>
			</div>
			<?php

		}
			include 'templates/widget-footer.php';
		}



		elseif(get_query_var('zh-widget') == 'checkout') {
			include 'templates/widget-header.php';
			?>
			<h1>YOU WILL BE REDIRECTED TO MPL WEBSITE WITHIN 5 SECONDS</h1>
			<a href="#" onclick="window.top.location.href = '<?php echo WC()->cart->get_checkout_url(); ?>'">Please click here if redirect does not happen</a>
			<?php

			// below not executed due to redirect
			$cart = WC()->cart->get_cart();
			echo '<h2>Your Order</h2>';
			foreach ($cart as $cart_item_key => $cart_item) {
				$zh_product = $cart_item['data'];
				$zh_product_title = $zh_product->get_title();
				$zh_product_quantity = $cart[$cart_item_key]['quantity'];
				$zh_product_total = '$' . $cart[$cart_item_key]['line_total'];

				?>

				<?php
				echo $zh_product_title;
				echo $zh_product_quantity;
				echo $zh_product_total;
			}
			?>

			<p>Subtotal: <?php echo WC()->cart->get_cart_subtotal(); ?></p>
			<p>Total: <?php echo WC()->cart->get_cart_total(); ?></p>
			<?php
			include 'templates/widget-footer.php';
		}

			// TODO: non working stuff for iframed checkout page

			// $zh_order = new WC_Order;
			// d($zh_order);


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
			// $zh_gateway = wc_braintree();
			// d($zh_gateway->get_gateway_ids());
			// d($zh_gateway->get_gateway($gateway_id = null));
			// $zh_order_id = absint( $_GET['order'] );
			// $zh_order    = wc_get_order( $order_id );

			// d(WC_Shortcode_Checkout::get());
			//
			// d($zh_order->get_formatted_order_total());
			// d($zh_order->get_address($type = 'billing'));
			// d($zh_order->get_payment_method($context = 'view'));
			// d($zh_order->get_payment_method_title($context = 'view'));
			// d($zh_order->get_items($types = 'line_item'));
			// d($zh_order_id);
			// d($zh_order);

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

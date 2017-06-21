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

require 'kint.php';

defined( 'ABSPATH' ) or exit;
define( 'ZH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ZH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
add_action( 'plugins_loaded', 'zh_wc_widget' );

// function accessProtected($obj, $propmeth, $type = 'property') {
// 	if ($type == 'method') {
// 		$r = new ReflectionMethod($obj, $propmeth);
// 		$r->setAccessible(true);
// 		return $r->invoke(new $obj());
// 	} else {
// 		$r = new ReflectionClass($obj);
// 		$property = $r->getProperty($propmeth);
// 		$property->setAccessible(true);
// 		return $property->getValue($obj);
// 	}
// }

class ZH_WC_Widget {
	protected static $instance;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public $template_url;

	protected $vendor = 'test-category';

	public function __construct() {
		// add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
		// add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );
		add_action('template_redirect', array($this, 'catch_widget_query'));
		// add_action('init', array($this, 'widget_add_vars'));
		add_filter('query_vars', array($this, 'add_query_vars'));

		// TODO: include a banner that is displayed on first install
	}

	/**
	 * Take care of anything that needs all plugins to be loaded
	 */
	public function plugins_loaded() {
	}

	public function zh_build_store() {
	}

	function add_query_vars($aVars) {
		$aVars[] = "zh-domain";
		$aVars[] = "zh-embed"; // represents the name of the product category as shown in the URL
		$aVars[] = "zh-id";
		$aVars[] = "zh-atc";
		$aVars[] = "zh-vendor";
		return $aVars;
	}

	// TODO: abstract WC page exports
	private function export_shop()
	{
	}

	public static function cartBackToVendorStorePage() {
		$url = parse_url(wp_get_referer());
		parse_str($url['query'], $path);
		$page = $path['zh-embed'];

		?>
			<div style="padding: 0 0 1.5em 0;">
				<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?zh-embed=<?php echo $page; if (isset($path['zh-id'])) { echo "&zh-id=" . $path['zh-id']; } ?>" class="zh-button-left" style="font-size:18px !important;">Continue Shopping</a>
			</div>
		<?php
	}

	public function productBackToVendorStorePage() {
		?>
			<div style="padding: 0 0 1.5em 0;">
				<a href="<?php echo get_permalink( wc_get_page_id('shop')) ?>?zh-embed=shop&amp;zh-vendor=<?php echo $this->vendor ?>" class="zh-button-left" style="font-size:18px !important;">Return to shop</a>
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

		/* If no 'embed' parameter found, return */
    if(!get_query_var('zh-embed')) return;

    /* 'embed' variable is set, export any content you like */
    if(get_query_var('zh-embed') == 'shop') {

			$args = array(
        'status'         => array( 'draft', 'pending', 'private', 'publish' ),
        'type'           => array_merge( array_keys( wc_get_product_types() ) ),
        'parent'         => null,
        'sku'            => '',
        'category'       => $this->vendor,
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

			 {

				?>

				<div class="cart">
					<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>?zh-embed=cart">View Cart</a>
				</div>

				<div class="woocommerce columns-4">
					<ul class="products">

						<?php foreach ($zh_products as $zh_product) : ?>
						<li class="product">
							<a href="<?php echo $zh_product->get_permalink(); ?>?zh-embed=product&amp;zh-id=<?php echo $zh_product->get_id(); ?>" class="product-link">
								<span class="product-image-wrapper">
									<?php echo $zh_product->get_image(); ?>
									<span class="et_overlay"></span>
								</span>
								<h2 class="product-title"><?php echo $zh_product->get_title(); ?></h2>
								<span class="product-price">
									<span class="product-amount">
										<span class="product-currency-symbol">$</span><?php echo $zh_product->get_price(); ?></span></span>
							</a>
							<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>?zh-embed=cart&amp;zh-id=<?php echo $zh_product->get_id();?>&amp;zh-atc=true" class="product-atc-button">Add to Cart</a>
						</li>
						<?php endforeach ?>

					</ul>
				</div>

				<?php
				d($zh_product);


			}


		}


		if(get_query_var('zh-embed') == 'product') {
			$zh_product = wc_get_product(get_query_var('zh-id'));

?>
			<div class="woocommerce-page">
				<?php self::productBackToVendorStorePage(); ?>
				<div id="product-<?php echo $zh_product->get_id(); ?>">
					<div id="product-image-<?php echo $zh_product->get_id(); ?>">
						<?php echo $zh_product->get_image($size = 'shop_thumbnail', $attr = array(), $placeholder = true) ?>
					</div>
					<div class="product-summary">
						<h1 class="product-title"><?php esc_attr_e($zh_product->get_title()); ?></h1>
						<p class="product-price">
							<span class="product-price-amount"><span class="product-price-symbol">$</span><?php echo $zh_product->get_price(); ?></span>
						</p>
						<div class="product-short-description"><?php esc_attr_e($zh_product->get_short_description($context = 'view')) ?></div>
						<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>?zh-embed=cart&amp;zh-id=<?php echo $zh_product->get_id();?>&amp;zh-atc=true" class="product-atc-button">Add to Cart</a>


					</div>
					<div class="product-description">
						<h2>Description</h2>
						<?php echo $zh_product->get_description(); ?>
					</div>
				</div>

			</div>
<?php


			d($zh_product);
		}

		if(get_query_var('zh-embed') == 'cart') {
			if (isset($_GET["zh-atc"]) === true && $_GET["zh-atc"] === "true") {
				$woocommerce->cart->add_to_cart( get_query_var('zh-id'), $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data = array() );
			}
			echo self::cartBackToVendorStorePage();


			d($woocommerce->cart->get_cart());
			$cart_url = $woocommerce->cart->get_cart_url();
			$checkout_url = $woocommerce->cart->get_checkout_url();
			?>
			<div class="cart">
				<a href="<?php echo $woocommerce->cart->get_checkout_url(); ?>?zh-embed=checkout">Checkout</a>
			</div>
			<?php
		}

		if(get_query_var('zh-embed') == 'checkout') {
			// $checkout_url = $woocommerce->cart->get_checkout_url();
			// $payment_page = $woocommerce->order->get_checkout_payment_url();

			// make ssl if needed
			if ( get_option( 'woocommerce_force_ssl_checkout' ) == 'yes' ) $payment_page = str_replace( 'http:', 'https:', $payment_page );

			echo 'checkout page placeholder';
		}


    exit();
	}

	// TODO: use template files for above
	// function get_plugin_template($template, $params = array())
  //   {
  //       wc_get_template($template, $params, $this->template_url, ZH_PLUGIN_PATH . '/templates/');
  //   }
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

?>

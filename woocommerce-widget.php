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

// require 'kint.php';

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
		$aVars[] = "zh_domain";
		$aVars[] = "zh_embed"; // represents the name of the product category as shown in the URL
		return $aVars;
	}

	// TODO: abstract WC page exports
	private function export_shop()
	{
	}

	/**
	 * Catches our query variable. If it's there, we'll stop the
	 * rest of WordPress from loading and do our thing, whatever
	 * that may be.
	 */
	public function catch_widget_query()
	{
		/* If no 'embed' parameter found, return */
    if(!get_query_var('zh_embed')) return;

    /* 'embed' variable is set, export any content you like */
    if(get_query_var('zh_embed') == 'shop')
    {

			$product_category = 'mpl';

			global $woocommerce, $woocommerce_loop, $product, $post;

			$args = array(
        'status'         => array( 'draft', 'pending', 'private', 'publish' ),
        'type'           => array_merge( array_keys( wc_get_product_types() ) ),
        'parent'         => null,
        'sku'            => '',
        'category'       => $product_category,
        'tag'            => array(),
        'limit'          => get_option( 'posts_per_page' ),
        'offset'         => null,
        'page'           => 1,
        'include'        => array(),
        'exclude'        => array(),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'return'         => 'objects',
        'paginate'       => false,
        'shipping_class' => array(),
    	);

			$zh_products = wc_get_products( $args );

			foreach ($zh_products as $zh_product) {

				?>

				<div class="woocommerce columns-4">
					<ul class="products">
						<li class="product">
							<a href="<?php echo $zh_product->get_permalink(); ?>?zh_embed=product" class="product-link">
								<span class="product-image-wrapper">
									<?php echo $zh_product->get_image(); ?>
									<span class="et_overlay"></span>
								</span>
								<h2 class="product-title"><?php echo $zh_product->get_title(); ?></h2>
								<span class="product-price">
									<span class="product-amount">
										<span class="product-currency-symbol">$</span><?php echo $zh_product->get_price() ?></span></span>
							</a>
							<a rel="nofollow" href="<?php $woocommerce->cart->get_cart_url(); ?>?zh_embed=cart&add-to-cart=<?php echo $zh_product->get_id(); ?>" data-quantity="1" class="">Add to cart</a>
						</li>
					</ul>
				</div>


				<?php



			}


		}


		if(get_query_var('zh_embed') == 'product') {echo 'product page placeholder';}

		if(get_query_var('zh_embed') == 'cart') {echo 'cart page placeholder';}

		if(get_query_var('zh_embed') == 'checkout') {echo 'checkout page placeholder';}


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

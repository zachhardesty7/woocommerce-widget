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
add_action( 'plugins_loaded', 'wc_widget' );

function accessProtected($obj, $propmeth, $type = 'property') {
	if ($type == 'method') {
		$r = new ReflectionMethod($obj, $propmeth);
		$r->setAccessible(true);
		return $r->invoke(new $obj());
	} else {
		$r = new ReflectionClass($obj);
		$property = $r->getProperty($propmeth);
		$property->setAccessible(true);
		return $property->getValue($obj);
	}
}

class WC_Widget {
	protected static $instance;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		// called only after woocommerce has finished loading
		add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );

		// called after all plugins have loaded
		add_action( 'plugins_loaded', array( &$this, 'plugins_loaded' ) );

		// TODO: include a banner that is displayed on first install
	}

	/**
	 * Take care of anything that needs all plugins to be loaded
	 */
	public function plugins_loaded() {
	}



/**
 * Returns the One True Instance of WC Widget.
 *
 * @return WC_Widget
 */
function wc_widget() {
	return WC_Widget::instance();
}

wc_widget();

?>

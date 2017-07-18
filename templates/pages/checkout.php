<?php

// TODO: Build iframe compatible WC checkout system

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

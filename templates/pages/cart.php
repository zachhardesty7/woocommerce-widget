<?php
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
	<div class="text-left pb-3">
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

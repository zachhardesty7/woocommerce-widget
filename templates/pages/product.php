<?php
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

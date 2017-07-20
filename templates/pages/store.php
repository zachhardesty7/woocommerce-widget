<?php

// exclude keynote courses from main store
$excluded_category = wc_get_products( array( 'category' => 'keynote' ) );
$excluded_products = array();

foreach ($excluded_category as $product) {
	array_push($excluded_products, $product->get_id());
}

$args = array(
	'type'     => array_merge( array_keys( wc_get_product_types() ) ),
	'category' => $category,
	'tag'      => array(),
	'limit'    => get_option( 'posts_per_page' ),
	'page'     => 1,
	'orderby'  => 'title',
	'order'    => 'ASC',
	'paginate' => false,
	'exclude'  => $excluded_products
);

$products = wc_get_products( $args );

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

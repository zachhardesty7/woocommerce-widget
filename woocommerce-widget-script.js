/* === WooCommerce Widget ===
 * Contributors: Zach Hardesty
 * Tags: woocommerce
 * Stable tag: 1.0.0
 *
 * == Description ==
 *
 * This script is linked in the widget and handles calling the PHP plugin to generate the data.

 * == Installation ==

 * 1. Upload the woocommerce-wdiget-script.js to your site's root folder. (Usually home/[username]/public_html or home/[username]/www)
 */

// TODO: implement ajax
// $('.zh-cart-remove').click(function() {
//
// 	$.ajax({
// 		 url: '/wp-content/plugins/woocommerce-widget/ajax-handler.php',
// 		 type: 'post',
// 		 data: { action : 'remove_cart_item', id : this.parentElement.parentElement.id },
// 		 success: function(data, status) {
// 			 $( "#" + data ).remove();
// 		 },
// 		 error: function(xhr, desc, err) {
// 			 console.log(xhr);
// 			 console.log("Details: " + desc + "\nError:" + err);
// 		 }
// 	 });
// })

// REVIEW: cannot be implemented wo stealing phpsessid from keynoteseries
$('#kns-post').click(function() {
	var jsId = document.cookie.match(/JSESSIONID=[^;]+/);
	if(jsId != null) {
		if (jsId instanceof Array)
		jsId = jsId[0].substring(11);
		else
		jsId = jsId.substring(11);
	}
	console.log(jsId);
	$.ajax({
		 url: '/wp-content/plugins/woocommerce-widget/ajax-handler.php',
		 type: 'post',
		 data: { action : 'knspost', cart_add : 1 },
		 success: function(data, status) {
			 console.log(data);
		 },
		 error: function(xhr, desc, err) {
			 console.log(xhr);
			 console.log("Details: " + desc + "\nError:" + err);
		 }
	 });
})

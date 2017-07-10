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

(function() {

	// Localize jQuery variable
	var jQuery;

	/* Load jQuery if not present */
	if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.7.2') {
		var script_tag = document.createElement('script');
		script_tag.setAttribute("type", "text/javascript");
		script_tag.setAttribute("src",
			"http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
		if (script_tag.readyState) {
			script_tag.onreadystatechange = function() { // For old versions of IE
				if (this.readyState == 'complete' || this.readyState == 'loaded') {
					scriptLoadHandler();
				}
			};
		} else {
			script_tag.onload = scriptLoadHandler;
		}

		(document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
	} else {
		// The jQuery version on the window is the one we want to use
		jQuery = window.jQuery;
		main();
	}


	/* Called once jQuery has loaded */
	function scriptLoadHandler() {
		jQuery = window.jQuery.noConflict(true);
		main();
	}

	/* Our Start function */
	function main() {
		jQuery(document).ready(function($) {
			var zhWidgetInput = document.getElementById("zh-wc-widget");
			var zhWidget = zhWidgetInput.getAttribute("data-zh-widget");
			// zhAffiliate also refers to category until properly implemented
			var zhAffiliate = zhWidgetInput.getAttribute("data-zh-affiliate");
			var zhCategory = zhWidgetInput.getAttribute("data-zh-category");
			var zhTag = zhWidgetInput.getAttribute("data-zh-tag");
			var zhProductId = zhWidgetInput.getAttribute("data-zh-product-id");
			var zhColumns = 4;

			var zhRefUrl = encodeURIComponent(window.document.location);
			var zhHeight = zhWidgetInput.getAttribute("data-zh-height");
			var zhWidth = zhWidgetInput.getAttribute("data-zh-width");

			var iframeContent = '<iframe style="border: none" height="' + zhHeight + '" width="' + zhWidth + '"src="http://dev.markporterlive.com/?zh-widget=' + zhWidget;
			if (zhAffiliate) iframeContent += "&zh-affiliate=" + zhAffiliate;
			if (zhCategory) iframeContent += "&zh-category=" + zhCategory;
			if (zhTag) iframeContent += "&zh-tag=" + zhTag;
			if (zhProductId) iframeContent += "&zh-product-id=" + zhProductId;
			if (zhRefUrl) iframeContent += "&zh-ref-url=" + zhRefUrl;
			console.log(iframeContent);

			iframeContent += '&timestamp=' + Date.now() + '"></iframe>';

			$("#zh-wc-widget").after(iframeContent);

		});
	}

})();

/**
 * Shortcode to place on website:
 * <a class="twitter-timeline" data-width="1000" data-height="1000" data-theme="dark" data-link-color="#E81C4F" href="https://twitter.com/TwitterDev">Tweets by TwitterDev</a>
 * <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
 ***/

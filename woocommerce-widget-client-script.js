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
			// capture snippet parameters
			let widgetInput = document.getElementById("wc-widget");
			let widget = widgetInput.getAttribute("data-widget");
			// Affiliate refers to category until properly implemented
			let affiliate = widgetInput.getAttribute("data-affiliate");
			let category = widgetInput.getAttribute("data-category");
			let tag = widgetInput.getAttribute("data-tag");
			let productId = widgetInput.getAttribute("data-product-id");
			let columns = widgetInput.getAttribute("data-columns");
			let ref = encodeURIComponent(window.document.location);
			let dynamic = widgetInput.getAttribute("data-dynamic");
			let height = widgetInput.getAttribute("data-height");
			let width = widgetInput.getAttribute("data-width");

			let iframeContent = '<iframe id="wc-widget-widget"';
			if (dynamic) iframeContent += 'style="border: none; width: 100%" scrolling="no"';
			else iframeContent += 'style="border: none" ';
			if (height) iframeContent += 'height="' + height + '" ';
			if (width) iframeContent += 'width="' + width + '" ';
			iframeContent += 'src="https://dev.markporterlive.com/?widget=' + widget;
			if (affiliate) iframeContent += "&affiliate=" + affiliate;
			if (category) iframeContent += "&category=" + category;
			if (tag) iframeContent += "&tag=" + tag;
			if (productId) iframeContent += "&product-id=" + productId;
			if (columns == "dynamic") {
				let width = $("#wc-widget").width();
				if (width >= 1365 - 16) {
					columns = 6;
				} else if (width >= 925 - 16) {
					columns = 4;
				} else if (width >= 705 - 16) {
					columns = 3;
				} else if (width >= 485 - 16) {
					columns = 2;
				} else if (width >= 200 - 16) {
					columns = 1;
				}
				iframeContent += "&columns=" + columns;
			} else if (columns) {
				iframeContent += "&columns=" + columns;
			}
			if (ref) iframeContent += "&ref=" + ref;

			iframeContent += '"></iframe>';

			// @credit https://github.com/davidjbradshaw/iframe-resizer
			if (dynamic) {
				let script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src = 'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.5.14/iframeResizer.min.js';
				$("body")[0].appendChild( script );
			}

			$("#wc-widget").after(iframeContent);

			// @credit https://github.com/davidjbradshaw/iframe-resizer
			if (dynamic) {
				$('#wc-widget-widget').on('load', function(){
					iFrameResize({heightCalculationMethod: 'lowestElement'}, '#wc-widget-widget');
				});
			}


		});
	}

})();

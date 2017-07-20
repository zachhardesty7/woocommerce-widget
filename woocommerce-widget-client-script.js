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
			let widget      = widgetInput.getAttribute("data-widget");
			let affiliate   = widgetInput.getAttribute("data-affiliate");
			let category    = widgetInput.getAttribute("data-category");
			let tag         = widgetInput.getAttribute("data-tag");
			let productId   = widgetInput.getAttribute("data-product-id");
			let columns     = widgetInput.getAttribute("data-columns");
			let size        = widgetInput.getAttribute("data-size");
			let ref         = encodeURIComponent(window.document.location);

			// begin iframe creation
			let iframeContent = '<iframe id="wc-widget-widget"';
			// set param for dynamic widget
			if (size == 'dynamic') {
				iframeContent += 'style="border: none; width: 100%" scrolling="no"';
			}
			// or set width and height
			else if (size) {
				dimensions = size.split(',');
				iframeContent += 'width="' + dimensions[0] + '" ';
				iframeContent += 'height="' + dimensions[1] + '" ';
				iframeContent += 'style="border: none" ';
			}
			else {
				iframeContent += 'style="border: none" ';
			}
			// begin iframe link generation
			iframeContent += 'src="https://dev.markporterlive.com/?widget=' + widget;
			if (affiliate) iframeContent += "&affiliate=" + affiliate;
			if (category) iframeContent  += "&category=" + category;
			if (tag) iframeContent       += "&tag=" + tag;
			if (productId) iframeContent += "&product-id=" + productId;
			// set columns based on width of parent div
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
			// or simply echo snippet params
			} else if (columns) {
				iframeContent += "&columns=" + columns;
			}
			// referring URL for future data analysis
			if (ref) iframeContent += "&ref=" + ref;

			// finish iframe creation
			iframeContent += '"></iframe>';

			// append dynamic iframe resizer script to body
			// @credit https://github.com/davidjbradshaw/iframe-resizer
			if (size == 'dynamic') {
				let script = document.createElement( 'script' );
				script.type = 'text/javascript';
				script.src  = 'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/3.5.14/iframeResizer.min.js';
				$("body")[0].appendChild( script );
			}

			// append iframe after snippet
			$("#wc-widget").after(iframeContent);

			// run dynamic iframe resizer once widget has loaded
			// @credit https://github.com/davidjbradshaw/iframe-resizer
			if (size == 'dynamic') {
				$('#wc-widget-widget').on('load', function(){
					iFrameResize({heightCalculationMethod: 'lowestElement'}, '#wc-widget-widget');
				});
			}

		});
	}

})();

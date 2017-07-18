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
// $('.cart-remove').click(function() {
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

jQuery(document).ready(function($) {
	// initialize constants and selectors
	let urlDef = "http://keynotecommunity.com/take-course/";
	let urlBase = "https://www.keynoteseries.com/course_details/";
	let keynoteCatProducts = $(".kn-product-link");
	let queryString = "?code=";
	let affiliate = $('body').data('affiliate');

	// dynamic link generation for keynote products
	function setCoursesUrl(url) {
		for (let i = 0; i < keynoteCatProducts.length; i++) {
			let output = url + queryString + affiliate;
			// append encoded product title (course) to @url
			if ($('#stateSelectDropdown').val()) {
				let urlCourse = encodeURI(keynoteCatProducts[i].lastElementChild.textContent);
				output = url + "\/" + urlCourse + queryString + affiliate;
			}
			keynoteCatProducts[i].href = output;
			keynoteCatProducts[i].target = "_blank"
		}
	}

	// set links to default on first run
	setCoursesUrl(urlDef);

	// on change handler for dropdown selector
	// dynamically generates link for each product
	$("#stateSelectDropdown").change(function() {
		// reset disabled courses style
		$("#keynoteCourseDisable").remove();
		// remove notice on Other States selection
		$('#cenoticemain').remove();
		// remove individual CE notice on disabled courses
		$('.cenotice').remove();
		// add "grow" hover effect
		$('.kn-product-link p:first-child').addClass('hvr-grow');

		// if select "Select state:" set default URL
		if (!$('#stateSelectDropdown').val()) {
			setCoursesUrl(urlDef);
		// else append value of dropdown option to URL and set
		} else {
			let urlPartner = $('#stateSelectDropdown').val();
			let urlNew = urlBase + urlPartner;
			setCoursesUrl(urlNew);

			// TODO:
			// if "Other State" selected, display main CE notice
			if ($('#stateSelectDropdown').val() == "keynoteseriesprofessionaldevelopment") {
				$('<p style="color:#c6000c">These courses do not offer CE credit and are for professional development only.</p>').attr('id', 'cenoticemain').insertAfter($('#stateSelect'));
			}

			// grab product IDs of disabled (not offered) courses from data-*
			let courseExclPostId = $('#stateSelectDropdown option:selected').data("courseExclPostId");
			// if only 1 (returned integer)
			if (Number.isInteger(courseExclPostId)) {
				// append <style>, greys out all children of href
				let courseId = "#product-" + courseExclPostId;
				let nodeString = "<style id='keynoteCourseDisable'> " + courseId + " > * {opacity: .3} </style>"
				$(document.head).append(nodeString);
				// disable href
				$(courseId).removeAttr('href');
				// remove "grow" hover effect
				$(courseId + " p:first").removeClass('hvr-grow');
				// notice below each product
				$(courseId).after($('<p style="color:#c6000c">Course not for CE credit\nPlease select "Other State"</p>').addClass("cenotice"));

				// if multiple (returned comma delineated string)
			} else if (typeof courseExclPostId === "string") {
				// string -> array
				let courseExclPostIds = courseExclPostId.split(",");
				// begin <style>
				nodeString = "<style id='keynoteCourseDisable'>"
				// for each disabled product ID
				courseExclPostIds.forEach(function(courseExclPostId, i) {
					// grey out all children of href
					let courseId = " #product-" + courseExclPostId;
					let nodeStringHolder = courseId + " > *";
					if (i != courseExclPostIds.length - 1) nodeStringHolder += ",";
					nodeString += nodeStringHolder;
					// disable href
					$(courseId).removeAttr('href');
					// remove "grow" hover effect
					$(courseId + " p:first").removeClass('hvr-grow');
					// notice below each product
					$(courseId).after($('<p style="color:#c6000c">Course not for CE credit\nPlease select "Other State"</p>').addClass("cenotice"));
				})
				// append closing tag, append to <head>
				nodeString += "{opacity: .3} </style>"
				$(document.head).append(nodeString);
			}
		}
	})

})

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
if (window.jQuery === undefined || window.jQuery.fn.jquery !== '1.7.2')
{
   var script_tag = document.createElement('script');
   script_tag.setAttribute("type","text/javascript");
   script_tag.setAttribute("src",
       "http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js");
   if (script_tag.readyState)
   {
     script_tag.onreadystatechange = function ()
     { // For old versions of IE
         if (this.readyState == 'complete' || this.readyState == 'loaded')
         {
             scriptLoadHandler();
         }
     };
   }
   else
   {
     script_tag.onload = scriptLoadHandler;
   }

   (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
}
else
{
   // The jQuery version on the window is the one we want to use
   jQuery = window.jQuery;
   main();
}


/* Called once jQuery has loaded */
function scriptLoadHandler()
{
   jQuery = window.jQuery.noConflict(true);
   main();
}

/* Our Start function */
function main()
{
   jQuery(document).ready(function($)
   {
       /* Get 'embed' parameter from the query */
       var widget = window.widget_embed;
       var domain = encodeURIComponent(window.document.location);

       /* Set 'height' and 'width' according to the content type */
       var iframeContent = '<iframe style="overflow-y: hidden;" height="5000" width="1000" frameborder="0" border="0" cellspacing="0" scrolling="no" src="http://dev.markporterlive.com/' + '?zh_embed=' + widget + '&zh_domain=' + domain + '&timestamp=' + Date.now() + '"></iframe>';

       $("#embed-widget-container").html(iframeContent);
   });
}

})();

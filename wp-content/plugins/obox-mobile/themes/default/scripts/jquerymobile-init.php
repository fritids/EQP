<?php header('Content-type: application/x-javascript');
header("Cache-Control: must-revalidate");
$offset = 72000 ;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);
$script = "";
if(get_option("mobile_no_ajax") == "yes") : $script .= 'jQuery.mobile.ajaxEnabled = false;'; endif;
if(get_option("mobile_page_transition") != "default") : $script .= ' jQuery.mobile.defaultPageTransition = "'.get_option("mobile_page_transition").'";'; endif;
if(get_option("mobile_loading_message") != "Loading" && get_option("mobile_loading_message") != "") : $script .= ' jQuery.mobile.loadingMessage = "'.get_option("mobile_loading_message").'";'; endif;
if(get_option("mobile_loading_error_message") != "Error Loading Page" && get_option("mobile_loading_error_message") != "") : $script .= ' jQuery.mobile.pageLoadErrorMessage = "'.get_option("mobile_loading_error_message").'";'; endif;
if($script != "")
	$script = 'jQuery(document).bind("mobileinit", function(){'.$script.'})'; 
echo $script; ?>
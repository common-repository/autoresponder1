<?php
	if (!function_exists('add_action'))
	{
	    require_once("../../../../wp-config.php");
	}
?>
jQuery(document).ready(function($) {
    // $() will work as an alias for jQuery() inside of this function
	console.log("ready");
	$("login-check").click(function(){
		alert('check');
	});
});

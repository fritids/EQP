<?php
/*
Plugin Name: Domain Switch Theme
Plugin URI: whatever
Description: switch theme base on domain
Version: 0.1dev
Author: herself
status: in development
Author URI: whatever
*/

function maybe_switch_theme($host = false)
{

	$host = ($host) ? $host : str_replace('www.', '', $_SERVER['SERVER_NAME']);

	$themes = array(
	'eqp.ida.cl' => array('eqp','eqp'),
	'eqpmobile.ida.cl' => array('eqpmobile','eqpmobile')
	);
        
	if (isset($themes[$host])){
		$themes = $themes[$host];
		switch_theme($themes[0],$themes[1]);
	}
}

//add_action('setup_theme','maybe_switch_theme');


function uagent_switch_theme($host = false)
{

	$host = ($userganet=="mobile") ? "mobile" : "desktop";

	$themes = array(
	'desktop' => array('eqp'),
	'mobile' => array('eqpmobile')
	);
        
	if (isset($themes[$host])){
		$themes = $themes[$host];
		switch_theme($themes[0],$themes[1]);
	}
}

//add_action('setup_theme','maybe_switch_theme');

?>

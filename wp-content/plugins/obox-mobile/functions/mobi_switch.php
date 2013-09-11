<?php function mobile_switch(){
	global $ocmx_mobile_class;
	if($ocmx_mobile_class->allow_mobile() === true) :
		if($ocmx_mobile_class->site_style() == "mobile") : ?>
			<a class="footer-switch" href="?site_switch=normal" rel="external"><?php _e("Ir al sitio normal"); ?></a>
		<?php else : ?>
			<div class="footer-switch"><a href="?site_switch=mobile" class="clearfix" rel="external"><?php _e("Ir al sitio normal"); ?></a></div>
		<?php endif;
	endif;
}

function ocmx_mobile_styles(){
	wp_register_style('ocmx-mobile', OCMXMOBILEURL."/default-style.css");
	wp_enqueue_style( 'ocmx-mobile');
}
function add_mobile_switch(){
	global $ocmx_mobile_class;
	if($ocmx_mobile_class->allow_mobile() === true && $ocmx_mobile_class->site_style() != "mobile") :
		add_filter("wp_footer", "mobile_switch", 2);
		add_action("wp_print_styles", "ocmx_mobile_styles");	
	endif;
}
add_action("init", "add_mobile_switch");
?>
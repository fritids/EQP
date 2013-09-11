<?php function mobile_custom_bg(){
	if(get_option("mobile_custom_background")) : ?>
    <style>
    	body{background: url('<?php echo get_option("mobile_custom_background"); ?>') repeat;}
    </style>
<?php endif; 
}?>
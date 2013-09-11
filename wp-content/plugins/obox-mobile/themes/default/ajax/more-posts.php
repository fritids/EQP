<?php  function ocmx_load_more_posts(){
	$args = array("offset" => $_GET["offset"], (get_option("posts_per_page")*2));
	query_posts($args);
	
	$i=1; while (have_posts()) : the_post();
		global $post;
		$this_post = get_post($post->ID);
		include(STYLESHEETPATH."/functions/fetch-list.php");
		$i++;
	endwhile;
	die("");
} ?>
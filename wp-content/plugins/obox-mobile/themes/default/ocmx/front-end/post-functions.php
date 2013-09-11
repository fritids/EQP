<?php
/* Template Functions */
function fetch_post_image($use_id, $width, $height)
	{
		$attach_args = array("post_type" => "attachment", "post_parent" => $use_id);
		$attachments = get_posts($attach_args);
		$attach_id = $attachments[0]->ID;
		return  wp_get_attachment_image($attach_id, array($width, $height));
	}
		
function fetch_post_tags($post_id)
	{
		global $wpdb;
		$tags = $wpdb->get_results("SELECT $wpdb->term_relationships.*, $wpdb->terms.* FROM $wpdb->terms INNER JOIN $wpdb->term_relationships ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->terms.term_id WHERE $wpdb->term_relationships.object_id = ".$post_id);
		foreach($tags as $posttag) :
			if(!isset($tag_list)) :
				$tag_list = $posttag->name;
			else :
				$tag_list .= ", ".$posttag->name;
			endif;
		return $tag_list;
	endforeach;
	}

function ocmx_pagination($container_class = "clearfix", $ul_class = "clearfix")
	{
		global $wp_query;
		$request = $wp_query->request;
		$showpages = 6;
		$numposts = $wp_query->found_posts;
		$pagenum = (ceil($numposts/get_option("posts_per_page")));
		$currentpage = get_query_var('paged');
		if($currentpage >= $showpages) :
			$startrow = ($currentpage-1);
			$maxpages = ($startrow + $showpages - 1);
			if($maxpages > $pagenum) :
				$startrow = ($startrow - ($maxpages - $pagenum));
				$maxpages = ($maxpages - ($maxpages - $pagenum));
			endif;
		else :
			$startrow = 1;
			$maxpages  = $showpages;
		endif;
			
		if((get_option("posts_per_page") && $numposts !== 0) && $numposts > get_option("posts_per_page")) :
?>
     <div class="<?php echo $container_class; ?>">
        <ul class="<?php echo $ul_class; ?>">
        	<?php if($currentpage !== 0) : ?>
	            <li class="previous-page"><?php previous_posts_link("Previous"); ?></li>
            <?php endif;
			
			if($startrow !== 1) : ?>
				<li><a href="<?php echo clean_url(get_pagenum_link(1)); ?>" class="other-page">1</a></li>  
			<?php endif;
			
			for($i = $startrow; $i <= $maxpages; $i++) : ?>
				<li><a href="<?php echo clean_url(get_pagenum_link($i)); ?>" class="<?php if($i == $currentpage || ($i == 1 && $currentpage == "")) :?>selected-page<?php else : ?>other-page<?php endif; ?>"><?php echo $i; ?></a></li>  
			<?php endfor;
			
			if($maxpages < $pagenum) : ?>
				<li><a href="<?php echo clean_url(get_pagenum_link($pagenum)); ?>" class="other-page"><?php echo $pagenum; ?></a></li>
			<?php endif; 
			
			if($currentpage !== ceil($numposts/get_option("posts_per_page"))) : ?>
				<li class="next-page"><?php next_posts_link("Next"); ?></li>
			<?php endif; ?>
        </ul>
    </div>
<?php
		endif;
	}
?>
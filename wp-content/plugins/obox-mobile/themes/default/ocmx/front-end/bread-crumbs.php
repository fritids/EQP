<?php function mobile_breadcrumbs($wrap_start = '<h2  class="page-title">', $wrap_end = '</h2>') {
	global $post, $wp, $current_cat, $current_cat_parent, $ocmx_post_types;
	$home_link = get_bloginfo("url");
	if (is_single()) :
		echo $wrap_start;
			$cat_parent = $current_cat[(count($current_cat)-1)]->parent;
			$bread_array[] = array();
			$cat_count = 1;
			while ($cat_parent) :				
				$category = get_category($cat_parent);
				$bread_array[] = array($category->cat_name.' &raquo; ');
				$cat_parent  = $category->parent;
				$cat_count++;
			endwhile;
			for($i = $cat_count; $i > 0; $i--) : echo $bread_array[$i][0]; endfor;
			if(is_array($current_cat)) :
				echo $current_cat[0]->cat_name;
			else:
				the_category();
			endif;
		echo $wrap_end;
	elseif(is_page()) :
		$parent_id = $post->post_parent;
		echo $wrap_start;
			while ($parent_id) :
				$page = get_page($parent_id);
				echo '<a href="'.get_permalink($page->ID).'">'.get_the_title($page->ID).'</a> &raquo; ';
				$parent_id  = $page->post_parent;
			endwhile;
		echo '<a href="'.get_permalink($post->ID).'">'.get_the_title($post->ID).'</a>';
		echo $wrap_end;
	elseif(is_tag()) :
		echo $wrap_start;
			the_tags("", " &raquo; ", "");
		echo $wrap_end;
	elseif (is_archive()) :
		echo $wrap_start;
			$cat_parent = $current_cat->parent;
			$bread_array[] = array();
			$cat_count = 1;
			while ($cat_parent) :				
				$category = get_category($cat_parent);
				$bread_array[] = array($category->cat_name.' &raquo; ');
				$cat_parent  = $category->parent;
				$cat_count++;
			endwhile;
			for($i = $cat_count; $i >= 0; $i--) : echo $bread_array[$i][0]; endfor;
			try {echo $current_cat->name;} catch (Exception $e) {$do="nothing";}
		echo $wrap_end;
	elseif (is_search()) :
		echo $wrap_start;
			_e("Your search results for: ");
			the_search_query();
		echo $wrap_end;
	endif;

} ?>
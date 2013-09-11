<?php get_header(); ?>
    <div id="content-container" data-role="content">
        <?php /* FEATURE WIDGET */	 
		global $ocmx_mobile_class, $slider_widget;
       	if($ocmx_mobile_class->allow_slider() && !is_paged()) :
            $slider_widget = new slider_widget();
            $args = array("name" => "Slider", "id" => "slider-widget");
            $instance = array("post_category" => get_option("mobile_slider_category"), "post_count" => get_option("mobile_slider_count"));
            $slider_widget->widget($args, $instance);
       	endif;
        $i=1; ?>
        <ul class="post-list">
            <?php if (have_posts()) : ?>
                <?php if(get_option("mobile_category_exclude") && get_option("mobile_category_exclude") !== 0):
                    $fetch_category = get_category_by_slug(get_option("mobile_category_exclude"));
                    $use_category = "cat=-".$fetch_category->term_id;
                else :
                    $use_category = "";
                endif;
				if (is_paged()) :
						query_posts($use_category."&paged=".get_query_var('paged'));
				elseif (is_home()) :
					query_posts($use_category);
				endif;
	               
                while (have_posts()) : the_post();
					global $i;
                    $this_post = get_post($post->ID);
                    setup_postdata($post);
            		get_template_part("/functions/fetch-list");
                    $i++;
                endwhile; ?>
            <?php else :
                ocmx_no_posts();
            endif; ?>
        </ul>
        <?php mobile_pagination(); ?>
    </div>

<?php get_footer(); ?>
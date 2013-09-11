<?php
// Fetch the Curreny Category Details
$current_cat = get_category(get_query_var("cat"));
$current_cat_id = $current_cat->term_id;
$current_cat_parent = get_category($current_cat->parent);

get_header(); ?>
<div id="content-container" data-role="content">
	<?php echo mobile_breadcrumbs(); ?>
	<?php if (have_posts()) : ?>
        <ul class="post-list">
            <?php while (have_posts()) : the_post();
                $this_post = get_post($post->ID);
                setup_postdata($post);
            	get_template_part("/functions/fetch-list");
                comments_template();
            endwhile; ?>
        </ul>
        <?php mobile_pagination(); ?>
    <?php else : ?>
        <ul class="post-list">
            <li class="clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>
            	<h3 class="post-title"><a href="#"><?php _e("No Posts", "obox-mobile"); ?></a></h3>
                <div class="copy">
                    <p><?php _e("There are no posts which match your selected criteria."); ?></p>
                </div>
            </li>
        </ul>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
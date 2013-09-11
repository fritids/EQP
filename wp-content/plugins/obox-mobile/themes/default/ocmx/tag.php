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

                include(TEMPLATEPATH."/functions/fetch-list.php");

                comments_template();

            endwhile; ?>

        </ul>

    <?php else :

        ocmx_no_posts();

    endif; ?>

</div>



<?php get_footer(); ?>
<?php
// Fetch the Curreny Category Details
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
        <?php mobile_pagination(); ?>
    <?php else :
        ocmx_no_posts();
    endif; ?>
</div>

<?php get_footer(); ?>
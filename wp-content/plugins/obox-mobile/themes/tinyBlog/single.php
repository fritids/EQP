<?php get_header(); ?>
<div id="content-container" data-role="content">
	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post();
            $this_post = get_post($post->ID);
            setup_postdata($post);
            get_template_part("/functions/fetch-post");
            comments_template();
        endwhile; ?>
    <?php else :
        ocmx_no_posts();
    endif; ?>
</div>

<?php get_footer(); ?>
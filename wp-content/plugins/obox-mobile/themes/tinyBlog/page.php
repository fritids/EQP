<?php get_header(); ?>
<div id="content-container" data-role="content">
	<?php if($post->post_name == "archives") :
		include("archives.php");
	elseif (have_posts()) : ?>
		<?php while (have_posts()) : the_post();
            $this_post = get_post($post->ID);
            get_template_part("/functions/fetch-post");
            comments_template();
        endwhile; ?>
    <?php else :
        ocmx_no_posts();
    endif; ?>
</div>
<?php get_footer(); ?>
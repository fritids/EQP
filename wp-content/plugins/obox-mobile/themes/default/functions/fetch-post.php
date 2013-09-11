<?php global $post;

$post_image = get_mobile_image(460, '', 'thumbnail', 'div', 'post-image', true); 
$format = get_post_format();

if(!function_exists("woo_tumblog_the_title") || isset($format) && $format == '' || !isset($format) ) :
	$image_class = " no-margin";
endif; 
 
if(function_exists("woo_tumblog_the_title")) : ?>
    <div class="post-container <?php if(is_page() || $format == "") : ?>no-margin<?php endif; ?> clearfix">		
        <div class="post post-type-<?php echo $format ?>">
            <div class="title-meta">
                <?php woo_tumblog_the_title($class ="post-title", $icon = false, $before = "", $after = "", $return = false, $outer_element = "h3") ?>
                <?php do_action("mobile_post_meta", "h5"); ?>
            </div>
    		<?php echo $post_image; ?>
            <div class="post-content">
                <div class="copy">
                    <?php woo_tumblog_content(); ?>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div> 

<?php else : ?>
    
    <div class="title-meta <?php echo $image_class; ?>">
        <h3 class="post-title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
        <?php do_action("mobile_post_meta", "h5"); ?>
    </div>
    <?php echo $post_image; ?>
    <div class="post-content no-margin">
        <div class="copy">
            <?php the_content(); ?>
        </div>
    </div>
<?php endif; ?>
<?php do_action("mobile_social_links"); ?>
<?php do_action("mobile_author_bio"); ?>
<?php mobile_advert("post"); ?>
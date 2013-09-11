<?php global $i, $slider_widget;

$link = get_permalink($post->ID);
if(function_exists("woo_tumblog_the_title"))
	$format = get_post_format();

$image_class = "large";
$width = "460";
$height = "200";

$get_post_video = get_post_meta($post->ID, "main_video", true);

if($get_post_video != "") :
	$image_class = "large";
endif;

$post_image = get_mobile_image(460, '', 'thumbnail', 'div', 'post-image', true);

if($post_image == "") :	
	$image_class = "no-image";
endif;

if(!function_exists("woo_tumblog_the_title") || isset($format) && $format == "") :
	$image_class .= " no-margin";
endif; 

$hideme = "";
if(isset($_GET["offset"]) || $i > get_option("posts_per_page")) : $image_class .= " slidedown"; $hideme = "style=\"display: none;\""; endif; ?>


<?php if(!function_exists('woo_tumblog_content')): ?>
<li class="post-container clearfix <?php echo $image_class; ?>" id="post-<?php echo ($post->ID);?>" <?php echo $hideme; ?>>		
	<div class="post post-aside"> 
 		<!-- Post Image Here -->
        <?php do_action( "mobile_post_meta", "h4" ); ?>
 		<h3 class="title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
        
 		<?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
            the_excerpt();
        else :
            the_content(__('Read more', "obox-mobile"));
        endif; ?>
	</div>
<?php elseif ( has_post_format( 'aside' )) : ?>
    <li class="post-container clearfix <?php echo $image_class; ?>" id="post-<?php echo ($post->ID);?>" <?php echo $hideme; ?>>	
        <div class="post post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("aside", "obox-mobile"); ?> </a>
            <?php woo_tumblog_the_title($class ="", $icon = false, $before = "", $after = "", $return = true, $outer_element = "h2") ?>
            <?php do_action( "mobile_post_meta", "h4" ); ?>
            <h3 class="title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
            <?php woo_tumblog_content(); ?>
            
            <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
                the_excerpt();
            else :
            	the_content(__('Read more', "obox-mobile"));
            endif; ?>
        </div>

<?php elseif ( has_post_format( 'image' )) : ?>
    <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>		
        <div class="post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("image", "obox-mobile"); ?> </a>
            
            <?php woo_tumblog_the_title($class ="", $icon = false, $before = "", $after = "", $return = true, $outer_element = "h2") ?>
            <?php woo_tumblog_content(); ?>
            
            <div class="poster">
                <?php do_action( "mobile_post_meta", "h4" ); ?>
                <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
					the_excerpt();
				else :
            		the_content(__('Read more', "obox-mobile"));
				endif; ?>
            </div>
        </div>

<?php elseif ( has_post_format( 'link' )) : ?>
    <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>		
        <div class="post post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("link", "obox-mobile"); ?> </a>
            <?php do_action( "mobile_post_meta", "h4" ); ?>
            
            <?php woo_tumblog_the_title($class ="link-post-link", $icon = false, $before = "", $after = "", $return = false, $outer_element = "div") ?>
            <?php woo_tumblog_content(); ?>
            
            <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
                the_excerpt();
            else :
            	the_content(__('Read more', "obox-mobile"));
            endif; ?>
        </div>

<?php elseif ( has_post_format( 'quote' )) : ?>
    <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>		
        <div class="post post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("quote", "obox-mobile"); ?> </a>
            <?php woo_tumblog_the_title($class ="", $icon = false, $before = "", $after = "", $return = true, $outer_element = "h2") ?>
            <?php do_action( "mobile_post_meta", "h4" ); ?>
            <?php woo_tumblog_content(); ?>
            <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
                the_excerpt();
            else :
            	the_content(__('Read more', "obox-mobile"));
            endif; ?>
        </div>
<?php elseif ( has_post_format( 'video' )) : ?>
   <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>		
        <div class="post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("video", "obox-mobile"); ?> </a>
            
            <?php woo_tumblog_the_title($class ="", $icon = false, $before = "", $after = "", $return = true, $outer_element = "h2") ?>
            <?php woo_tumblog_content(); ?>
            
            <div class="poster">
                <?php do_action( "mobile_post_meta", "h4" ); ?>
                <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
					the_excerpt();
				else :
            		the_content(__('Read more', "obox-mobile"));
				endif; ?>
            </div>
        </div>
<?php elseif ( has_post_format( 'audio' )) : 
	$link_url = get_post_meta( $post->ID, 'audio', true );?>
    <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>	
        <div class="post post-<?php echo $format ?>"> 
            <a class="post-type" href="<?php echo $link ?>"> <?php _e("audio", "obox-mobile"); ?> </a>
            <script type='text/javascript'>
			jQuery(document).ready(function($){ 
				jQuery("#jquery_jplayer_<?php echo $post->ID; ?>").jPlayer({
					ready: function () {
						$(this).jPlayer("setMedia", {
							<?php echo substr( $link_url, -3 ); ?> : "<?php echo $link_url; ?>"
						})
					},
					cssSelectorAncestor : '#audio-player-<?php echo $post->ID; ?>',
					cssSelector : {
						play : '.jp-play',
						pause : '.jp-pause',
						stop : '.jp-stop'
					},
					volume: 50,
					swfPath : '<?php echo get_template_directory_uri(); ?>/scripts/',
					solution: 'html, flash',
					supplied : '<?php echo substr( $link_url, -3 ); ?>',
					backgroundColor : 'transparent'
				});
			});
			</script>
		
			<div id="jquery_jplayer_<?php echo $post->ID; ?>" class="jplayer-embed"></div> 
				<div id="audio-player-<?php echo $post->ID; ?>" class="audio-player">
					<div class="play-pause">
						<a href="#" id="jplayer_play_<?php echo $post->ID; ?>" class="jp-play clearfix" tabindex="1">play</a>
						<a href="#" id="jplayer_pause_<?php echo $post->ID; ?>" class="jp-pause clearfix" tabindex="1">pause</a>
					</div>
					<div class="stop">
						<a href="#" id="jplayer_stop_<?php echo $post->ID; ?>" class="jp-stop" tabindex="1">stop</a>
					</div>
				</div>
			    <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
	                the_excerpt();
	            else :
            		the_content(__('Read more', "obox-mobile"));
	            endif; ?>
	        </div>
		</div>

<?php else : ?>
    <li class="post-container clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>
   		<div class="post"> 
    	<a class="post-type" href="<?php echo $link ?>"><?php if (is_page()) : ?> <?php _e("page", "obox-mobile"); ?> <?php else : ?> <?php _e("post", "obox-mobile"); ?> <?php endif; ?></a>		
       <?php echo $post_image; ?>
            <?php woo_tumblog_the_title($class ="", $icon = false, $before = "", $after = "", $return = true, $outer_element = "h2") ?>
            <?php do_action( "mobile_post_meta", "h4" ); ?>
            <h3 class="title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
            <?php woo_tumblog_content(); ?>
            <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
                the_excerpt();
            else :
            	the_content(__('Read more', "obox-mobile"));
            endif; ?>
	</div>   
<?php endif; ?>
</li>               
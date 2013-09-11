<?php global $i, $slider_widget;

$link = get_permalink($post->ID);
if(function_exists("woo_tumblog_the_title"))
	$format = get_post_format();

$image_class = "";
if (!isset($_GET["offset"]) && isset($i) && $i == 1 && !isset($slider_widget)) :
	$image_class = "large";
	$width = "460";
	$height = "200";
else :
	$width = "55";
	$height = "55";
endif;

$get_post_video = get_post_meta($post->ID, "main_video", true);

if($get_post_video != "") :
	$image_class = "large";
endif;

$post_image = get_mobile_image($width, $height, 'post-thumbnail');

if($post_image == "") :	
	$image_class = "no-image";
endif;

if(!function_exists("woo_tumblog_the_title") || isset($format) && $format == '' || !isset($format) ) :
	$image_class .= " no-margin";
endif; 
$hideme = "";
if(isset($_GET["offset"]) || (isset($i) && $i > get_option("posts_per_page"))) : $image_class .= " slidedown"; $hideme = "style=\"display: none;\""; endif; ?>

<?php if ( has_post_format( 'audio' )) : 
	$link_url = get_post_meta( $post->ID, 'audio', true );?>
    <li class="post-container clearfix" <?php echo $hideme; ?>>	
        <div class="post post-type-<?php echo $format ?>"> 
            <span class="post-icon <?php echo $format ?>"> <a href="<?php echo $link ?>"> <?php echo $format ?> </a></span>
            <h3 class="post-title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
           <?php do_action("mobile_post_meta", "h5"); ?>
           
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
             <?php the_content(__('Read more', "obox-mobile")); ?>
        </div>
 
<?php elseif(function_exists("woo_tumblog_the_title")) : ?>
    <li class="post-container <?php echo $image_class; ?> clearfix" <?php echo $hideme; ?>>	
	    
	    <!-- Post Image Here -->
		<?php echo $post_image; ?>
	
        <div class="post post-type-<?php echo $format ?>">        
        <?php woo_tumblog_the_title($class ="post-title", $icon = true, $before = "", $after = "", $return = false, $outer_element = "h3") ?>
       <?php do_action("mobile_post_meta", "h5"); ?>
		<?php woo_tumblog_content(); ?>
        <?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
                the_excerpt();
            else :
            	the_content(__('Read more', "obox-mobile"));
            endif; ?>
        </div>
    </li>
    
<?php else : ?>
   <li class="clearfix <?php echo $image_class; ?>" <?php echo $hideme; ?>>

		<!-- Display Comment Count -->
		<?php if(get_option("mobile_comments_usage") != "comments_off" && comments_open() && $post->comment_count != "0") : ?>
			<a href="<?php echo $link; ?>#comments" class="comment-count"><span><?php comments_number('0','1','%'); ?></span></a>	
		<?php endif; ?>
	
	    <!-- Post Image Here -->
		<?php echo $post_image; ?>
	    <h3 class="post-title"><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h3>
	   <?php do_action("mobile_post_meta", "h5"); ?>
        <div class="copy">
			<?php if($post->post_excerpt != "" || get_option("mobile_auto_excerpt") == "yes") :
	            the_excerpt();
	        else :
				the_content(__('Read more', "obox-mobile"));
	        endif; ?>
	    </div>
	</li>
<?php endif; ?>
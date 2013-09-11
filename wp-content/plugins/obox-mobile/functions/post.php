<?php function mobile_social_links(){
	global $post;
	$link = get_permalink($post->ID);
	if(get_option("mobile_social_link_usage") != "off") : ?>
	<ul class="post-meta clearfix">
        <li class="social-links">
            <?php if(get_option("mobile_facebook") != "false") : ?>
                <div class="social-facebook">
                    <iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo $link; ?>&amp;layout=button_count&amp;show_faces=true&amp;width=50&amp;action=like&amp;colorscheme=light&amp;height=21" style="border: medium none; overflow: hidden; width: 50px; height: 21px;" allowtransparency="true" frameborder="0" scrolling="no"></iframe>
                </div>
            <?php endif; ?>
            <?php if(get_option("mobile_twitter") != "false") : ?>
                <div class="social-twitter">
                    <a href="http://twitter.com/share" class="twitter-share-button" data-count="none" data-url="<?php the_permalink(); ?>" data-text="<?php the_title()?>">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                </div>
            <?php endif; ?>
            <?php if(get_option("mobile_googleplus") != "false") : ?>
                <div class="social-google">
                    <!-- Place this tag where you want the +1 button to render -->
                    <g:plusone size="medium" count="false" href="<?php echo $link; ?>"></g:plusone>
                    
                    <!-- Place this tag after the last plusone tag -->
                    <script type="text/javascript">
                      (function() { 
                        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                        po.src = 'https://apis.google.com/js/plusone.js';
                        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                      })();
                    </script>
                </div>
            <?php endif; ?>
        </li>
	</ul>
<?php endif;
};

add_action("mobile_social_links", "mobile_social_links"); 

function mobile_post_meta($wrap = "h5"){ 
	if(get_option("mobile_post_meta") == "off") :
		return false;
	elseif((is_single() || is_archive()) && get_option("mobile_post_meta") == "pages") :
		return false;
	elseif(is_page() && get_option("mobile_post_meta") == "posts") :
		return false;
	endif;?>
	<<?php echo $wrap; ?> class="date">
		<?php if(get_option("mobile_post_date") != "false") :  
			echo date_i18n("F j, Y");
			$hasdate = 1; 
		endif;?>
        <?php if(get_option("mobile_post_author") != "false") : ?>  
			<?php if(isset($hasdate)) :
				_e("por", "obox-mobile");
			else :
				_e("Por", "obox-mobile");
			endif; ?> <a href="<?php the_author_meta('url'); ?>"><?php the_author(); ?></a> 
        <?php endif;?>
		<?php if(!is_page()) : 
			if(get_option("mobile_post_tags") != "false") :?>
				<?php the_tags(_("Claves: "),', ');
			endif;
			if(get_option("mobile_post_categories") != "false") :
            	_e("Publicado en", "obox-mobile"); ?> <?php the_category(', ');
			endif;
		endif; ?>
	</<?php echo $wrap; ?>>
    
<?php 
	}
add_action("mobile_post_meta", "mobile_post_meta");

function mobile_author_bio(){ 
	if(get_option("mobile_author_display") == "off") :
		return false;
	elseif((is_single() || is_archive()) && get_option("mobile_author_display") == "pages") :
		return false;
	elseif(is_page() && get_option("mobile_author_display") == "posts") :
		return false;
	endif;?>
	<ul class="comment-container author">
		<li class="comment clearfix">
            <div class="comment-post no-margin">
                <a href="#" class="comment-avatar"><?php echo get_avatar( get_the_author_meta('email'), "45" ); ?></a>
                <h4 class="comment-name"><?php the_author_meta('nickname'); ?></h4>
                <p><?php the_author_meta('description'); ?></p>
			</div>
	    </li>
    </ul>        
<?php 
}
add_action("mobile_author_bio", "mobile_author_bio"); ?>
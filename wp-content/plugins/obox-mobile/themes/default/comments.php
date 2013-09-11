<?php if(get_option("mobile_comments_usage") == "comments_off") :
	return false;
elseif(is_single() && get_option("mobile_comments_usage") == "comments_pages") :
	return false;
elseif(is_page() && get_option("mobile_comments_usage") == "comments_posts") :
	return false;
endif; ?>
	<div class="comments">
	   	<?php comments_number("", __('<h3 class="section-title">1 Comment</h3>','obox-mobile'), __( '<h3 class="section-title">% Comments</h3>','obox-mobile') );?>
	    <ul class="comment-container">
	         <?php  foreach ($comments as $comment) :
	            if ($comment->comment_parent == 0) : ?>
	                <li class="comment clearfix">
	                    <a href="#" class="comment-avatar"><?php echo get_avatar($comment, 45); ?> </a>
	                    <div class="comment-post">
	                        <h5 class="date"> <?php comment_date('d M Y'); ?></h5>
	                        <h4 class="comment-name"><a href="<?php comment_author_url(); ?>" class="commentor_url" name="comment-<?php echo $comment->comment_ID; ?>" rel="nofollow"><?php comment_author(); ?></a></h4>
	                       <?php if ($comment->comment_approved == '0') : ?>
	                            <p><?php _e("Comment is awaiting moderation.", "obox-mobile"); ?></p>
	                        <?php else :
	                            comment_text();
	                        endif; ?>
	                    </div>
						<?php fetch_comments($comment->comment_ID); ?>
	                </li>
	            <?php endif; ?>
	        <?php endforeach; ?>
	    </ul>
	</div>
	<div class="leave-comment">
	    <h3 class="section-title"><?php _e("Leave a Comment", "obox-mobile"); ?></h3>
	    <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" class="comment_form">
        	<?php if ($user_ID ) : ?>
               <p>
                   Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php" class="std_link"><?php echo $user_identity; ?></a>.
                   <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="Log out of this account">Logout</a>
               </p>
            <?php else : ?>
                <p>
                    <input type="text" name="author" maxlength="50" value="<?php if($comment_author != ""){echo $comment_author;}; ?>" />
                    <label><?php _e("Name", "obox-mobile"); ?></label>
                </p>
                <p>
                    <input type="text" name="email" maxlength="200" value="<?php if($comment_author_email != ""){echo $comment_author_email;};?>" />
                    <label><?php _e("Email", "obox-mobile"); ?></label>
                </p>
                <p>
                    <input type="text" name="url" maxlength="50" value="<?php if($comment_author_url != ""){echo $comment_author_url;}; ?>" />
                    <label><?php _e("Link", "obox-mobile"); ?></label>
                </p>
			<?php endif; ?>
	        <p>
	            <input type="text" name="twitter" maxlength="50" value="" />
	            <label><?php _e("Twitter", "obox-mobile"); ?></label>
	        </p>
	        <p>
	            <textarea  name="comment"></textarea>
	        </p>
	        <input type="submit" class="submit_button" value="Post Comment" name="cmdSubmit" />
	        <input type="hidden" id="comment_post_id" name="comment_post_ID" value="<?php echo $id; ?>" />
	        <input type="hidden" id="comment_parent_id" name="comment_parent_id" value="0" />                         
	    </form>		
	</div>
<?php function show_comments($comment)
	{
		// Adjust the classes according to whether or not we're replying to another comment
		global $header_class, $main_class, $footer_class, $comment, $wpdb, $comment_post_ID;
		
		$comment_table = $wpdb->prefix . "ocmx_comment_meta";
		$comment_meta_sql = "SELECT * FROM $comment_table WHERE commentId = ".$comment->comment_ID." LIMIT 1";
		$fetch_comment_count = $wpdb->get_row( $wpdb->prepare("SELECT comment_count FROM $wpdb->posts WHERE ID = %d", $comment_post_ID) );
		$comment_meta = $wpdb->get_row($comment_meta_sql); 
		/* Threaded Comments */
		if(isset($_POST['comment_parent']) && $_POST['comment_parent'] !== "0" && $_POST['comment_parent'] !== "") : 
			/* Count the Child Comments */
			$sql = "SELECT * FROM $wpdb->comments WHERE comment_parent = ".$_POST['comment_parent'];
			$child_comments =  $wpdb->get_results($sql); ?>
			<li class="comment clearfix">
				<div class="comment-meta">
					<h4 class="comment-name">
						<?php if($comment->comment_author_url !== "http://" && $comment->comment_author_url !== "") : ?>
						   <a href="<?php echo $comment->comment_author_url; ?>" class="commentor_url" name="comment-<?php echo $comment->comment_ID; ?>" rel="nofollow"> <?php echo $comment->comment_author; ?></a>
						<?php else : ?>
							 <?php echo $comment->comment_author; ?>
						<?php endif; ?>
					</h4>
					<h5 class="date">
						<?php if($comment_meta->twitter !== "") : ?><a href="http://twitter.com/<?php echo $comment_meta->twitter; ?>" class="twitter-link" rel="nofollow">@<?php echo $comment_meta->twitter; ?></a><?php endif; ?>
						<?php echo date('d/m/Y', strtotime($comment->comment_date)); ?> <?php echo date("H\:i a", strtotime($comment->comment_date)); ?>
					</h5>
				</div>
				<div class="comment-post">
					<?php if ($comment->comment_approved == '0') : ?>
						<p>Comment is awaiting moderation.</p>
					<?php else :
						$use_comment = apply_filters('wp_texturize', $comment->comment_content);
						$use_comment = str_replace("\n", "<br>", $use_comment);
						echo "<p>".$use_comment."</p>";
					endif; ?>
				</div>
			</li>
		<?php 
		/* Regular Comments */
		else: ?>
			<?php if($fetch_comment_count->comment_count == "1") : ?>
				<h3 class="section-title">1 <?php _e("Comment"); ?></h3>
			<?php endif; ?>
			<ul class="comment-container">
				<li class="comment clearfix">
					<div class="comment-avatar">
						<span class="tear-drop"></span>
						<?php echo get_avatar( $comment, 50); ?>
					</div>
					<div class="comment-meta">
						<h4 class="comment-name">
						   <?php if($comment->comment_author_url !== "http://" && $comment->comment_author_url !== "") : ?>
							   <a href="<?php echo $comment->comment_author_url; ?>" name="comment-<?php echo $comment->comment_ID; ?>" rel="nofollow"> <?php echo $comment->comment_author; ?></a>
							<?php else : ?>
								 <?php echo $comment->comment_author; ?>
							<?php endif; ?>
						</h4>
						<h5 class="date">
							<?php echo date('F d Y', strtotime($comment->comment_date)); ?>
							<br />
							<?php if(($comment_meta->twitter) && $comment_meta->twitter !== "") : ?>
								<a href="http://twitter.com/<?php echo $comment_meta->twitter; ?>" class="twitter-link" rel="nofollow">@<?php echo $comment_meta->twitter; ?></a>
							<?php endif; ?>     
						</h5>
					</div>
					<div class="comment-post">
						<?php if ($comment->comment_approved == '0') : ?>
							<p>Comment is awaiting moderation.</p>
						<?php else :
							$use_comment = apply_filters('wp_texturize', $comment->comment_content);
							$use_comment = str_replace("\n", "<br />", $use_comment);
							echo "<p>".$use_comment."</p>";
						endif; ?>
						<?php $comment_id = $comment->comment_ID; ?>
					</div>
				</li>
			</ul>
		<?php endif; ?>
<?php } ?>
<?php /*****************************/
	/* Threaded Replies Function */
	function fetch_comments($comment_id)
		{		
			global $wpdb;
			$sql = "SELECT * FROM $wpdb->comments WHERE comment_parent = ".$comment_id;
			$child_comments =  $wpdb->get_results($sql);
			$thread_count = 0;
			if(count($child_comments) !== 0) :
				$thread_count++ ?>
                <ul class="threaded-comments">
                    <?php
                        foreach($child_comments as $sub_comment) :
                            $this_comment = get_comment($sub_comment->comment_ID);
                            $comment_table = $wpdb->prefix . "ocmx_comment_meta";
                            $sub_comment_meta_sql = "SELECT * FROM $comment_table WHERE commentId = ".$sub_comment->comment_ID." LIMIT 1";
                            $sub_comment_meta = $wpdb->get_row($sub_comment_meta_sql);
                    ?>
                    <li class="comment clearfix">
                        <div class="comment-post">
                            <h5 class="date"><?php echo date('F d Y', strtotime($sub_comment->comment_date)); ?></h5>
                            <h4 class="comment-name">
                            	<?php if($sub_comment->comment_author_url !== "http://" && $sub_comment->comment_author_url !== "") : ?>
                                   <a href="<?php echo $sub_comment->comment_author_url; ?>" name="comment-<?php echo $sub_comment->comment_ID; ?>" rel="nofollow"> <?php echo $sub_comment->comment_author; ?></a>
                                <?php else : ?>
                                     <?php echo $sub_comment->comment_author; ?>
                                <?php endif; ?>
                            </h4>
                           	<?php if ($sub_comment->comment_approved == '0') : ?>
                                <p>Comment is awaiting moderation.</p>
                            <?php else :
                                $use_comment = apply_filters('wp_texturize', $this_comment->comment_content);
                                $use_comment = str_replace("\n", "<br>", $use_comment);
                                echo "<p>".$use_comment."</p>";
                            endif; ?>
                        </div>
						<?php fetch_comments($sub_comment->comment_ID); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
<?php
			endif;
		}
?>
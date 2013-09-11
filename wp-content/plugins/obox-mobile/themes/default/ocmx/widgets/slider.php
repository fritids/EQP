<?php
class slider_widget extends WP_Widget {
    /** constructor */
    function slider_widget() {
        parent::WP_Widget(false, $name = "Slider Widget", array("description" => "Featured Posts widget."));	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
		if(!($instance["post_category"])) :
			$use_catId = 0;
		else :
	        $use_category = $instance["post_category"];
			$use_catId = get_category_by_slug($use_category);
		endif;
		
		if(!($instance["post_count"])) :
			$count = 3;
		else :
			$count = $instance["post_count"];
		endif;
		
		if(!($instance["read_more"])) :
			$readmoretext = _("Read More");
		else :
	        $readmoretext = $instance["read_more"];
		endif;
		
		if(!($instance["show_thumbnails"])) :
			$show_thumbs = 1;
		else :
			$show_thumbs = $instance["show_thumbnails"];
		endif;
		//Set the post Aguments and Query accordingly
		rewind_posts();
		$ocmx_featured = new WP_Query("cat=".$use_catId->term_id."&posts_per_page=".$count);
?>
        <div class="slider">
            <ul id="feature-slider">
                <?php  $count = 1;
                while ($ocmx_featured->have_posts()) : $ocmx_featured->the_post();
                    $this_post = get_post($post->ID);
                    $link = get_permalink($this_post->ID); 
					$post_image = get_mobile_image(460, 240, 'thumbnail'); 
					if($post_image == ""): 
						$attach_args = array("post_type" => "attachment", "post_parent" => $this_post->ID);
						$attachments = get_posts($attach_args);
						$attach_id = $attachments[0]->ID;
						$post_image = wp_get_attachment_image($attach_id, array(460, 240));
					endif; ?>
                    <li>
						<?php echo $post_image; ?>
                        <h2><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h2>
                    </li>
                <?php 
                    $count++;
                endwhile; ?>
            </ul>
            <div class="post-count"><span>1</span>/<?php echo ($count-1); ?></div>
            <div class="no_display" id="feature-count"><?php echo ($count-1); ?></div>
        </div>
<?php

    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $post_category = esc_attr($instance["post_category"]);
	    $auto_interval = $instance["auto_interval"];
		$show_thumbs = $instance["show_thumbnails"];
?>
            <p><label for="<?php echo $this->get_field_id('post_category'); ?>">Category</label>
               <select size="1" class="widefat" id="<?php echo $this->get_field_id("post_category"); ?>" name="<?php echo $this->get_field_name("post_category"); ?>">
                    <option <?php if($post_count == 0){echo "selected=\"selected\"";} ?> value="0">All</option>
                    <?php
							$category_args = array('hide_empty' => false);
                            $option_loop = get_categories($category_args);
                            foreach($option_loop as $option_label => $value)
                                { 	
                                    // Set the $value and $label for the options
                                    $use_value =  $value->slug;
                                    $label =  $value->cat_name;
                                    //If this option == the value we set above, select it
                                    if($use_value == $post_category)
                                        {$selected = " selected='selected' ";}
                                    else
                                        {$selected = " ";}
                    ?>
                                    <option <?php echo $selected; ?> value="<?php echo $use_value; ?>"><?php echo $label; ?></option>
                    <?php 
                                }
                    ?>
                </select>
			</p>
            <p><label for="<?php echo $this->get_field_id('show_thumbnails'); ?>">Show Selection Blocks</label>
               <select size="1" class="widefat" id="<?php echo $this->get_field_id("show_thumbnails"); ?>" name="<?php echo $this->get_field_name("show_thumbnails"); ?>">
                    <option <?php if($show_thumbs !== "no"){echo "selected=\"selected\"";} ?> value="yes">Yes</option>
                    <option <?php if($show_thumbs == "no"){echo "selected=\"selected\"";} ?> value="no">No</option>
                </select>
			</p>
            <p><label for="<?php echo $this->get_field_id('auto_interval'); ?>">Auto Slide Interval (seconds)<input class="shortfat" id="<?php echo $this->get_field_id('auto_interval'); ?>" name="<?php echo $this->get_field_name('auto_interval'); ?>" type="text" value="<?php echo $auto_interval; ?>" /><br /><em>(Set to 0 for no auto-sliding)</em></label></p>
<?php 
	} // form

}// class


?>
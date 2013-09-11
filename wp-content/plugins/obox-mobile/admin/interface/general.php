<?php function fetch_mobile_options($input)
	{	
		global $counter, $label_class;
		if($input) : ?>
    <li class="admin-block-item">
        <div class="admin-description">
            <?php if($input["main_section"]) : ?>
                <h4><?php _e($input["main_section"]);?></h4>
                <?php if($input["main_description"] !== "") : ?>
                    <p><?php _e($input["main_description"]);?></p>
                <?php endif; ?>
            <?php else : ?>
                <h4><?php _e($input["label"]);?></h4>
                <?php if($input['description'] !== "") : ?>
                    <p><?php _e($input["description"]);?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="admin-content">
            <?php if(isset($input["main_section"])) : ?>
            	<?php if(isset($input["note"])) : ?>
	                <p><em><?php _e($input["note"]);?></em></p>
				<?php endif; ?>
                <ul class="form-options contained-forms">
                    <?php foreach($input["sub_elements"] as $sub_input) : 
						if(isset($sub_input["linked"])) :
							$option = $sub_input["linked"];
							if(get_option($option) == "false" || get_option($option) == "no" || get_option($option) == "off")
								{$hideme=1;}
							else
								{unset($hideme);}
							$showif = "rel=\"".$sub_input["linked"]."\"";
						endif;?>
                        <li <?php if(isset($hideme)) : ?>class="no_display"<?php endif; ?> <?php if(isset($showif)) : echo $showif; endif; ?>>
                            <label class="parent-label"><?php echo $sub_input["label"]; ?></label>
                            <div class="form-wrap">
                                <?php create_mobile_form($sub_input, count($input), "child-form"); ?>
                            </div>
                            <?php if($sub_input["description"] !== "") : ?>
                                <span class="tooltip"><?php echo $sub_input["description"]; ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : 
                create_mobile_form($input, count($input), $label_class);
            endif; ?>
        </div>
    </li>
<?php endif;
	}
add_action("fetch_mobile_options", "fetch_mobile_options"); ?>
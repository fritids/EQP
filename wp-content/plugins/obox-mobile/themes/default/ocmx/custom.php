<?php 
// Custom fields for WP write panel

$obox_meta = array(
		"media" => array (
			"name"			=> "other_media",
			"default" 		=> "",
			"label" 		=> "Main Image",
			"desc"      	=> "Select a cover image to use for your post.",
			"input_type"  	=> "image",
			"input_size"	=> "50",
			"img_width"		=> "535",
			"img_height"	=> "255"
		),
		"video" => array (
			"name"			=> "main_video",
			"default" 		=> "",
			"label" 		=> "Video Object",
			"desc"      	=> "Input the embed code of your video here.",
			"input_type"  	=> "textarea"
		)
	);

function create_meta_box_ui() {
	global $post, $obox_meta;
		$meta_count = 0;
		$post_layout = get_post_meta($post->ID,"ocmx_post_layout",true)
?>
	<table class="obox_metaboxes_table">
		<?php foreach ($obox_meta as $metabox) :
			$obox_metabox_value = get_post_meta($post->ID,$metabox["name"],true);
			
			if ($obox_metabox_value == "" || !isset($obox_metabox_value)) :
				$obox_metabox_value = $metabox['default'];
			endif; ?>
			<tr>
				<td width="20%" valign="top" class="obox_label">
					<label for="<?php echo $metabox; ?>"><?php echo $metabox["label"]; ?></label>
					<p><?php echo $metabox["desc"] ?></p>
				</td>
				<td colspan="3">
					<?php if($metabox["input_type"] == "image") : ?>
						<div class="obox_main_image_upload">
                            <p><strong>Select Image</strong></p>
                            <div class="image_upload"><input type="file" name="<?php echo "obox_".$metabox["name"]."_file"; ?>" /></div>
                            <p><strong>Image Preview</strong></p>
    	                    <?php if($obox_metabox_value !== "") : ?>
								<input class="obox_input_text" type="text" name="<?php echo "obox_".$metabox["name"]; ?>" id="<?php echo $metabox ?>" value="<?php echo $obox_metabox_value; ?>" size="<?php echo $metabox["input_size"] ?>" />
							<?php else : ?>
								<input class="obox_input_text" type="text" name="<?php echo "obox_".$metabox["name"]; ?>" />
							<?php endif; ?>
						</div>
                        <?php if($obox_metabox_value !== "") : ?>
                            <p><strong>Image</strong></p>
                            <div class="obox_main_image">
                                <img src="<?php echo $obox_metabox_value; ?>" />
                            </div>
                        <?php endif; ?>
					<?php elseif($metabox["input_type"] == "textarea") : ?>
						<textarea class="obox_metabox_fields" style="width: 70%;" rows="8" name="<?php echo "obox_".$metabox["name"]; ?>" id="<?php echo "obox_".$metabox["name"]; ?>"><?php echo $obox_metabox_value; ?></textarea>
					<?php else : ?>
						<input class="obox_metabox_fields" type="text" name="<?php echo "obox_".$metabox["name"]; ?>" id="<?php echo "obox_".$metabox["name"]; ?>" value="<?php echo $obox_metabox_value; ?>" size="<?php echo $metabox["input_size"] ?>" />
					<?php endif; ?>                
					
				</td>
			</tr>
		<?php endforeach; ?>
    </table>
    <br />    
<?php
}
function insert_obox_metabox($pID) {
	global $obox_meta, $use_file_field, $set_width, $set_height, $image_name, $upload, $meta_added;
	$i = 0;
	if(!isset($meta_added)) :
		foreach ($obox_meta as $metabox) {
			$var = "obox_".$metabox["name"];
			if (isset($_POST[$var])) :
				if($metabox["input_type"] == "image") :
					$use_file_field = $var."_file";				
					/* Check if we've actually selected a file */
					if(!empty($_FILES) && $_FILES[$use_file_field]["name"] !== "") :			
						$upload = wp_upload_bits($_FILES[$use_file_field]["name"], null, file_get_contents($_FILES[$use_file_field]["tmp_name"]));
						
						$resized_image = ocmx_custom_resize($upload["file"], $use_width, $use_height, true);
						
						mobile_add_attachment($upload);
											
						//Update Post Meta
						add_post_meta($pID, $metabox["name"], $upload["url"],true) or update_post_meta($pID,  $metabox["name"], $upload["url"]);
					else :
						//Update Post Meta
						add_post_meta($pID,$metabox["name"],$_POST[$var],true) or update_post_meta($pID,$metabox["name"], $_POST[$var]);
					endif;
				else :
					add_post_meta($pID,$metabox["name"],$_POST[$var],true) or update_post_meta($pID,$metabox["name"],$_POST[$var]);				
				endif;
			endif;
		}
		$meta_added = 1;
	endif;
}
function ocmx_change_metatype(){
?>
	<script type="text/javascript">
    /* <![CDATA[ */
        jQuery(window).load(function(){
            jQuery('form#post').attr('enctype','multipart/form-data');
        });
    /* ]]> */
    </script>
	<style type="text/css">
    .obox_input_text 				{width: 64%; padding: 5px; margin: 0 0 10px 0; background: #f4f4f4; color: #444; font-size: 11px;}
    .obox_input_select 				{width: 60%; padding: 5px; margin: 0 0 10px 0; background: #f4f4f4; color: #444; font-size: 11px;}
    .obox_input_checkbox 			{margin: 0 10px 0 0; }
    .obox_input_radio 				{margin: 0 10px 0 0; }
    .obox_input_radio_desc 			{color: #666; font-size: 12px;}
    .obox_spacer 					{display: block; height: 5px;}
	
	.obox_main_image				{float: left; margin-right: 20px; border: 5px solid #f5f5f5; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px;} 
		.obox_main_image img		{-webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; max-width: 600px;} 
	
	.obox_label						{ width: 30%;}
	.obox_label label				{display: block; font-weight: bold;}
	.obox_label p					{clear: both; padding: 0px; margin: 10px 0px 0px !important; color: #595959; font-style: italic;}
    
    .obox_metabox_desc				{display: block; font-size: 10px; color: #aaa;}
    .obox_metaboxes_table			{width:100%; border-collapse: collapse;}
	.obox_metaboxes_table tr		{border-bottom: 1px solid #e0e0e0;}
	.obox_metaboxes_table tr:last-child	{border-bottom: none;}
    .obox_metaboxes_table th,
    .obox_metaboxes_table td		{padding: 10px; text-align: left; vertical-align: top;}
	.obox_metaboxes_table textarea	{width: 90% !important;}
    .obox_metabox_names				{width: 20%}
    .obox_metabox_fields			{width: 80%}
    .obox_metabox_image				{text-align: right;}
    .obox_red_note					{margin-left: 5px; color: #c77; font-size: 10px;}
    .obox_input_textarea			{width: 90%; height: 120px; padding: 5px; margin: 0px 0px 10px 0px; background: #f0f0f0; color: #444; font-size: 11px;}
	#excerpt						{height: 120px;}
    </style>
<?php }

function add_obox_meta_box() {
	if (function_exists('add_meta_box') ) {
		add_meta_box('obox-meta-box',$GLOBALS['themename'].' Options','create_meta_box_ui','post','normal','high');
		add_meta_box('obox-meta-box',$GLOBALS['themename'].' Options','create_meta_box_ui','page','normal','high');
	}
}

function my_page_excerpt_meta_box() {
	add_meta_box( 'postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', 'page', 'normal', 'core' );
}

add_action('admin_menu', 'add_obox_meta_box');
add_action('admin_menu', 'my_page_excerpt_meta_box');
add_action('admin_head', 'ocmx_change_metatype');
add_action('save_post', 'insert_obox_metabox');
add_action('plublish_post', 'insert_obox_metabox');  ?>
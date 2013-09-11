<?php 
// Custom fields for WP write panel

$obox_seo_meta = array(
		"title" => array (
			"name"			=> "meta_title",
			"default" 		=> "",
			"label" 		=> "SEO - Title",
			"desc"      	=> "Set a custom meta title for this post",
			"input_type"  	=> "text"
		),
		"keywords" => array (
			"name"			=> "meta_keywords",
			"default" 		=> "",
			"label" 		=> "SEO - Keywords",
			"desc"      	=> "Set keywords",
			"input_type"  	=> "text"
		),
		"description" => array (
			"name"			=> "meta_description",
			"default" 		=> "",
			"label" 		=> "SEO - Description",
			"desc"      	=> "Set the meta description for this post.",
			"input_type"  	=> "textarea"
		)
	);
function seo_meta_box_ui() {
	global $post, $obox_seo_meta;
	$meta_count = 0;
?>
	<table class="obox_metaboxes_table">
		<?php foreach ($obox_seo_meta as $metabox) :
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
                	<?php if($metabox["input_type"] == "textarea") : ?>
						<textarea class="obox_metabox_fields" rows="3" name="<?php echo "obox_".$metabox["name"]; ?>" id="'.$metabox.'"><?php echo $obox_metabox_value; ?></textarea>
					<?php else : ?>
						<input class="obox_metabox_fields" type="text" name="<?php echo "obox_".$metabox["name"]; ?>" id="<?php echo $metabox ?>" value="<?php echo $obox_metabox_value; ?>" size="<?php echo $metabox["input_size"] ?>" />
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
    </table>
    <br />    
<?php
}
function insert_seo_metabox($pID) {
	global $obox_seo_meta, $seo_meta_added;
	$i = 0;
	if(!isset($seo_meta_added)) :
		foreach ($obox_seo_meta as $metabox) :
			$var = "obox_".$metabox["name"];
			if (isset($_POST[$var])) :
				add_post_meta($pID,$metabox["name"],$_POST[$var],true) or update_post_meta($pID,$metabox["name"],$_POST[$var]);
			endif;
		endforeach;
		$seo_meta_added = 1;
	endif;
}
function add_seo_meta_box() {
	if (function_exists('add_meta_box') ) {
		add_meta_box('obox-seo-meta-box', 'OCMX - SEO','seo_meta_box_ui','post','normal','high');
		add_meta_box('obox-seo-meta-box', 'OCMX - SEO','seo_meta_box_ui','page','normal','high');
	}
}
add_action('admin_menu', 'add_seo_meta_box');
add_action('save_post', 'insert_seo_metabox'); ?>
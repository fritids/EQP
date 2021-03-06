<?php 

/*--------------------------------------------------------------------------
*
*	Acf_options_page
*
*	@author Elliot Condon
*	@since 2.0.4
* 
*-------------------------------------------------------------------------*/
 
 
class Acf_options_page 
{

	var $parent;
	var $dir;
	
	var $menu_name;
	var $menu_heading;
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	Acf_options_page
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function Acf_options_page($parent)
	{
		// vars
		$this->parent = $parent;
		$this->dir = $parent->dir;
		
		
		// Customize the Labels here 
		$this->menu_name = __('Options','acf');
		$this->menu_heading = __('Options','acf');
		
		
		// actions
		add_action('admin_menu', array($this,'admin_menu'));
		
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_menu
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_menu() 
	{
	
		// add page
		$options_page = add_menu_page('acf_options', $this->menu_name, 'edit_posts', 'acf-options',array($this, 'options_page'));
		
		
		// some fields require js + css
		add_action('admin_print_scripts-'.$options_page, array($this, 'admin_print_scripts'));
		add_action('admin_print_styles-'.$options_page, array($this, 'admin_print_styles'));
		
		
		// Add admin head
		add_action('admin_head-'.$options_page, array($this,'admin_head'));

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function admin_head()
	{
		//if(!array_key_exists('options_page', $this->parent->activated_fields)){exit;}
		
		
		// save
		if(isset($_POST['update_options']))
		{
			$this->update_options();
		}
		
		
		// create tyn mce instance for wysiwyg
		wp_tiny_mce();
		
		
		// add these acf's to the page
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.global.css" />';
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/css/style.input.css" />';
		echo '<script type="text/javascript" src="'.$this->dir.'/js/functions.input.js" ></script>';
		
		
		// date picker!
		echo '<link rel="stylesheet" type="text/css" href="'.$this->dir.'/core/fields/date_picker/style.date_picker.css" />';
		echo '<script type="text/javascript" src="'.$this->dir.'/core/fields/date_picker/jquery.ui.datepicker.js" ></script>';
	}
	
	
	/*---------------------------------------------------------------------------------------------
	 * admin_print_scripts / admin_print_styles
	 *
	 * @author Elliot Condon
	 * @since 2.0.4
	 * 
	 ---------------------------------------------------------------------------------------------*/
	function admin_print_scripts() {
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		
		
		// wysiwyg
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('word-count');
		wp_enqueue_script('post');
		wp_enqueue_script('editor');
		
		
		// repeater
		wp_enqueue_script('jquery-ui-sortable');
		
	}
	
	function admin_print_styles() {
		wp_enqueue_style('thickbox');
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	options_page
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function options_page()
	{
		//if(!array_key_exists('options_page', $this->parent->activated_fields)){exit;}
		
		
		// load acf's
		$acfs = get_pages(array(
			'numberposts' 	=> 	-1,
			'post_type'		=>	'acf',
			'sort_column' 	=>	'menu_order',
		));
		
		// blank array to hold acfs
		$add_acf = array();
		
		if($acfs)
		{
			foreach($acfs as $acf)
			{
				$add_box = false;
				$location = $this->parent->get_acf_location($acf->ID);
				
				
				if($location->allorany == 'all')
				{
					// ALL
					
					$add_box = true;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules dont return true, dont add this acf
							if(!$this->parent->match_location_rule($post, $rule))
							{
								$add_box = false;
							}
						}
					}
					
				}
				elseif($location->allorany == 'any')
				{
					// ANY
					
					$add_box = false;
					
					if($location->rules)
					{
						foreach($location->rules as $rule)
						{
							// if any rules return true, add this acf
							if($this->parent->match_location_rule($post, $rule))
							{
								$add_box = true;
							}
						}
					}
				}
				
				if($add_box == true)
				{
					$add_acf[] = $acf;
				}
			}
		}
		
		?>
		
		<div class="wrap">
		
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php echo $this->menu_heading; ?></h2>
			
			<?php if(isset($_POST['update_options'])): ?>
				<div class="updated settings-error" id="setting-error-settings_updated"><p><strong><?php _e("Settings saved",'acf'); ?></strong></p></div>
			<?php endif; ?>
			
			<form id="post" method="post" name="post">
				
				<div class="metabox-holder has-right-sidebar" id="poststuff">
				
					<div class="inner-sidebar" id="side-info-column">
						
						<?php if($add_acf): ?>
						<div class="postbox">
							<h3 class="hndle"><span><?php _e("Save",'acf'); ?></span></h3>
							<div class="inside">
								
								<input type="submit" class="button-primary" value="Save Options" name="update_options">
								
							</div>
						</div>
						<?php endif; ?>
						
					</div>
						

				
				<div id="post-body">
				<div id="post-body-content">
				<div id="acf_input" class="postbox">
				<div class="acf_fields_input">
				<?php 
	
				$i = 0;
				if($add_acf)
				{
				foreach($add_acf as $acf)
				{
				
					// load acf data
					$options = $this->parent->get_acf_options($acf->ID);
					$fields = $this->parent->get_fields($acf->ID);
					$html = '';
					
					
					if($options->field_group_layout == "in_box")
					{
						echo '<div class="postbox"><div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>'.$acf->post_title.'</span></h3><div class="inside">';
					}
			
			
					foreach($fields as $field)
					{
					
						// if they didn't select a type, skip this field
						if($field->type == 'null')
						{
							continue;
						}
						
						
						// set value, id and name for field
						$field->value_id = $this->parent->load_value_id_input($post->ID, $field);
						$field->value = $this->parent->load_value_for_input($post->ID, $field);
						$field->input_name = 'acf['.$i.'][value]';
						$field->input_class = '';
						
						
						echo '<div class="field">';
						
							echo '<input type="hidden" name="acf['.$i.'][field_id]" value="'.$field->id.'" />';
							echo '<input type="hidden" name="acf['.$i.'][field_type]" value="'.$field->type.'" />';
							echo '<input type="hidden" name="acf['.$i.'][value_id]" value="'.$field->value_id.'" />';
							
							
							echo '<label for="'.$field->input_name.'">'.$field->label.'</label>';
						
							
							if($field->instructions)
							{
								echo '<p class="instructions">'.$field->instructions.'</p>';
							}
							
							
							$this->parent->create_field($field);
					
						echo '</div>';
						
						$i++;
					} 
					
					
					if($options->field_group_layout == "in_box")
					{
						echo '</div></div>';
					}
				}
				}
				else
				{
					?>
					
					<div class="postbox">
						<div title="Click to toggle" class="handlediv"><br></div>
						<h3 class="hndle"><span><?php _e("No Options",'acf'); ?></span></h3>
						
						<div class="inside">
							<div class="field">
								<p><?php _e("Sorry, it seems there are no fields for this options page.",'acf'); ?></p>
							</div>
						</div>
					</div>
					
					<?php
				}
				
				
				?>
				</div>
				</div>
				</div>
				</div>
				</div>
			</form>
		</div>
		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	options_page
	*
	*	@author Elliot Condon
	*	@since 2.0.4
	* 
	*-------------------------------------------------------------------------------------*/
	function update_options()
	{
		// vars
		global $wpdb;
		$table_name = $wpdb->prefix.'acf_values';
		$post_id = 0;
		
		
		// remove all old values from the database
		$wpdb->query("DELETE FROM $table_name WHERE post_id = '$post_id'");
			
		
	    foreach($_POST['acf'] as $field)
	    {	
	    	if(method_exists($this->parent->fields[$field['field_type']], 'save_input'))
			{
				$this->parent->fields[$field['field_type']]->save_input($post_id, $field);
			}
			else
			{
				//$field = apply_filters('wp_insert_post_data', $field);
				$field = stripslashes_deep( $field );
				
				
				// if select is a multiple (multiple select value), you need to save it as an array!
				if(is_array($field['value']))
				{
					$field['value'] = serialize($field['value']);
				}
				
				
				// create data object to save
				$data = array(
					'post_id'	=>	$post_id,
					'field_id'	=>	$field['field_id'],
					'value'		=>	$field['value']
				);
				
				// if there is an id, this value already exists, so save it in the same ID spot
				if($field['value_id'])
				{
					$data['id']	= $field['value_id'];
				}
				
				
				// insert new data
				$new_id = $wpdb->insert($table_name, $data);
			}
			
						
	    }
	    
	}
	
}

?>

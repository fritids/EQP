<?php class mobile_plugin_class {
	
	//! A list of all the known plugin hooks
	var $plugin_hooks;
	var $plugin_whitelist = array( "akismet", "obox-mobile", "woo-tumblog");
	
	// List all the files & their relative path in a specific directory
	function list_files( $dir, $file_types, $rel_path = '' ) {
			$files = array();
			
			if ( !is_array( $file_types ) ) :
				$file_types = array( $file_types );	
			endif;
					
			$d = opendir( $dir );
			if ( $d ) :
				while ( ( $f = readdir( $d ) ) !== false ) :
					if ( $f == '.' || $f == '..' || $f == '.svn' ) continue;
					
					if ( is_dir( $dir . '/' . $f ) ) {
						$files = array_merge( $files, $this->list_files( $dir . '/' . $f, $file_types, $rel_path . '/' . $f ) );	
					} else {					
						foreach( $file_types as $file_type ) {
							if ( strpos( $f, $file_type ) !== false ) {
								$files[] = $rel_path . '/' . $f;
								break;	
							}	
						}
					}
				endwhile;
				
				closedir( $d );	
			endif;
			
			return $files;	
		}

	// List all the files in a specific directory
	function list_files_in_dir( $directory_name, $extension ) {
		$files = array();
		
		$dir = @opendir( $directory_name );
		
		if ( $dir ) {
			while ( ( $f = readdir( $dir ) ) !== false ) {
				
				// Skip common files in each directory including the directory name
				if ( $f == '.' || $f == '..' || $f == '.svn' || $f == '._.DS_Store' ) {
					continue;	
				}
				
				if ( strpos( $f, $extension ) !== false ) {
					$files[] = $directory_name . '/' . $f;	
				}	
			}	
			
			closedir( $dir );	
		}
		
		return $files;
	}
	
	// Get the Contents of a specific file, and return it
	function load_file( $file_name ) {
		$contents = '';
		
		$f = @fopen( $file_name, 'rb' ); //@
		if ( $f ) :
			while ( !feof( $f ) ) :
				$new_contents = fread( $f, 8192 );
				$contents = $contents . $new_contents;	
			endwhile;
			
			fclose( $f );
			return $contents;
		else :
			return false;
		endif;
		
	}
	
	//Generate plugin list and add the plugins as options in OCMX
	function plugin_list() {
		global $mobile_theme_options; 
	
		$mobile_theme_options["plugin_options"] = array();
		 
		$php_files = $this->list_files( WP_PLUGIN_DIR, "php" );
				
		foreach( $php_files as $plugin_file ) :
			$path_info = explode( '/', $plugin_file );	
			
			if ( count( $path_info ) > 2 ) :	
				$plugin_slug = $path_info[1];
				
				if ( in_array( $plugin_slug, $this->plugin_whitelist ) ) :
					continue;	
				endif;
					
				$plugin_file_path = WP_PLUGIN_DIR . $plugin_file;

				if ( in_array(substr($plugin_file, 1, (strlen($plugin_file))), get_option("active_plugins") ) && !isset( $this->plugin_hooks[$plugin_slug]) ) :
					$formname = "mobile_disable_" . str_replace( '-', '_', $plugin_slug );
					array_unshift($mobile_theme_options["plugins"][0]["sub_elements"], array("label" => $this->get_friendly_plugin_name($plugin_slug), "description" => "", "name" => $formname, "default" => "", "id" => $plugin_slug, "input_type" => "checkbox"));
					$this->plugin_hooks[ $plugin_slug ] = 1;
				endif;
			endif;
		endforeach;
	}

	//Find the real name of the plugin, including caps, spaces etc.
	function get_friendly_plugin_name( $name ) {
		$plugin_file = WP_PLUGIN_DIR . '/' . $name . '/' . $name . '.php';
		if ( file_exists( $plugin_file ) ) {
			$contents = $this->load_file( $plugin_file );
			if ( $contents ) {
				if ( preg_match( "#Plugin Name: (.*)\n#", $contents, $matches ) ) {
					return $matches[1];	
				}	
			}
		}
		
		$all_files = $this->list_files_in_dir( WP_PLUGIN_DIR . '/' . $name, '.php' );
		if ( $all_files ) {
			foreach( $all_files as $some_file ) {
				if ( file_exists( $some_file ) ) {
					$contents = $this->load_file( $some_file );
					if ( $contents ) {
						if ( preg_match( "#Plugin Name: (.*)\n#", $contents, $matches ) ) {
							return $matches[1];	
						}	
					}
				}				
			}	
		}
		
		return str_replace( '_' , ' ', $name );
	}
	
	function disable_plugins() {
		global $mobile_theme_options;
		$plugin_to_disable = $mobile_theme_options["plugins"][0]["sub_elements"];

		foreach($plugin_to_disable as $plugins) :
			$option = $plugins["name"];
			$plugin_file = $plugins["id"];
			$folder = WP_PLUGIN_DIR . "/" . $plugin_file;
			
			if(get_option($option) == "false" || in_array( $plugin_file, $this->plugin_whitelist ) ) :		
				$fetch_the_hooks = $this->load_plugin_hooks($folder);
				
				foreach( $fetch_the_hooks as $name => $hook_info ) :
					if ( count( $hook_info->filters ) ) {
						foreach( $hook_info->filters as $hooks ) :
							if ( $hooks->priority ) {
								remove_filter( $hooks->hook, $hooks->hook_function, $hooks->priority );
							} else { 
								remove_filter( $hooks->hook, $hooks->hook_function );	
							}
						endforeach;
					}
					
					if ( count( $hook_info->actions ) ) {
						foreach( $hook_info->actions as $hooks ) :
							if ( $hooks->priority ) {
								remove_action( $hooks->hook, $hooks->hook_function, $hooks->priority );
							} else {
								remove_action( $hooks->hook, $hooks->hook_function );
							}
						endforeach;
					}
				endforeach;
			endif; 
		endforeach;
	}
		
		//Find the hooks for the plugins
		function load_plugin_hooks( $folder =  WP_PLUGIN_DIR) {
			
			$php_files = $this->list_files( $folder, "php" );
			
			foreach( $php_files as $plugin_file ) {
				$path_info = explode( '/', $plugin_file );
				
				if ( count( $path_info ) > 0 ) {
						
					$plugin_slug = $path_info[1];
					if ( in_array( $plugin_slug, $this->plugin_whitelist ) ) :
						continue;	
					endif;
					
					$plugin_file_path = $folder . $plugin_file;
					
					$contents = $this->load_file( $plugin_file_path );
					
					if ( !isset( $this->plugin_hooks[ $plugin_slug ] ) ) :
						$this->plugin_hooks[ $plugin_slug ] = new stdClass;
					endif;
					
					// Default actions
					if ( preg_match_all( "#add_action\([ ]*[\'\"]+(.*)[\'\"]+,[ ]*[\'\"]+(.*)[\'\"]+[ ]*(\s*[,]\s*+(.*))*\)\s*;#iU", $contents, $matches ) ) {
						for( $i = 0; $i < count( $matches[0] ); $i++ ) {						
							if ( strpos( $matches[2][$i], ' ' ) === false ) {
								$info = new stdClass;
								$info->hook = $matches[1][$i];
								$info->hook_function = $matches[2][$i];
								
								if ( isset( $matches[4][$i] ) && $matches[4][$i] > 0 ) {
									$info->priority = $matches[4][$i];   
								} else {
									$info->priority = false;   
								}
								
								$this->plugin_hooks[ $plugin_slug ]->actions[] = $info;
								
							}
						}
					}
					
					// Default filters
					if ( preg_match_all( "#add_filter\([ ]*[\'\"]+(.*)[\'\"]+,[ ]*[\'\"]+(.*)[\'\"]+[ ]*(\s*[,]\s*+(.*))*\)\s*;#iU", $contents, $matches ) ) {
						for( $i = 0; $i < count( $matches[0] ); $i++ ) {
							if ( strpos( $matches[2][$i], ' ' ) === false ) {
								$info = new stdClass;
								$info->hook = $matches[1][$i];
								$info->hook_function = $matches[2][$i];
								
								if ( isset( $matches[4][$i] ) && $matches[4][$i] > 0 ) {
									$info->priority = $matches[4][$i];   
								} else {
									$info->priority = false;   
								}								
								
								$this->plugin_hooks[ $plugin_slug ]->filters[] = $info;
								
							}
						}
					}
				}
			}
			return $this->plugin_hooks;
		}
	
}; ?>
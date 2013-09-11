<?php class ocmx_mobile_include_folder
	{
		function trawl_folder($folder)
			{
				$folder = OCMXMOBILEDIR.$folder;
				if ($handler = opendir($folder)) :
					while (false !== ($file = readdir($handler))) :
						if ($file !== "." && $file !== ".." && strpos($file, ".php")) :
							include_once ($folder.$file);
						endif;
					endwhile;
					closedir($handler);
				endif;
			}
	}
?>
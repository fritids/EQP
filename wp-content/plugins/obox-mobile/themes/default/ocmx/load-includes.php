<?php 
//Include all the OCMX files
foreach($include_folders as $inc_folder) :
	$include_folders = new include_folder();
	$folder = TEMPLATEPATH.$inc_folder;
	$include_folders->trawl_folder($folder);
endforeach;
?>
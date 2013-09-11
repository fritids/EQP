<?php
require_once '../wp-blog-header.php';

$users = $wpdb->get_results("SELECT umeta_id, meta_value FROM wp_usermeta WHERE meta_key = 'twitter'");
$consola=true;

foreach ($users AS $utd){
                echo "ok"."\n";
                $replace = str_replace("www.", "", $utd->meta_value);                
		$wpdb->query("UPDATE wp_usermeta SET meta_value='$replace' WHERE umeta_id = $utd->umeta_id");
}
exit;?>

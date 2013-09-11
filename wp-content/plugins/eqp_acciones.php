<?php
/*
Plugin Name: Acciones Quinto Poder
Plugin URI: http://ida.cl
Description: Desarrollo de funciones de acciones para wordpress
Version: 1.0
Author: Fernando Silva
Author URI: http://ida.cl
License: Open Source
*/

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;

if ($wpdb->has_cap('collation')) {
    if(!empty($wpdb->charset)) { $charset_collate = " DEFAULT CHARACTER SET $wpdb->charset"; }
    if(!empty($wpdb->collate)) { $charset_collate .= " COLLATE $wpdb->collate"; }
}

$table_name = $wpdb->prefix.'acciones';
if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'")) {
    $sql = "CREATE TABLE " . $table_name . " (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL DEFAULT '0',
            post_id bigint(20) NOT NULL DEFAULT '0',
            email VARCHAR(255) NOT NULL DEFAULT '',
            nombre VARCHAR(255) NOT NULL DEFAULT '',
            rut VARCHAR(255) NOT NULL DEFAULT '',
            profesion VARCHAR(255) NOT NULL DEFAULT '',
            institucion VARCHAR(255) NOT NULL DEFAULT '',
            UNIQUE KEY id (id)
    ) ".$charset_collate.";";
    dbDelta($sql);
}
if (!isset($wpdb->acciones)) {
    $wpdb->acciones = $wpdb->prefix . 'acciones';
}

?>

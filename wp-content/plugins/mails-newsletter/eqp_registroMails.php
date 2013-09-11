<?php
/*
Plugin Name: Mails Para NewsLetter
Plugin URI: http://ida.cl
Description: Desarrollo de funciones de Newsletter para wordpress
Version: 1.0
Author: Fernando Silva
Author URI: http://ida.cl
License: Open Source
*/
?>
<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
global $wpdb;

if ($wpdb->has_cap('collation')) {
    if(!empty($wpdb->charset)) { $charset_collate = " DEFAULT CHARACTER SET $wpdb->charset"; }
    if(!empty($wpdb->collate)) { $charset_collate .= " COLLATE $wpdb->collate"; }
}

$table_name = $wpdb->prefix.'newsletter';
if(!$wpdb->get_var("SHOW TABLES LIKE '".$table_name."'")) {
    $sql = "CREATE TABLE " . $table_name . " (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL DEFAULT '0',
            email VARCHAR(255) NOT NULL DEFAULT '',
            nombre VARCHAR(255) NOT NULL DEFAULT '',
            UNIQUE KEY id (id)
    ) ".$charset_collate.";";
    dbDelta($sql);
}
if (!isset($wpdb->newsletter)) {
    $wpdb->newsletter = $wpdb->prefix . 'newsletter';
}



function newsAjax(){
    global $wpdb;
    
    $email = $_POST['email'];
    
    $exist = $wpdb->get_var("SELECT id FROM $wpdb->newsletter WHERE email = '$email'");
    
    if( !$exist ){
        if( email_exists($email) ) {
            $usuario = get_user_by('email', $email);
            $nombre = $usuario->first_name .' '. $usuario->last_name;
            $wpdb->insert( $wpdb->newsletter, array(
                'user_id' => $usuario->ID,
                'email' => $usuario->user_email,
                'nombre' => $nombre
            ));
        }
        else {
            $wpdb->insert( $wpdb->newsletter, array(
                'user_id' => 0,
                'email' => $email,
                'nombre' => ''
            ));
        }
        die('gracias');
    }
    else {
        die('repetido');
    }
}

function getNewsletterForm() {           
    $out = '<form id="newsletter-subscription" action="" method="post">';
    $out .= '<input type="email" name="susbcribeEmail" placeholder="ejemplo@email.com" required>';
    $out .= '<input class="ganalytics" data-ga-category="Newsletter" data-ga_action="Boletin" data-ga_opt_label="Btnsuscribe" type="submit" id="suscribir" value="Suscribir">';
    $out .= '</form>';
    return $out;
}


function newsLetterPage_content(){
    global $wpdb;
    
    $results = $wpdb->get_results("SELECT * FROM $wpdb->newsletter");

    $out = '<div id="newsletter-page-wrapper" >';
    $out .= '<h1>Registro de Suscripciones<br>El Quinto Poder</h1>';
    $out .= '<div id="emailsTable-holder" >';
    $out .= '<a href="/wp-admin/admin.php?page=sucriptions-menu&emailngdatacsv=true" id="ExportData" title="Exportar Datos">Exportar Datos</a>';
    $out .= '<table id="emailsTable">';
    $out .= '<tr>';
    $out .= '<th>#</th>';
    $out .= '<th>E-mail</th>';
    $out .= '<th>Nombre</th>';
    $out .= '</tr>';
    
    $count = 1;
    foreach ((array)$results as $result) {
        $out .= '<tr>';
        $out .= '<td>'. $count .'</td>';
        $out .= '<td>'. $result->email .'</td>';
        $out .= '<td>'. $result->nombre .'</td>';
        $out .= '</tr>';
        $count++;
    }
    
    $out .= '</table>';
    $out .= '</div>';
    $out .= '</div>';
    
    echo $out;
}


function csvdata(){
    global $wpdb;
     $results = $wpdb->get_results("SELECT * FROM $wpdb->newsletter");
     $out = "";
         foreach ((array)$results as $result) {
             if (!$result->nombre) $result->nombre = " ";
            $out .=  $result->nombre . "," . $result->email . "\n" ;
        }
    return $out;
}

if($_GET["emailngdatacsv"]=="true"){
    header("Content-Type: application/csv") ; 
    header('Content-Disposition: attachment; filename="emailing.csv"');    
    echo csvdata();
    exit;
}

function newsLetterPage() {
	add_menu_page( 'Suscripciones', 'Suscripciones', 'manage_options', 'sucriptions-menu', 'newsLetterPage_content' );
}

function includeStyle() {
    if (is_admin()) {
        wp_deregister_style( 'newsLetterStyle' );
        wp_register_style( 'newsLetterStyle', plugins_url( 'newsletterStyle.css' , __FILE__ ));
        wp_enqueue_style( 'newsLetterStyle' );
    }
}
function includeNewsJs() {
    if (!is_admin()) {
        wp_deregister_script( 'newsletterJs' );
        wp_register_script( 'newsletterJs', plugins_url( 'newsletter.js' , __FILE__ ));
        wp_enqueue_script( 'newsletterJs' );
    }
}
function includeJquery() {
    if (!is_admin()) {
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
        wp_enqueue_script( 'jquery' );
    }
}



//////////////////////////////////////////////////////////////////////////////// Actions, Filters and Stuff

add_action('wp_enqueue_scripts', 'includeJquery');
add_action('wp_enqueue_scripts', 'includeNewsJs');
add_action('admin_print_styles', 'includeStyle');
add_action('admin_menu', 'newsLetterPage');

add_action('wp_ajax_newsAjax', 'newsAjax');
add_action('wp_ajax_nopriv_newsAjax', 'newsAjax');


?>
#!/usr/bin/php
<?php
require_once '../wp-blog-header.php';

$ultima_lista = get_option('ultima_lista');

$ahora = date("Y-m-d H:i:s");
update_option('ultima_lista', $ahora);

$usertodelete = $wpdb->get_results("SELECT user_id FROM wp_usermeta WHERE meta_value = '18' AND  meta_key = 'last_name' LIMIT 9000");

foreach ($usertodelete AS $utd){
		$wpdb->query("DELETE FROM wp_users WHERE ID = $utd->user_id");
		$wpdb->query("DELETE FROM wp_usermeta WHERE user_id = $utd->user_id");
}

$userList = $wpdb->get_results("
    SELECT A.meta_value as nombre, B.meta_value as apellido, C.meta_value as status, user_email 
    FROM wp_users 
    JOIN wp_usermeta A 
    JOIN wp_usermeta B 
    JOIN wp_usermeta C
    ON (A.user_id = ID AND ID = B.user_id AND C.user_id = ID) 
    WHERE A.meta_key = 'first_name' 
    AND  B.meta_key = 'last_name' 
    AND user_email NOT LIKE '%@twitter.com'
    AND C.meta_key = 'newsletter_suscriber'
    AND user_registered > '$ultima_lista' 
    GROUP BY user_email 
    ORDER BY user_registered DESC
    LIMIT 0, 9000
");

$suscriberList = $wpdb->get_results("
    SELECT email, nombre
    FROM wp_newsletter
    WHERE fecha_registro > '$ultima_lista' 
    ORDER BY fecha_registro DESC
");

$fop = fopen( ABSPATH . 'extra/usuarios.txt', 'w');

foreach( (array)$userList as $userObj ){
   fwrite($fop, $userObj->nombre. " " .  $userObj->apellido . "," .  $userObj->user_email ."\r\n" );
}
foreach( (array)$suscriberList as $suscriber ){
   fwrite($fop, $suscriber->nombre. "," .  $suscriber->email ."\r\n" );
}

fclose($fop);

$para   = 'contacto@ida.cl, fernando@ida.cl, eabbagliati@fdd.cl';
$titulo = 'Nueva lista de usuarios';
$mensaje = 'La lista de usuarios ha sido actualizada en http://www.elquintopoder.cl/extra/usuarios.txt';
$cabeceras = 'From: cronwordpress@elquintopoder.cl' . "\r\n" .
    'Reply-To: no-reply@elquintopoder.cl' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($para, $titulo, $mensaje, $cabeceras);
exit;

?>

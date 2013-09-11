<?php
/*
Template Name: url
*/

if( $_GET['newUser'] == 'true' && !is_user_logged_in() && !$_POST ) {
        
        $userID = crearUsuario($_GET);
        
        $claves = array_flip(array_merge(range('a','z'),range('A','Z'),range(0,9)));
        $password = implode("",array_rand($claves, 8));
        wp_redirect(home_url() . '/perfil-de-usuario/?var='.$password.'&userid='.$userID); exit;
    }else{
       wp_redirect(home_url()); exit;  
    }
?>
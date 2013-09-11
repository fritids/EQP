<?php

function newsAjax(){
    global $wpdb;
    
    $eMail = $_POST['email'];
    
    $exist = $wpdb->get_var("SELECT * FROM $wpdb->newsletter WHERE email='$eMail'");
    
    if(!$exist){
        $userAr = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_email = '$eMail'");
        if(!empty($userAr)) {
            $user = $userAr[0];
            $wpdb->insert( $wpdb->acciones, array(
                'user_id' => $user->ID,
                'email' => $user->user_email,
                'nombre' => $user->first_name .' '. $user->last_name
            ));
        }
        else {
            $wpdb->insert( $wpdb->acciones, array(
                'user_id' => 0,
                'email' => $eMail,
                'nombre' => ''
            ));
        }
        die('gracias');
    }
    else {
        die('repetido');
    }
}

<?php

/*
  Plugin Name: zocial login IDA
  Plugin URI: http://ida.cl
  Description: Login social by IDA
  Version: 1.0
  Author: Ideas Digitales Aplicadas
  Author URI: http://ida.cl
  License: Open Source
 */


require_once dirname(__FILE__) . '/clases/class-twitter.php';
require_once dirname(__FILE__) . '/clases/class-google.php';
require_once dirname(__FILE__) . '/clases/class-facebook.php';
session_start();

class allogin {

    function allogin() {
        
    }

    function custom_avatar($avatar, $mixed, $size, $default, $alt = '') {
        //Chosen user
        $user_id = null;

        //Check if we have an user identifier
        if (is_numeric($mixed)) {
            if ($mixed > 0) {
                $user_id = $mixed;
            }
        }
        //Check if we have an user object
        else if (is_object($mixed)) {
            if (property_exists($mixed, 'user_id') AND is_numeric($mixed->user_id)) {
                $user_id = $mixed->user_id;
            }
        }

        if (!empty($user_id)) {
            //Read Thumbnail
            if (($user_thumbnail = get_user_meta($user_id, 'oa_social_login_user_thumbnail', true)) != false) {
                if (strlen(trim($user_thumbnail)) > 0) {
                    return '<img alt="' . esc_attr($alt) . '" src="' . $user_thumbnail . '" class="avatar avatar-social-login avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
                }
            } elseif (($user_thumbnail = get_user_meta($user_id, 'avatar', true)) != false) {
                return '<img alt="' . esc_attr($alt) . '" src="' . $user_thumbnail . '" class="avatar avatar-social-login avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
            }
        }


        //Default
        return $avatar;
    }

    function createUser($info) {
        global $wpdb;

        $newUser = wp_create_user(urldecode($info['usrLogin']), urldecode($info['usrPassword']), urldecode($info['mail']));
        if (!is_wp_error($newUser)) {

            $theuser = get_userdata($newUser);

            if ($info['website']) {
                wp_update_user(array('ID' => $theuser->ID, 'user_url' => $info['website']));
            }
            if ($info['apellido']) {
                update_user_meta($theuser->ID, 'last_name', urldecode($info['apellido']));
            }
            if ($info['nombre']) {
                update_user_meta($theuser->ID, 'first_name', urldecode($info['nombre']));
            }
            if ($info['tipocuenta']) {
                update_user_meta($theuser->ID, 'tipoCuenta', urldecode($info['tipocuenta']));
            }
            if ($info['avatar']) {
                update_user_meta($theuser->ID, 'avatar', urldecode($info['avatar']));
            }


            $creds = array(
                'user_login' => $theuser->user_login,
                'user_password' => urldecode($info['usrPassword']),
                'remember' => true
            );
            $usercheck = wp_signon($creds, false);
        }
    }

    function updateUserAvatar($info) {
        global $wpdb;
        if ($info['apellido']) {
            update_user_meta($info['id'], 'last_name', urldecode($info['apellido']));
        }
        if ($info['nombre']) {
            update_user_meta($info['id'], 'first_name', urldecode($info['nombre']));
        }
        if ($info['avatar']) {
            update_user_meta($info['id'], 'avatar', urldecode($info['avatar']));
        }
        if ($info['website']) {
            wp_update_user(array('ID' => $info['id'], 'user_url' => $info['website']));
        }
    }

    function debug($obj) {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    }

}
add_filter('get_avatar', array('allogin', 'custom_avatar'), 10, 5);
$allogin = new allogin();

$alloginTW = new twitter();
$alloginFB = new facebook();
$alloginGP = new google();
?>
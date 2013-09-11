<?php

class facebook extends allogin {

    function __construct() {
        global $wpdb;

        $app_id = "264596836884988";
        $app_secret = "30001e9faa67e4bbc9445ebf8e3495fc";
        $my_url = "http://www.elquintopoder.cl/";
        $code = $_REQUEST["code"];

        if (isset($_REQUEST['loginfacebook'])) {
            if (empty($code)) {
                $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
                $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
                        . $app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
                        . $_SESSION['state'] . "&scope=email&display=popup";
                echo("<script> top.location.href='" . $dialog_url . "'</script>");
            }
        }

        if ($_SESSION['state'] && $_REQUEST['state']) {
            $token_url = "https://graph.facebook.com/oauth/access_token?"
                    . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                    . "&client_secret=" . $app_secret . "&code=" . $code;

            $response = file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);

            $graph_url = "https://graph.facebook.com/me?access_token="
                    . $params['access_token'];

            $resp = json_decode(file_get_contents($graph_url));

            require_once(ABSPATH . WPINC . '/registration.php');
            require_once(ABSPATH . WPINC . '/pluggable.php');
            
            $datos_user = array(
                'usrLogin' => $resp->username,
                'usrPassword' => $resp->id,
                'mail' => $resp->username . "@facebook.com",
                'avatar' => 'http://graph.facebook.com/' . $resp->username . '/picture',
                'nombre' => $resp->first_name,
                'apellido' => $resp->last_name,
                'tipocuenta' => 'facebook',
                'website' => "http://facebook.com/$resp->username"
            );

            $user_id = $wpdb->get_var("SELECT ID FROM wp_users WHERE user_email='$resp->email'") ? $wpdb->get_var("SELECT ID FROM wp_users WHERE user_email='$resp->email'") : $wpdb->get_var("SELECT ID FROM wp_users WHERE user_login='$resp->username'");

            if (is_numeric($user_id) && $user_id > 0) {
                wp_set_auth_cookie($user_id, true);
                $datos_user["id"] = $user_id;
                $this->updateUserAvatar($datos_user);
            }

            if (is_wp_error(get_userdata($user_id)) || !is_numeric($user_id)) {
                $this->createUser($datos_user);
            }
            header("Location:/?flush=cache");
            exit;
        } else {
            //  echo("The state does not match. You may be a victim of CSRF.");
        }
    }

}

?>

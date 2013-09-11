<?php

class twitter extends allogin {

    function twitter() {
        global $wpdb;
        
        if( ! class_exists('TwitterOAuth') ){ require_once 'twitter/twitteroauth.php'; }
        
        $access_data = array(
            'consumer_key' => 'SMaFAjGnApTmzzoOiKuoQ',
            'consumer_secret' => 'fev3cEMYV1JANsD6QkBWqybuGtxfjOLHSLe3XKCdwI',
            'access_token' => '8346872-s4ObCuTIvaWefmX9iNRGZF0Yl6BpO2iEPGIZXfPoDo',
            'acess_token_secret' => 'L2vdpq2L0IAw2WDY4X4lhr8H6ZuCgxoVtbqSRph60',
            'oauth_callback' => 'http://'. $_SERVER['HTTP_HOST'] .'/?flush=cache'
        );
        
        if( $_REQUEST['oauth_token'] || $_REQUEST['oauth_verifier'] ){
            $connection = new TwitterOAuth($access_data['consumer_key'], $access_data['consumer_secret'], $_REQUEST['oauth_token'], $_REQUEST['oauth_verifier']);
            $connection->ssl_verifypeer = TRUE;
            $connection->host = "https://api.twitter.com/1.1/";
            
            $token_credentials = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            $connection = new TwitterOAuth($access_data['consumer_key'], $access_data['consumer_secret'], $token_credentials['oauth_token'], $token_credentials['oauth_token_secret']);
            
            $account = $connection->get('account/verify_credentials');
            
//            $this->debug( $token_credentials );
//            $this->debug( $account );
            
            
            $datos_user = array(
                'usrLogin' => $account->screen_name,
                'usrPassword' => $account->id,
                'mail' => $account->screen_name . "@twitter.com",
                'avatar' => $account->profile_image_url,
                'tipocuenta' => 'twitter',
                'website' => "http://twitter.com/$account->screen_name"
            );

            require_once(ABSPATH . WPINC . '/registration.php');
            require_once(ABSPATH . WPINC . '/pluggable.php');
            $user_id = $wpdb->get_var("SELECT ID FROM wp_users WHERE user_login='$account->screen_name'");
            
            if ($user_id && is_numeric($user_id) && $user_id > 0) {
                wp_set_auth_cookie($user_id, true);
                $datos_user["id"] = $user_id;
                $this->updateUserAvatar($datos_user);
            }
            
            elseif (is_wp_error(get_userdata($user_id)) || !is_numeric($user_id)) {
                $nya = explode(" ", $account->name);

                $datos_user["nombre"] = $nya[0] ? $nya[0] : $account->screen_name;
                $datos_user["apellido"] = $nya[1];

                if ($datos_user["apellido"] == "") {
                    unset($datos_user["appellido"]);
                    $datos_user["nombre"] = $account->name ? $account->name : $account->screen_name;
                }

                $this->createUser($datos_user);
            }
        }
        
        else if( $_REQUEST['authenticate'] ){
            $connection = new TwitterOAuth($access_data['consumer_key'], $access_data['consumer_secret']);
            $connection->ssl_verifypeer = TRUE;
            $connection->host = "https://api.twitter.com/1.1/";
            
            $temporary_credentials = $connection->getRequestToken( $access_data['oauth_callback'] );
            $redirect_url = $connection->getAuthorizeURL($temporary_credentials); // Use Sign in with Twitter
            $this->debug($redirect_url);
            header("Location:" . $redirect_url);
            exit;
        }
        
        
        
        
        
//        $tmhOAuth = new tmhOAuth(array(
//                    'consumer_key' => 'SMaFAjGnApTmzzoOiKuoQ',
//                    'consumer_secret' => 'fev3cEMYV1JANsD6QkBWqybuGtxfjOLHSLe3XKCdwI',
//                ));
//        // already got some credentials stored?
//        if (isset($_SESSION['access_token']) && $_REQUEST["flush"] == "cache") {
//
//            $tmhOAuth->config['user_token'] = $_SESSION['access_token']['oauth_token'];
//            $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];
//
//            $code = $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
//            
//            if ($code == 200) {
//                $resp = json_decode($tmhOAuth->response['response']);
//                
//
//                $datos_user = array(
//                    'usrLogin' => $resp->screen_name,
//                    'usrPassword' => $resp->id,
//                    'mail' => $resp->screen_name . "@twitter.com",
//                    'avatar' => $resp->profile_image_url,
//                    'tipocuenta' => 'twitter',
//                    'website' => "http://twitter.com/$resp->screen_name"
//                );
//                require_once(ABSPATH . WPINC . '/registration.php');
//                require_once(ABSPATH . WPINC . '/pluggable.php');
//                $user_id = $wpdb->get_var("SELECT ID FROM wp_users WHERE user_login='$resp->screen_name'");
//                if (is_numeric($user_id) && $user_id > 0) {
//                    wp_set_auth_cookie($user_id, true);
//                    $datos_user["id"] = $user_id;
//                    $this->updateUserAvatar($datos_user);
//                }
//
//                if (is_wp_error(get_userdata($user_id)) || !is_numeric($user_id)) {
//
//
//                    $nya = explode(" ", $resp->name);
//
//                    $datos_user["nombre"] = $nya[0] ? $nya[0] : $resp->screen_name;
//                    $datos_user["apellido"] = $nya[1];
//
//                    if ($datos_user["apellido"] == "") {
//                        unset($datos_user["appellido"]);
//                        $datos_user["nombre"] = $resp->name ? $resp->name : $resp->screen_name;
//                    }
//                                        
//                    $this->createUser($datos_user);
//                }
//            } else {
//                $this->outputError($tmhOAuth);
//            }
//            // we're being called back by Twitter
//        } elseif (isset($_REQUEST['oauth_verifier'])) {
//
//            $tmhOAuth->config['user_token'] = $_SESSION['oauth']['oauth_token'];
//            $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
//
//
//            $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
//                'oauth_verifier' => $_REQUEST['oauth_verifier']
//                    ));
//            if ($code == 200) {
//                $_SESSION['access_token'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
//                unset($_SESSION['oauth']);
//                header("Location: /?flush=cache");
//                exit;
//            } else {
//                $this->outputError($tmhOAuth);
//            }
//            // start the OAuth dance
//        } elseif (isset($_REQUEST['authenticate']) || isset($_REQUEST['authorize'])) {
//            $callback = isset($_REQUEST['oob']) ? 'oob' : $here;
//
//            $params = array(
//                'oauth_callback' => $callback
//            );
//
//            if (isset($_REQUEST['force_write'])) :
//                $params['x_auth_access_type'] = 'write';
//            elseif (isset($_REQUEST['force_read'])) :
//                $params['x_auth_access_type'] = 'read';
//            endif;
//
//            $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), $params);
//            
//            if ($code == 200) {
//                $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
//                $method = isset($_REQUEST['authenticate']) ? 'authenticate' : 'authorize';
//                $force = isset($_REQUEST['force']) ? '&force_login=1' : '';
//                $authurl = $tmhOAuth->url("oauth/{$method}", '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}";
//                header("Location:" . $authurl);
//                exit;
//            } else {
//                $this->outputError($tmhOAuth);
//            }
//        }
    }

    function outputError($tmhOAuth) {
        echo 'Error: ' . $tmhOAuth->response['response'] . PHP_EOL;
        tmhUtilities::pr($tmhOAuth);
    }

}

?>
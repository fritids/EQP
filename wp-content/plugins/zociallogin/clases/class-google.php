<?php

class google extends allogin {

    public function __construct() {
        global $wpdb;
        
        require_once dirname(__FILE__) . '/google/apiClient.php';
        require_once dirname(__FILE__) . '/google/contrib/apiOauth2Service.php';
        
        $client = new apiClient();
        $client->setApplicationName('Google+ PHP Starter Application');
        $client->setClientId('937046899725.apps.googleusercontent.com');
        $client->setClientSecret('7ZNuj2AXJMwfQjiABCzrI3Ml');
        $client->setRedirectUri('http://www.elquintopoder.cl/');
        $client->setDeveloperKey('AIzaSyBQ7j71dp_texiQNwurManZ5lqIT-k1tRs');
        $client->setScopes(array('https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'));
        $client->setApprovalPrompt("auto");
        // we don't need to access a Google API when the user is not present at the browser
//        $client->setAccessType("online");

        $plus = new apiOauth2Service($client);

        if (isset($_REQUEST['logingoogle'])) {
            $authUrl = $client->createAuthUrl();
            $_SESSION['gstate'] = md5(uniqid(rand(), TRUE)); //CSRF protection
            header("Location:" . $authUrl);
            exit;
        }

        if (isset($_REQUEST['code'])) {
            $client->authenticate();
            $_SESSION['token'] = $client->getAccessToken();
            header("Location:/?gstate=" . $_SESSION['gstate']);
            exit;
        }

        if (isset($_SESSION['token'])) {
            $client->setAccessToken($_SESSION['token']);
        }

        if ($client->getAccessToken() && ($_SESSION['gstate'] && ($_SESSION['gstate'] === $_REQUEST['gstate']))) {
            require_once(ABSPATH . WPINC . '/registration.php');
            require_once(ABSPATH . WPINC . '/pluggable.php');
            $person = $plus->userinfo->get();
            $email = filter_var($person['email'], FILTER_SANITIZE_EMAIL);
            $img = filter_var($person['picture'], FILTER_VALIDATE_URL);
            $username = $person['name'];
            $nombre = $person['given_name'];
            $apellido = $person['family_name'];
            $id = $person['$id'];
            $website = filter_var($person['link'], FILTER_VALIDATE_URL);

            $datos_user = array(
                'usrLogin' => $username,
                'usrPassword' => $id,
                'mail' => $email,
                'avatar' => $img,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'tipocuenta' => 'google',
                'website' => $website
            );


            $user_id = $wpdb->get_var("SELECT ID FROM wp_users WHERE user_email='$email'");

            if (is_numeric($user_id) && $user_id > 0) {
                wp_set_auth_cookie($user_id, true);
                $datos_user["id"] = $user_id;
                $this->updateUserAvatar($datos_user);
            }

            if (is_wp_error(get_userdata($user_id)) || !is_numeric($user_id)) {


                $this->createUser($datos_user);
            }
            $_SESSION['token'] = $client->getAccessToken();
            header("Location:/?flush=cache");
            exit;
        }
    }

}

?>

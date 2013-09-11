<?php


function niceTime($time) {
    $delta = time() - $time;
    if ($delta < 60) {
        return 'menos de un minuto.';
    } else if ($delta < 120) {
        return 'hace un minuto atrás.';
    } else if ($delta < (45 * 60)) {
        return floor($delta / 60) . ' minutos.';
    } else if ($delta < (90 * 60)) {
        return 'hace una hora atras.';
    } else if ($delta < (24 * 60 * 60)) {
        return 'hace ' . floor($delta / 3600) . ' horas.';
    } else if ($delta < (48 * 60 * 60)) {
        return '1 día.';
    } else {
        return floor($delta / 86400) . ' días.';
    }
}

function get_facebook_cookie($app_id, $application_secret) {
    $args = array();
    parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
    ksort($args);
    $payload = '';
    foreach ($args as $key => $value) {
        if ($key != 'sig') {
            $payload .= $key . '=' . $value;
        }
    }
    if (md5($payload . $application_secret) != $args['sig']) {
        return null;
    }
    return $args;
}

function zona_social() {
    $cookie = get_facebook_cookie('162477490459612', 'a7e5da9f5656f598092fc3c7b36fbe88');
    echo "<div id='fb-root'></div>";
    echo '<div id="timeline">';
    echo '<div id="headsocial" class="clearfix">';
    echo "<h2>Comenta con nosotros</h2>";
    echo '<div id ="auth" class="clearfix">';
    if (!$cookie['uid']) {

        if ($_SESSION['status'] == 'verified') {
            echo '<a id="twitterLogout" href="?twitter=logout" title="Comparte con twitter">twitter logout</a>';
        } else {
            echo '<a id="twitterLogin" href="?twitter=login" title="Comparte con twitter">twitter login</a>';
        }
    }
    if ($_SESSION['status'] != 'verified') {
        echo ' <fb:login-button autologoutlink="true" perms="email,user_birthday,status_update,publish_stream,read_stream"></fb:login-button>';
    }
    echo '</div></div>';
    if ($_SESSION['status'] == 'verified'):
        echo '<form action="" method="post" id="commentform"><input type="hidden" id="cuenta" name="cuenta" value="twitter" /><textarea name="comentbox" id="commentbox" class="evtc">Algo que comentar?</textarea><span class="comentri"></span> <input type="submit" value="comentar" name="comentar" id="comentar" class="evtc"></form>';
    endif;
    if ($cookie['uid']):
        echo '<form action="" method="post" id="commentform" class="fb"><input type="hidden" id="cuenta" name="cuenta" value="facebook" /><textarea name="comentbox" id="commentbox" class="evtc">Algo que comentar?</textarea><span class="comentri"></span> <input type="submit" value="comentar" name="comentar" id="comentar" class="comentar"></form>';
    endif;
    echo '<span style="display:none" class="fb"><em class="comentar"></em></span>';
    echo '<div id="actualizar"><a href="#" id="refresh" class="evtc">Actualizar</a></div>';
    echo '<div id="crono" class="clearfix"><ul>';
    echo '</ul></div>';
    echo '</div>';
}

function getcurl($getURL, $formvars=false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $getURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($formvars != false)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formvars);
    $page = curl_exec($ch);
    if (!curl_errno($ch)) {
        $info = curl_getinfo($ch);
    }
    curl_close($ch);
    return $page;
}



?>

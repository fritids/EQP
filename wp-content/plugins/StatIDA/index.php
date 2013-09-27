<?php

/*
  Plugin Name: Plugin de estadísticas
  Plugin URI: http://ida.cl
  Description: Widgets de estadísticas de El Quinto Poder
  Version: 1.0
  Author: Ideas Digitales Aplicadas
  Author URI: http://ida.cl
  License: Open Source
 */

class statIDA {

    public function __construct() {
        add_action('admin_menu', array($this, 'addmenues'));
    }

    public function addmenues() {
        add_menu_page('Estadísticas', 'Estadísticas', 'manage_options', 'stats-ida', array($this, 'obtener_graficos'));
        add_action('admin_enqueue_scripts', array($this, 'statscript'));
    }

    public function obtener_graficos() {
        echo '<div id="estadisticas_ida"><h1>Centro de estadísticas EQP</h1> 
            <section class="graph_publicaciones" style="width:47%; float:left; margin: 30px 1%;"></section>
            <section class="graph_usuarios" style="width:47%; float:left; margin: 30px 1%;"></section>
            <section class="graph_comentarios" style="width:47%; float:left; margin: 30px 1%;"></section>
            <section class="graph_votos" style="width:47%; float:left; margin: 30px 1%;"></section>
            </div>';
    }

    public function statscript() {
        wp_deregister_script('highcharts');
        wp_register_script('highcharts', 'http://code.highcharts.com/highcharts.js');
        wp_enqueue_script('highcharts');
        wp_deregister_script('highchartsexport');
        wp_register_script('highchartsexport', 'http://code.highcharts.com/modules/exporting.js');
        wp_enqueue_script('highchartsexport');
        wp_deregister_script('statsida');
        wp_register_script('statsida', plugins_url('statsida.js', __FILE__));
        wp_enqueue_script('statsida');
    }

    function jsondata() {
        // pasandole fechas  con get
        global $wpdb;


        for ($hh = 11; $hh >= 0; $hh--):
            //MES PARA EL CALCULO   
            $mesnum = date("m", mktime(0, 0, 0, date("m") - $hh, 1, date("Y")));
            $mesnum = (int) $mesnum;
            //AÑO PARA EL CALCULO   
            $year = date("Y", mktime(0, 0, 0, date("m") - $hh, 1, date("Y")));
            $year = (int) $year;
            //MES PARA JSON   
            $mes = date("M", mktime(0, 0, 0, $mesnum, 1, $year));
            //PARA QUERY           
            $lastday = cal_days_in_month(CAL_GREGORIAN, $mesnum, $year);
            $mes_ini = date("Y-m-d H:i:s", mktime(0, 0, 0, $mesnum, 1, $year));
            $mes_fin = date("Y-m-d H:i:s", mktime(0, 0, 0, $mesnum, $lastday, $year));
            $menosmes = gmmktime(0, 0, 0, $mesnum, 1, $year);
            $masmes = gmmktime(0, 0, 0, $mesnum, $lastday, $year);

            if ($_GET["obt"] == "publicaciones") {
                $numberPosts = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
                $tojson["entradas"] = (int) $numberPosts;
                $numberAcciones = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_acciones' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
                $tojson["acciones"] = (int) $numberAcciones;
                $numberFotos = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_fotos' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
                $tojson["fotos"] = (int) $numberFotos;
                $numberVideos = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_videos' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
                $tojson["videos"] = (int) $numberVideos;
            } elseif ($_GET["obt"] == "usuarios") {
                $R_ActiveUsers = $wpdb->get_results("SELECT COUNT(ID) as cu, post_author FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ('post_acciones','post_fotos','post_videos','post') AND post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin' GROUP BY post_author");
                $cusers=1;
                foreach ($R_ActiveUsers as $cu){
                    $cusers++;
                }
                $tojson["usuarios_activos"] = (int) $cusers;
                
                $R_ActiveUsersComments = $wpdb->get_var("SELECT COUNT(comment_author) FROM $wpdb->comments WHERE comment_approved = '1' AND user_id != 0 AND comment_date >= '$mes_ini'  AND comment_date <= '$mes_fin'");
                $tojson["usuarios_comentado"] = (int) $R_ActiveUsersComments;

                $numberUsers = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE user_registered >= '$mes_ini'  AND user_registered <= '$mes_fin'");
                $tojson["usuarios_registrados"] = (int) $numberUsers;

//                $numberUsersTotal = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE 1         ");
//                $tojson["usuarios_total"] = (int) $numberUsersTotal;

                } elseif ($_GET["obt"] == "comentarios") {
                $numberComments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE $wpdb->comments.comment_approved = '1' AND $wpdb->comments.comment_date >= '$mes_ini' AND $wpdb->comments.comment_date <= '$mes_fin'");
                $tojson["comentarios"] = (int) $numberComments;

                $numberCommentsVote = $wpdb->get_var("SELECT COUNT(id) FROM wp_commentsvote WHERE vote = '1' AND voteTime >= $menosmes  AND voteTime <= $masmes         ");
                $tojson["comentarios_positivos"] = (int) $numberCommentsVote;

                $numberCommentsVote = $wpdb->get_var("SELECT COUNT(id) FROM wp_commentsvote WHERE vote = '-1' AND voteTime >= $menosmes  AND voteTime <= $masmes        ");
                $tojson["comentarios_negativos"] = (int) $numberCommentsVote;
            } elseif ($_GET["obt"] == "votos") {
                $numberAccionesVotosLogin = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE user_id != '0' AND $wpdb->acciones.fecha >= '$mes_ini'   AND $wpdb->acciones.fecha <= '$mes_fin'");
                $tojson["votos_acciones_login"] = (int) $numberAccionesVotosLogin;

                $numberAccionesVotosNoLogin = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE user_id = '0' AND $wpdb->acciones.fecha >= '$mes_ini'  AND $wpdb->acciones.fecha <= '$mes_fin'");
                $tojson["votos_acciones_sin_login"] = (int) $numberAccionesVotosNoLogin;

                $numberAccionesVotosTotal = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE $wpdb->acciones.fecha >= '$mes_ini' AND $wpdb->acciones.fecha <= '$mes_fin'         ");
                $tojson["total_votos_acciones"] = (int) $numberAccionesVotosTotal;
            } else {
                print json_encode(array("data" => "no valid "));
                exit;
            }
            $json_data[$mes] = $tojson;
        endfor;
        print json_encode($json_data);
        exit;
    }

    function gastatida() {
        require_once(WP_PLUGIN_DIR . '/google-analyticator/class.analytics.stats.php');
        # Create a new API object
        $api = new GoogleAnalyticsStats();
        # Get the current accounts accounts
        $login = $api->checkLogin();
        $accounts = $api->getSingleProfile();
        # Verify accounts exist
        if (count($accounts) <= 0)
            return 0;
        # Loop throught the account and return the current account
        foreach ($accounts AS $account) {
            # Check if the UID matches the selected UID
            if ($account['ga:webPropertyId'] == get_option('ga_uid')) {
                $api->setAccount($account['id']);
                break;
            }
        }


        for ($hh = 1; $hh >= 0; $hh--):
            //MES PARA EL CALCULO   
            $mesnum = date("m", mktime(0, 0, 0, date("m") - $hh, 1, date("Y")));
            $mesnum = (int) $mesnum;
            //AÑO PARA EL CALCULO   
            $year = date("Y", mktime(0, 0, 0, date("m") - $hh, 1, date("Y")));
            $year = (int) $year;
            //MES PARA JSON   
            $mes = date("M", mktime(0, 0, 0, $mesnum, 1, $year));
            //PARA QUERY           
            $lastday = cal_days_in_month(CAL_GREGORIAN, $mesnum, $year);            
            $desde = date("Y-m-d", mktime(0, 0, 0, $mesnum, 1, $year));
            $hasta = date("Y-m-d", mktime(0, 0, 0, $mesnum, $lastday, $year));
            $viewsAnalytics = $api->getMetrics('ga:socialActivities', $desde, $hasta, 'ga:socialActivityPost');
            echo "<pre>";
            print_r($viewsAnalytics);
            echo "</pre>";
        endfor;
        die(json_encode($aryview));
    }

}

$statIda = new statIDA();

function ajax_statIDA() {
    $statIda = new statIDA();
    $statIda->jsondata();
    exit;
}

add_action('wp_ajax_ajax_statIDA', 'ajax_statIDA');
add_action('wp_ajax_nopriv_ajax_statIDA', 'ajax_statIDA');

function gastatida() {
    $statIda = new statIDA();
    $statIda->gastatida();
    exit;
}

add_action('wp_ajax_gastatida', 'gastatida');
add_action('wp_ajax_nopriv_gastatida', 'gastatida');
?>

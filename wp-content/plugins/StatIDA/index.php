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
        echo '<div id="estadisticas_ida"><h1>Centro de estadísticas EQP</h1> <section class="graph" style="width:90%; height="500px; margin: 30px auto; "></section></div>';
    }

    public function statscript() {
        wp_deregister_script('highcharts');
        wp_register_script('highcharts', 'http://code.highcharts.com/highcharts.js');
        wp_enqueue_script('highcharts');
        wp_deregister_script('statsida');
        wp_register_script('statsida', plugins_url('statsida.js', __FILE__));
        wp_enqueue_script('statsida');
    }

    function jsondata() {
       // pasandole fechas  con get
    global $wpdb;
    
        
        for ($hh=11; $hh >=0; $hh-- ):
        
        $mesnum = date("m", mktime(0, 0, 0,  date("m") - $hh, 1, date("Y")) ); 
		$mesnum = (int) $mesnum;

        $year =  date("Y", mktime(0, 0, 0,  date("m") - $hh, 1, date("Y")) );         
        $year = (int)$year;

		
        $mes =  date("M", mktime(0, 0, 0,  $mesnum, 1, $year) ); 

        
        $lastday = cal_days_in_month(CAL_GREGORIAN, $mesnum, $year);
        $mes_ini =  date("Y-m-d H:i:s", mktime(0, 0, 0,  $mesnum, 1, $year) );
        $mes_fin =  date("Y-m-d H:i:s", mktime(0, 0, 0,  $mesnum, $lastday, $year) );
        $menosmes =  gmmktime(0, 0, 0,  $mesnum, 1, $year);
        $masmes =   gmmktime(0, 0, 0,  $mesnum, $lastday, $year);

//        $numberPosts = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
//        $tojson["entradas"] = (int) $numberPosts; 
        
//        $numberAcciones = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_acciones' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
//        $tojson["acciones"] = (int) $numberAcciones; 
        
//        $numberFotos = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_fotos' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
//        $tojson["fotos"] = (int) $numberFotos; 
        
//        $numberVideos = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post_videos' AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'");
//        $tojson["videos"] = (int) $numberVideos;         
        
        $R_ActiveUsers = $wpdb->get_results("SELECT COUNT(post_author) AS cu FROM $wpdb->posts WHERE post_status = 'publish' AND post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'  GROUP BY post_author");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
//        $tojson["usuarios_activos"] = count($ActiveUsersAry);   
        
        $R_ActiveUsersComments = $wpdb->get_results("SELECT COUNT(user_id) AS cu FROM $wpdb->comments WHERE $wpdb->comments.comment_approved = '1' AND user_id != 0 AND comment_date >= '$mes_ini'  AND comment_date <= '$mes_fin' GROUP BY user_id");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
//        $tojson["usuarios_comentado"] = count($ActiveUsersAryComm);          
        
        $numberUsers = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE  $wpdb->users.user_registered >= '$mes_ini'  AND $wpdb->users.user_registered <= '$mes_fin'");
//        $tojson["usuarios_registrados"] = (int) $numberUsers;
        
//        $numberUsersTotal = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users WHERE 1         ");
//        $tojson["usuarios_total"] = (int) $numberUsersTotal;
        
        
        $numberComments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE $wpdb->comments.comment_approved = '1' AND $wpdb->comments.comment_date >= '$mes_ini' AND $wpdb->comments.comment_date <= '$mes_fin'         ");
        $tojson["comentarios"] = (int) $numberComments;
        
        $numberCommentsVote = $wpdb->get_var("SELECT COUNT(id) FROM wp_commentsvote WHERE vote = '1' AND voteTime >= $menosmes  AND voteTime <= $masmes         ");
        $tojson["comentarios_positivos"] = (int) $numberCommentsVote;
        
        $numberCommentsVote = $wpdb->get_var("SELECT COUNT(id) FROM wp_commentsvote WHERE vote = '-1' AND voteTime >= $menosmes  AND voteTime <= $masmes        ");
        $tojson["comentarios_negativos"] = (int) $numberCommentsVote;
        
        $numberAccionesVotosLogin = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE user_id != '0' AND $wpdb->acciones.fecha >= '$mes_ini'   AND $wpdb->acciones.fecha <= '$mes_fin'          ");
//        $tojson["votos_acciones_login"] = (int) $numberAccionesVotosLogin;
        
        $numberAccionesVotosNoLogin = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE user_id = '0' AND $wpdb->acciones.fecha >= '$mes_ini'  AND $wpdb->acciones.fecha <= '$mes_fin'            ");
//        $tojson["votos_acciones_sin_login"] = (int) $numberAccionesVotosNoLogin;        
        
        $numberAccionesVotosTotal = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->acciones WHERE $wpdb->acciones.fecha >= '$mes_ini' AND $wpdb->acciones.fecha <= '$mes_fin'         ");
//        $tojson["total_votos_acciones"] = (int) $numberAccionesVotosTotal; 
        
        $tojson_tres_meses[$mes] = $tojson;
        endfor;
     
        print json_encode($tojson_tres_meses);
        exit;
    }

}

$statIda = new statIDA();


function ajax_statIDA(){
        $statIda = new statIDA();    
        $statIda->jsondata(); 
        exit;
}
add_action( 'wp_ajax_ajax_statIDA', 'ajax_statIDA' );
add_action( 'wp_ajax_nopriv_ajax_statIDA', 'ajax_statIDA' );

?>

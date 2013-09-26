<?php

/*
Plugin Name: Widgets de estadísticas
Plugin URI: http://ida.cl
Description: Widgets de estadísticas de El Quinto Poder
Version: 1.0
Author: Ideas Digitales Aplicadas
Author URI: http://ida.cl
License: Open Source
*/

function stats30Dias() {
    
	// Display whatever it is you want to show
    global $wpdb;
	echo "<ol>";
        $numberPosts = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Entradas : <strong>" .$numberPosts . "</strong></li>";
        $numberAcciones = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_acciones' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Acciones : <strong>" .$numberAcciones . "</strong></li>";
        $numberFotos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_fotos' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Fotos : <strong>" .$numberFotos . "</strong></li>";
        $numberVideos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_videos' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Videos : <strong>" .$numberVideos . "</strong></li>";
        $R_ActiveUsers = $wpdb->get_results("
            SELECT COUNT(post_author) AS cu
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
            GROUP BY post_author    
        ");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios Activos : <strong>" .count($ActiveUsersAry) . "</strong></li>";
        $R_ActiveUsersComments = $wpdb->get_results("
            SELECT COUNT(user_id) AS cu
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND user_id != 0     
            AND comment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
            GROUP BY user_id    
        ");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios con login que han comentado : <strong>" .count($ActiveUsersAryComm) . "</strong></li>";        
        $numberUsers = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE  $wpdb->users.user_registered >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Usuarios Registrados : <strong>" .$numberUsers . "</strong></li>";
        $numberUsersTotal = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE 1             
        ");
        echo "<li>Total usuarios: <strong>" .$numberUsersTotal . "</strong></li>";
        $menosmes =  time() - (30 * 24 * 60 * 60);
        $numberComments = $wpdb->get_var("
            SELECT COUNT(comment_ID) 
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND $wpdb->comments.comment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Comentarios : <strong>" .$numberComments . "</strong></li>";
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '1' 
            AND voteTime >= $menosmes             
        ");
        echo "<li>Comentarios +1 : <strong>" .$numberCommentsVote . "</strong></li>";
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '-1' 
            AND voteTime >= $menosmes             
        ");
        echo "<li>Comentarios -1 : <strong>" .$numberCommentsVote . "</strong></li>";        
        $numberAccionesVotosLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id != '0' 
            AND $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Adhesiones a acciones (con login) : <strong>" .$numberAccionesVotosLogin . "</strong></li>";        
        $numberAccionesVotosNoLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id = '0' 
            AND $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Adhesiones a acciones (sin login) : <strong>" .$numberAccionesVotosNoLogin . "</strong></li>"; 
        $numberAccionesVotosTotal = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE 
            $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        echo "<li>Total votos de acciones : <strong>" .$numberAccionesVotosTotal . "</strong></li>"; 
	echo "</ol>";
        echo '<p><a href="/wp-admin/?exportdayscsv=true">Descargar CSV</a></p>';
        
} 
function statsUltimoMes(){
    // Display whatever it is you want to show
    global $wpdb;
    $mes_ini =  date("Y-m-d H:i:s", mktime(0, 0, 0,  date("m"), 1, date("Y")) );
	echo "<ol>";
        $numberPosts = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post' 
            AND $wpdb->posts.post_date >= '$mes_ini'             
        ");
        echo "<li>Entradas : <strong>" .$numberPosts . "</strong></li>";
        
        $numberAcciones = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_acciones' 
            AND $wpdb->posts.post_date >= '$mes_ini'             
        ");
        echo "<li>Acciones : <strong>" .$numberAcciones . "</strong></li>";
        
        
        $numberFotos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_fotos' 
            AND $wpdb->posts.post_date >= '$mes_ini'            
        ");
        echo "<li>Fotos : <strong>" .$numberFotos . "</strong></li>";

        $numberVideos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_videos' 
            AND $wpdb->posts.post_date >= '$mes_ini'             
        ");
        echo "<li>Videos : <strong>" .$numberVideos . "</strong></li>";

        $R_ActiveUsers = $wpdb->get_results("
            SELECT COUNT(post_author) AS cu
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND post_date >= '$mes_ini' 
            GROUP BY post_author    
        ");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios Activos : <strong>" .count($ActiveUsersAry) . "</strong></li>";
        
        $R_ActiveUsersComments = $wpdb->get_results("
            SELECT COUNT(user_id) AS cu
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND user_id != 0     
            AND comment_date >= '$mes_ini'  
            GROUP BY user_id    
        ");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios con login que han comentado : <strong>" .count($ActiveUsersAryComm) . "</strong></li>";   
        
        $numberUsers = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE  $wpdb->users.user_registered >= '$mes_ini'             
        ");
        echo "<li>Usuarios Registrados : <strong>" .$numberUsers . "</strong></li>";
 
        $numberUsersTotal = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE 1             
        ");
        echo "<li>Total usuarios: <strong>" .$numberUsersTotal . "</strong></li>";
 
 
        $menosmes =   gmmktime(0, 0, 0,  date("m"), 1, date("Y"));
        $numberComments = $wpdb->get_var("
            SELECT COUNT(comment_ID) 
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND $wpdb->comments.comment_date >= '$mes_ini'             
        ");
        echo "<li>Comentarios : <strong>" .$numberComments . "</strong></li>";
          
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '1' 
            AND voteTime >= $menosmes             
        ");
        echo "<li>Comentarios +1 : <strong>" .$numberCommentsVote . "</strong></li>";
        
         $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '-1' 
            AND voteTime >= $menosmes             
        ");
        echo "<li>Comentarios -1 : <strong>" .$numberCommentsVote . "</strong></li>";        
                
        
        $numberAccionesVotosLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id != '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'            
        ");
        echo "<li>Adhesiones a acciones (con login) : <strong>" .$numberAccionesVotosLogin . "</strong></li>";        
        
        $numberAccionesVotosNoLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id = '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'             
        ");
        echo "<li>Adhesiones a acciones (sin login) : <strong>" .$numberAccionesVotosNoLogin . "</strong></li>"; 
        
        $numberAccionesVotosTotal = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE 
            $wpdb->acciones.fecha >= '$mes_ini'             
        ");
        echo "<li>Total votos de acciones : <strong>" .$numberAccionesVotosTotal . "</strong></li>"; 
	echo "</ol>";
        echo '<p><a href="/wp-admin/?exportmonthcsv=true">Descargar CSV</a> ';          
        echo '<select id="selectmes">
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
            </select>
            </p>';   
}
function statWidgetInit() {
    global $userdata;
    if( in_array('administrator', $userdata->roles) ){
        wp_add_dashboard_widget('ultimos_30_dias', 'Últimos 30 días', 'stats30Dias');	
	wp_add_dashboard_widget('el_mes_en_curso', 'El mes en curso', 'statsUltimoMes');	  
    }
} 

function exportmonthcsv(){
    // pasandole fechas  con get
    global $wpdb;
    
    $lastday = cal_days_in_month(CAL_GREGORIAN, $_GET["mes"], date('Y'));
    if( $_GET["mes"] ) {
        $mes_ini =  date("Y-m-d H:i:s", mktime(0, 0, 0,  $_GET["mes"], 1, date("Y")) );
        $mes_fin =  date("Y-m-d H:i:s", mktime(0, 0, 0,  $_GET["mes"], $lastday, date("Y")) );
        $menosmes =  gmmktime(0, 0, 0,  $_GET["mes"], 1, date("Y"));
        $masmes =   gmmktime(0, 0, 0,  $_GET["mes"], $lastday, date("Y"));
    }
    else{
        $mes_ini =  date("Y-m-d H:i:s", mktime(0, 0, 0,  date("m"), 1, date("Y")) );
        $mes_fin =  date("Y-m-d H:i:s", mktime(0, 0, 0,  date("m"), $lastday, date("Y")) );
        $menosmes =   gmmktime(0, 0, 0,  date("m"), 1, date("Y"));
        $masmes =   gmmktime(0, 0, 0,  date("m"), $lastday, date("Y"));
    }
    
        $numberPosts = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post' 
            AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'             
        ");
        $tocsv["entradas"] = $numberPosts; 
        $numberAcciones = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_acciones' 
            AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'            
        ");
        $tocsv["acciones"] = $numberAcciones; 
        $numberFotos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_fotos' 
            AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'             
        ");
        $tocsv["fotos"] = $numberFotos; 
        $numberVideos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_videos' 
            AND $wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'              
        ");
        $tocsv["videos"] = $numberVideos;         
        $R_ActiveUsers = $wpdb->get_results("
            SELECT COUNT(post_author) AS cu
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin'  
            GROUP BY post_author    
        ");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
        $tocsv["usuarios_activos"] = count($ActiveUsersAry);   
        $R_ActiveUsersComments = $wpdb->get_results("
            SELECT COUNT(user_id) AS cu
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND user_id != 0     
            AND comment_date >= '$mes_ini'  AND comment_date <= '$mes_fin' 
            GROUP BY user_id    
        ");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
        $tocsv["usuarios_comentado"] = count($ActiveUsersAryComm);          
        $numberUsers = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE  $wpdb->users.user_registered >= '$mes_ini'  AND $wpdb->users.user_registered <= '$mes_fin'            
        ");
        $tocsv["usuarios_registrados"] = $numberUsers;
        $numberUsersTotal = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE 1             
        ");
        $tocsv["usuarios_total"] = $numberUsersTotal;
        
        
        $numberComments = $wpdb->get_var("
            SELECT COUNT(comment_ID) 
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND $wpdb->comments.comment_date >= '$mes_ini' AND $wpdb->comments.comment_date <= '$mes_fin'             
        ");
        $tocsv["comentarios"] = $numberComments;
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '1' 
            AND voteTime >= $menosmes  AND voteTime <= $masmes             
        ");
        $tocsv["comentarios_positivos"] = $numberCommentsVote;
         $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '-1' 
            AND voteTime >= $menosmes  AND voteTime <= $masmes            
        ");
        $tocsv["comentarios_negativos"] = $numberCommentsVote;
        $numberAccionesVotosLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id != '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'   AND $wpdb->acciones.fecha <= '$mes_fin'              
        ");
        $tocsv["votos_acciones_login"] = $numberAccionesVotosLogin;
        $numberAccionesVotosNoLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id = '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'  AND $wpdb->acciones.fecha <= '$mes_fin'                
        ");
        $tocsv["votos_acciones_sin_login"] = $numberAccionesVotosNoLogin;        
        $numberAccionesVotosTotal = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE 
            $wpdb->acciones.fecha >= '$mes_ini' AND $wpdb->acciones.fecha <= '$mes_fin'             
        ");
        $tocsv["total_votos_acciones"] = $numberAccionesVotosTotal; 
                 $out = "";
         foreach ((array)$tocsv as $k=>$v) {
            $out .=  $k . "," . $v . "\n" ;
        }
        return $out;
}
function exportdayscsv() {
	// Display whatever it is you want to show
    global $wpdb;
        $numberPosts = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["entradas"] = $numberPosts; 
        $numberAcciones = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_acciones' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["acciones"] = $numberAcciones; 
        $numberFotos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_fotos' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["fotos"] = $numberFotos; 
        $numberVideos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_videos' 
            AND $wpdb->posts.post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["videos"] = $numberVideos;         
        $R_ActiveUsers = $wpdb->get_results("
            SELECT COUNT(post_author) AS cu
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND post_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
            GROUP BY post_author    
        ");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
        $tocsv["usuarios_activos"] = count($ActiveUsersAry); 
        $R_ActiveUsersComments = $wpdb->get_results("
            SELECT COUNT(user_id) AS cu
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND user_id != 0     
            AND comment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY) 
            GROUP BY user_id    
        ");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
        $tocsv["usuarios_comentado"] = count($ActiveUsersAryComm);         
        $numberUsers = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE  $wpdb->users.user_registered >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["usuarios_registrados"] = $numberUsers;
        $numberUsersTotal = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE 1             
        ");
        $tocsv["usuarios_total"] = $numberUsersTotal;
        $menosmes =  time() - (30 * 24 * 60 * 60);
        $numberComments = $wpdb->get_var("
            SELECT COUNT(comment_ID) 
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND $wpdb->comments.comment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["comentarios"] = $numberComments;
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '1' 
            AND voteTime >= $menosmes             
        ");
        $tocsv["comentarios_positivos"] = $numberCommentsVote;
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '-1' 
            AND voteTime >= $menosmes             
        ");
        $tocsv["comentarios_negativos"] = $numberCommentsVote;
        $numberAccionesVotosLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id != '0' 
            AND $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["votos_acciones_login"] = $numberAccionesVotosLogin;
        $numberAccionesVotosNoLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id = '0' 
            AND $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["votos_acciones_sin_login"] = $numberAccionesVotosNoLogin;        
        $numberAccionesVotosTotal = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE 
            $wpdb->acciones.fecha >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)             
        ");
        $tocsv["total_votos_acciones"] = $numberAccionesVotosTotal;             
         $out = "";
         foreach ((array)$tocsv as $k=>$v) {
            $out .=  $k . "," . $v . "\n" ;
        }
    return $out;
} 

function mensual_stat(){
    // Display whatever it is you want to show
    global $wpdb;
    $lastday = cal_days_in_month(CAL_GREGORIAN, $_GET["mes"], date('Y'));
    $mes_ini =  date("Y-m-d H:i:s", mktime(0, 0, 0,  "0".$_GET["mes"], 1, date("Y")) );
    $mes_fin =  date("Y-m-d H:i:s", mktime(0, 0, 0,  "0".$_GET["mes"], $lastday, date("Y")) );
    
    if($_GET["mes"]==1){$s1=" Selected";}
    if($_GET["mes"]==2){$s2=" Selected";}
    if($_GET["mes"]==3){$s3=" Selected";}
    if($_GET["mes"]==4){$s4=" Selected";}
    if($_GET["mes"]==5){$s5=" Selected";}
    if($_GET["mes"]==6){$s6=" Selected";}
    if($_GET["mes"]==7){$s7=" Selected";}
    if($_GET["mes"]==8){$s8=" Selected";}
    if($_GET["mes"]==9){$s9=" Selected";}
    if($_GET["mes"]==10){$s10=" Selected";}
    if($_GET["mes"]==11){$s11=" Selected";}
    if($_GET["mes"]==12){$s12=" Selected";}
    
	echo "<ol>";
        $numberPosts = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE post_status='publish'
            AND post_type = 'post' 
            AND (post_date >= '$mes_ini' AND post_date <= '$mes_fin')
        ");
        echo "<li>Entradas : <strong>" .$numberPosts . "</strong></li>";
        
        $numberAcciones = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_acciones' 
            AND ($wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin')
        ");
        echo "<li>Acciones : <strong>" .$numberAcciones . "</strong></li>";
        
        
        $numberFotos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_status = 'publish' 
            AND $wpdb->posts.post_type = 'post_fotos' 
            AND ($wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin')
        ");
        echo "<li>Fotos : <strong>" .$numberFotos . "</strong></li>";

        $numberVideos = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->posts 
            WHERE 
             $wpdb->posts.post_type = 'post_videos' 
            AND ($wpdb->posts.post_date_gmt >= '$mes_ini' AND $wpdb->posts.post_date_gmt <= '$mes_fin')
        ");
        echo "<li>Videos : <strong>" .$numberVideos . "</strong></li>";

        $R_ActiveUsers = $wpdb->get_results("
            SELECT COUNT(post_author) AS cu
            FROM $wpdb->posts 
            WHERE post_status = 'publish' 
            AND ($wpdb->posts.post_date >= '$mes_ini' AND $wpdb->posts.post_date <= '$mes_fin')
            GROUP BY post_author    
        ");
        foreach ($R_ActiveUsers as $R_ActiveUser) {
            $ActiveUsersAry[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios Activos : <strong>" .count($ActiveUsersAry) . "</strong></li>";
        
        $R_ActiveUsersComments = $wpdb->get_results("
            SELECT COUNT(user_id) AS cu
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND user_id != 0     
            AND (comment_date >= '$mes_ini' AND comment_date <='$mes_fin' )
            GROUP BY user_id    
        ");
        foreach ($R_ActiveUsersComments as $R_ActiveUsersComment) {
            $ActiveUsersAryComm[]=$R_ActiveUser->cu;
        }
        echo "<li>Usuarios con login que han comentado : <strong>" .count($ActiveUsersAryComm) . "</strong></li>";   
        
        $numberUsers = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE  $wpdb->users.user_registered >= '$mes_ini' AND $wpdb->users.user_registered <= '$mes_fin'                         
        ");
        echo "<li>Usuarios Registrados : <strong>" .$numberUsers . "</strong></li>";
 
        $numberUsersTotal = $wpdb->get_var("
            SELECT COUNT(ID) 
            FROM $wpdb->users 
            WHERE 1             
        ");
        echo "<li>Total usuarios: <strong>" .$numberUsersTotal . "</strong></li>";
 
 
        $menosmes =   gmmktime(0, 0, 0,  $_GET["mes"], 1, date("Y"));
        $masmes =   gmmktime(0, 0, 0,  $_GET["mes"], $lastday, date("Y"));
        $numberComments = $wpdb->get_var("
            SELECT COUNT(comment_ID) 
            FROM $wpdb->comments 
            WHERE $wpdb->comments.comment_approved = '1' 
            AND $wpdb->comments.comment_date >= '$mes_ini'  AND $wpdb->comments.comment_date <= '$mes_fin'                        
        ");
        echo "<li>Comentarios : <strong>" .$numberComments . "</strong></li>";
          
        $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '1' 
            AND voteTime >= $menosmes AND voteTime <= $masmes             
        ");
        echo "<li>Comentarios +1 : <strong>" .$numberCommentsVote . "</strong></li>";
        
         $numberCommentsVote = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM wp_commentsvote 
            WHERE vote = '-1' 
            AND voteTime >= $menosmes  AND voteTime <= $masmes                        
        ");
        echo "<li>Comentarios -1 : <strong>" .$numberCommentsVote . "</strong></li>";        
                
        
        $numberAccionesVotosLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id != '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'   AND $wpdb->acciones.fecha <= '$mes_fin'        
        ");
        echo "<li>Adhesiones a acciones (con login) : <strong>" .$numberAccionesVotosLogin . "</strong></li>";        
        
        $numberAccionesVotosNoLogin = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE user_id = '0' 
            AND $wpdb->acciones.fecha >= '$mes_ini'  AND $wpdb->acciones.fecha <= '$mes_fin'               
        ");
        echo "<li>Adhesiones a acciones (sin login) : <strong>" .$numberAccionesVotosNoLogin . "</strong></li>"; 
        
        $numberAccionesVotosTotal = $wpdb->get_var("
            SELECT COUNT(id) 
            FROM $wpdb->acciones 
            WHERE 
            $wpdb->acciones.fecha >= '$mes_ini'  AND $wpdb->acciones.fecha <= '$mes_fin'                        
        ");
        echo "<li>Total votos de acciones : <strong>" .$numberAccionesVotosTotal . "</strong></li>"; 
	echo "</ol>";
        echo '<p><a href="/wp-admin/?exportmonthcsv=true&mes='.$_GET["mes"].'">Descargar CSV</a> ';          
        echo '<select id="selectmes">
            <option value="1"'.$s1.'>Enero</option>
            <option value="2"'.$s2.'>Febrero</option>
            <option value="3"'.$s3.'>Marzo</option>
            <option value="4"'.$s4.'>Abril</option>
            <option value="5"'.$s5.'>Mayo</option>
            <option value="6"'.$s6.'>Junio</option>
            <option value="7"'.$s7.'>Julio</option>
            <option value="8"'.$s8.'>Agosto</option>
            <option value="9"'.$s9.'>Septiembre</option>
            <option value="10'.$s10.'">Octubre</option>
            <option value="11"'.$s11.'>Noviembre</option>
            <option value="12"'.$s12.'>Diciembre</option>
            </select>
            </p>';                  
}
function ajax_stat(){
    if ($_GET["mes"]){
        mensual_stat(); 
        exit;
    }
}

function script_stat($hook) {
    if( 'index.php' != $hook )
        return;
    wp_enqueue_script( 'custom_script', plugins_url('/ajax.js', __FILE__) );
}

function detectUserRole(){
    global $userdata;
    if( is_admin() && !in_array('administrator', $userdata->roles) ){ wp_redirect( home_url() ); exit; }
}

if($_GET["exportmonthcsv"]=="true"){
    header("Content-Type: application/csv") ; 
    header('Content-Disposition: attachment; filename="1month.csv"');    
    echo exportmonthcsv();
    exit;
}
if($_GET["exportdayscsv"]=="true"){
    header("Content-Type: application/csv") ; 
    header('Content-Disposition: attachment; filename="30days.csv"');    
    echo exportdayscsv();
    exit;
}

add_action( 'auth_redirect', 'detectUserRole' );
add_action( 'wp_dashboard_setup', 'statWidgetInit' );
add_action( 'admin_enqueue_scripts', 'script_stat' );
add_action( 'wp_ajax_ajax_stat', 'ajax_stat' );
add_action( 'wp_ajax_nopriv_ajax_stat', 'ajax_stat' );

?>

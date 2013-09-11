<?php
/*
Plugin Name: Twitter Feeds
Plugin URI: http://ida.cl
Description: Dessarrollo de sistema para buscar tweets compatible con Twitter API v1.1
Version: 1.0
Author: Fernando Silva
Author URI: http://ida.cl
License: Open Source
*/


class TwitterFeeds {
    function __construct() {
        add_action('wp_ajax_twitter_feed', array($this, 'ajax_handler'));
        add_action('wp_ajax_nopriv_twitter_feed', array($this, 'ajax_handler'));
    }
    function ajax_handler(){
        if( $_REQUEST['funcion'] && method_exists($this, $_REQUEST['funcion']) ){ $this->{$_REQUEST['funcion']}(); }
        else { die('Not Allowed >:('); }
    }
    function getSearch(){
        if( $json_transient = get_transient( 'twitter_feed_'. $_REQUEST['searchString'] ) ){
            die( $json_transient );
        }
        
        if( ! class_exists('TwitterOAuth') ){ require_once 'lib/twitteroauth.php'; }
        
        $access_data = array(
            'consumer_key' => 'SMaFAjGnApTmzzoOiKuoQ',
            'consumer_secret' => 'fev3cEMYV1JANsD6QkBWqybuGtxfjOLHSLe3XKCdwI',
            'access_token' => '8346872-s4ObCuTIvaWefmX9iNRGZF0Yl6BpO2iEPGIZXfPoDo',
            'acess_token_secret' => 'L2vdpq2L0IAw2WDY4X4lhr8H6ZuCgxoVtbqSRph60'
        );
        
        $toa = new TwitterOAuth($access_data['consumer_key'], $access_data['consumer_secret'], $access_data['access_token'], $access_data['acess_token_secret']);
        
        $json_response = json_encode( $toa->get('search/tweets', array(
            'q' => $_REQUEST['searchString'],
            'count' => intval( $_REQUEST['resultsNumber'] )
        )));
                
        set_transient( 'twitter_feed_'. $_REQUEST['searchString'], $json_response, 60 * 5 );
        
        die( $json_response );
    }
}

$twitterAjax = new TwitterFeeds();

?>

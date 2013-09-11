<?php
/*
Template Name: Portadas
*/
get_header();

if( $post->post_name === 'entradas' ){ get_template_part( 'landing', 'entradas' ); }
else { get_template_part( 'landing', 'media' ); }

get_footer();?>
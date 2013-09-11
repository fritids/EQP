<?php
/*
Template Name: Mapa del sitio
*/
?>
<?php get_header(); the_post();?>


<section id="content" class="inside gen-singles">

    <section id="inside-showcased-items">
        <div class="section-header">
            <h1 class="pseudo-breadcrumb eqp">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>   
    </section>
    <div class="article-body">
        <ul class="mapList">
        <?php 
        $args = array(
	'depth'        => 0,
	'show_date'    => '',
	'date_format'  => get_option('date_format'),
	'child_of'     => 0,
	'exclude'      => '',
	'include'      => '',
	'title_li'     => '',
	'echo'         => 1,
	'authors'      => '',
	'sort_column'  => 'menu_order, post_title',
	'link_before'  => '',
	'link_after'   => '',
        'exclude'      => '685, 41, 49, 51, 47, 45, 43, 559, 39, 682, 2139, 2141, 2143, 2145 ',
	'walker'       => '' );
        
        wp_list_pages($args);
        
        $args2 = array(
	'show_option_all'    => '' ,
        'orderby'            => 'name',
        'order'              => 'ASC',
        'style'              => 'list',
        'show_count'         => 0,
        'hide_empty'         => 1,
        'use_desc_for_title' => 1,
        'child_of'           => 0,
        'feed'               => '',
        'feed_type'          => '',
        'feed_image'         => '',
        'exclude'            => '',
        'exclude_tree'       => '',
        'include'            => '',
        'hierarchical'       => true,
        'title_li'           => __( 'Temas' ),
        'show_option_none'   => __('No categories'),
        'number'             => NULL,
        'echo'               => 1,
        'depth'              => 0,
        'current_category'   => 0,
        'pad_counts'         => 0,
        'taxonomy'           => 'category',
        'walker'             => 'Walker_Category' );
        
        wp_list_categories( $args2 );
        
        ?>
        </ul>
    </div>

</section>
</div>


<?php get_footer()?>
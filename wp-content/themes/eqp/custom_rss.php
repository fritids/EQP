<?php 
/* 
Template Name: Custom feed
*/

$args = array(
        'post_type' => 'post',
        'showposts' => 3
);

$consulta = new WP_Query($args);


header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>
<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?> - Entradas</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<?php the_generator( 'rss2' ); ?>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
        
        <?php 
        $post_objects = get_field('_fddArticles','options');
        foreach($post_objects as $item){ 
        ?>
	<item>
		<title><?php echo get_the_title($item['_eqpID']); ?></title>
		<link><?php echo get_permalink($item['_eqpID']); ?></link>
                
                <?php 
                
                $pid = get_post($item['_eqpID']); 
                
                
                ?>
                
		<autor><?php echo nombre_y_apellido($pid->post_author); ?></autor>
                <imagen><![CDATA[<?php echo get_the_post_thumbnail($item['_eqpID'], 'ajaxLoaded') ?>]]></imagen>
                
                <?php $categories = get_the_category($item['_eqpID']); ?>
                <categoria><?php echo $categories[0]->name ?></categoria>
                


<?php rss_enclosure(); ?>
<?php do_action('rss2_item'); ?>

	</item>
         <?php
             }
         ?>

</channel>
</rss>
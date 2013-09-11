<?php get_header(); the_post(); 

    $fecha = get_the_date();
    
    $categories = get_the_category( $post->ID );
    foreach( $categories as $cat ){
        $cate = $cat->cat_name;
        break;
    }
?>

<section id="content" class="inside ">
    <section id="inside-showcased-items">
        <div class="inside-content-header" >
            <p>Especial <?php echo $cate; ?></p>
            <h1><?php the_title(); ?></h1>
        </div>
        <div class="inside-col-2" >
            <?php
           regenerate_image_data( get_post_thumbnail_id( $post->ID ), 'singleEspecials_chico' );
           the_post_thumbnail('singleEspecials_chico', array('alt' => get_the_title(), 'title' => get_the_title()));
           ?>
        </div>
        <div class="inside-col-2 last especial-editorial-holder" >
            <h2 class="label">Editorial</h2>
            <?php the_content(); ?>
            <time pubdate datetime="<?php echo $fecha; ?>" title="publicado el <?php echo $fecha; ?>"><?php echo $fecha; ?></time>
        </div>
    </section>
    
    <?php
        $entradas = getEspecialContent('_entradas');
        if( $entradas ){
            echo '<section class="especial-related-content" >';
            echo '<h2 class="label">Entradas</h2>';
            echo $entradas;
            echo '</section>';
        }
        
        $fotos = getEspecialContent('_fotos');
        if( $fotos ){
            echo '<section class="especial-related-content" >';
            echo '<h2 class="label">Fotos</h2>';
            echo $fotos;
            echo '</section>';
        }
        
        $videos = getEspecialContent('_videos');
        if( $videos ){
            echo '<section class="especial-related-content" >';
            echo '<h2 class="label">Videos</h2>';
            echo $videos;
            echo '</section>';
        }
        
        $acciones = getEspecialContent('_acciones');
        if( $acciones ){
            echo '<section class="especial-related-content" >';
            echo '<h2 class="label">Acciones</h2>';
            echo $acciones;
            echo '</section>';
        }
        
    ?>
    
    
    
        
    
    
</section>
</div>

    <script>
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
            dataType: "json",
            success : function(data){ $(".visited").html("Número de visitas: " +  data.visitas); }
        });
    </script>
    
<?php get_footer();?>
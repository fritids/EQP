<?php get_header(); the_post(); 

    $fecha = get_the_date();
    
    $categories = get_the_category( $post->ID );
    foreach( $categories as $cat ){
        $cate = $cat->cat_name;
        break;
    }
?>

<section id="content" class="inside special">
    <section id="inside-showcased-items">
        <div id="special-edition-holder">
           <?php the_post_thumbnail('singleEspecials', array('alt' => get_the_title(), 'title' => get_the_title())); ?>
           <div>
               <p>Especial <?php echo $cate; ?></p>
               <h1><?php the_title(); ?></h1>
           </div>
       </div>
    </section>
    
    <?php
    
        $entradas = getEspecialContent('_entradas');
        $videos = getEspecialContent('_videos');
        $fotos = getEspecialContent('_fotos');
        $acciones = getEspecialContent('_acciones');
    
    ?>
    
    <section id="secondary-content">
        <div class="col four special">
            <?php if($entradas) : ?>
            <h2 class="label">Entradas</h2>
            <ul class="article-list">
                <?php echo $entradas; ?>
            </ul>
            <?php endif;?>

            <?php if($videos) : ?>
            <h2 class="label">Videos</h2>
            <ul class="article-list vertical">
                <?php echo $videos; ?>
            </ul>
            <?php endif;?>
        </div>
        <div class="col four special">
            <h2 class="label">Editorial</h2>
            <article class="article-body">
                <?php the_content(); ?>
                <time pubdate datetime="<?php echo $fecha; ?>" title="publicado el <?php echo $fecha; ?>"><?php echo $fecha; ?></time>
            </article>
        </div>
        <div class="col four special ultima">
            <?php if($fotos) : ?>
            <h2 class="label">Fotos</h2>
            <ul class="article-list vertical">
                <?php echo $fotos; ?>
            </ul>
            <?php endif;?>
            
            <?php if($acciones) : ?>
            <h2 class="label">Acciones Realizadas</h2>
            <ul class="article-list actions regular">
                <?php echo $acciones; ?>
            </ul>
            <?php endif;?>
        </div>
    </section>
</section>
</div>

    <script>
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
            dataType: "json",
            success : function(data){
                $(".visited").html("Número de visitas: " +  data.visitas); 
            }
            
        });
    </script>
            


<?php get_footer();?>
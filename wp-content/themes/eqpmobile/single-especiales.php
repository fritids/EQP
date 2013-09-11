<?php get_header(); the_post(); ?>
        <header class="topEntry">
            <div class="row topEntryWrap">
                <?php echo mobile_breadcrumbs(); ?>
                <h1 class="entryTitle column10"><?php the_title(); ?></h1>
                <?php if( is_singular('post') ) { echo mobile_readLater_button( 'normal' ); } ?>
                <div class="column12">
                    <ul class="socialCounter hide-on-phones aLeft">
                        <li class="faceCount editLeft"><?php echo mobile_get_shares( 'facebook', $post->ID ) ?></li>
                        <li class="tweetCount editLeft"><?php echo mobile_get_shares( 'twitter', $post->ID ) ?></li>
                        <li class="comentCount editLeft"><?php echo mobile_comments_number( $post->ID ) ?></li>
                    </ul>
                </div>
                <?php if( is_singular('post') ) { echo mobile_readLater_button( 'normal' ); } ?>
            </div> 
        </header>
        <div class="row entryWrap">
            <article class="entryContent clearfix center contEdit">
                <div class="thumbnail-holder column6" >
                    <?php
                        mobile_regenerate_image_data( get_post_thumbnail_id( $post->ID ) , 'mobile_single' );
                        the_post_thumbnail('mobile_single', array( 'title' => get_the_title(), 'alt' => get_the_title() ));
                    ?>    
                </div>
                <h2>EDITORIAL</h2>
                <?php the_content(); ?>
            </article>
        </div>
        <?php
            $entradas_relacionadas = mobile_get_especial_contents( '_entradas', $post->ID );
            if( $entradas_relacionadas ) : 
        ?>
        <div class="borderBoth row">   
            <section class="recentNews row noBorder">
                <ul id="especial_entradas_swipe_control" class="sliderPagination aRight hide-on-phones">
                    <li><a class="active" href="#">1</a></li>
                    <li><a href="#">2</a></li>
                </ul>
                <h2>ENTRADAS</h2>
                <div id="especial_entradas_swipe" data-control="especial_entradas_swipe_control" class="entradasEditorial row">
                    <ul>
                        <?php echo $entradas_relacionadas ?>
                    </ul>
                </div> 
            </section>
        </div>
        <?php endif; ?>
        <?php
            $fotos_relacionadas = mobile_get_especial_contents( '_fotos', $post->ID );
            if( $fotos_relacionadas ) :
        ?>
        <section class="row editRow">
            <ul id="especial_fotos_swipe_controls" class="sliderPagination aRight hide-on-phones">
                <li><a class="active" href="#">1</a></li>
                <li><a href="#">2</a></li>
            </ul>
            <h2>Fotos</h2>
            <div id="especial_fotos_swipe" data-control="especial_fotos_swipe_controls" class="editSlideWrap row">
                <ul>
                    <?php echo $fotos_relacionadas; ?>
                </ul>
            </div>
        </section>
        <?php endif; ?>
        <?php
            $videos_relacionados = mobile_get_especial_contents( '_videos', $post->ID );
            if( $videos_relacionados ) : 
        ?>
        <section class="row editRow">
            <ul id="especial_videos_swipe_control" class="sliderPagination aRight hide-on-phones">
                <li><a class="active" href="#">1</a></li>
                <li><a href="#">2</a></li>
            </ul>
            <h2>Videos</h2>
            <div id="especial_videos_swipe" data-control="especial_videos_swipe_control" class="editSlideWrap row" >
                <ul>
                    <?php echo $videos_relacionados; ?>
                </ul>
            </div>
        </section>
        <?php endif; ?>
        <?php
            $acciones_relacionados = mobile_get_especial_contents( '_acciones', $post->ID );
            if( $acciones_relacionados ) : 
        ?>
        <section class="row editRow noBorder acciones-relacionadas">
            <h2>Acciones</h2>
            <?php echo $acciones_relacionados; ?>
        </section>
        <?php endif; ?>
<script>
    $.ajax({
        type: "POST",
        url: '/wp-admin/admin-ajax.php',
        data: 'action=ajax_count&funcion=cva&postid=<?php echo $post->ID; ?>',
        dataType: "json",
        success : function(data){}
    });
</script>
     <?php get_footer(); ?>

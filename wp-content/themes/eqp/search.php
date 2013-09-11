<?php
/*
Template Name: Search Page
*/
?>
<?php get_header();?>


<section id="content" class="inside gen-singles">

    <section id="inside-showcased-items">
        <div class="section-header">
            <h1 class="pseudo-breadcrumb eqp">
                <?php echo breadcrumb(); ?>
            </h1>
            <?php waysToConnect(); ?>
        </div>   
    </section>
    <section class="single-content-holder">
        
        <?php if($_GET['searchType'] == 'content') : ?>
        
            <?php if($wp_query->found_posts > 0) : ?>

            <div class="message success" >
                <p><strong>¡Bien!</strong></p>
                <p>Has encontrado <?php echo $wp_query->found_posts; ?> resultados para la búsqueda <strong>"<?php echo $_GET['s'] ?>"</strong></p>
            </div>
        
            <section id="searchResultsHolder" >
                <ul id="searchResultsList" >
                    <?php if(have_posts()) : while(have_posts()) : the_post(); ?>
                    <?php 
                    
                    $data = "Entrada por:";
                    if($wp_query->post->post_type == 'post_acciones'){$data = "Acción de:";}
                    elseif($wp_query->post->post_type == 'post_videos'){$data = "Video de:";}
                    elseif($wp_query->post->post_type == 'post_fotos'){$data = "Foto por:";}
                    ?>
                    
                    <li class="searchResult <?php echo $wp_query->post->post_type ?>">
                        <h2><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                        <p class="published-by"><?php echo $data ?> <a href="/perfil-de-usuario/?user=<?php echo $wp_query->post->post_author ?>"><?php echo nombre_y_apellido($wp_query->post->post_author) ?></a></p>
                        <?php the_excerpt(); ?>
                        <a class="permalink" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_permalink(); ?></a>
                    </li>

                    <?php endwhile; endif; ?>
                </ul>
                <?php pagination($wp_query, home_url().'/'); ?>
            </section>

            <?php else : ?>

            <div class="message fail" >
                <p><strong>¡Lo sentimos!</strong></p>
                <p>No hemos encontrado ningún resultado para la búsqueda <strong>"<?php echo $_GET['s'] ?>"</strong>. Te sugerimos realizar una nueva búsqueda:</p>
                <?php get_search_form(); ?>
            </div>

            <?php endif; ?>
        
        
        <?php else : ?>
        
            <?php $autoresQuery = searchAuthors($_GET); ?>
            <?php if($autoresQuery) : ?>

            <div class="message success" >
                <p><strong>¡Bien!</strong></p>
                <p>Has encontrado <?php echo count($autoresQuery); ?> resultados para la búsqueda <strong>"<?php echo $_GET['s'] ?>"</strong></p>
            </div>
        
            <section id="searchResultsHolder" >
                <ul id="searchResultsList" >
                    <?php foreach($autoresQuery as $autor): $author = get_userdata($autor->user_id); ?>

                    <li class="searchResult users" data-score="<?php echo $autor->score; ?>" data-id="<?php echo $autor->user_id; ?>">
                        <div class="usr-avatar-holder"><?php echo get_avatar($autor->user_id, 40) ?></div>
                        <div class="searchUserInfo">
                            <h2><a href="/perfil-de-usuario/?user=<?php echo $autor->user_id; ?>" title="<?php nombre_y_apellido($author->ID, true); ?>"><?php nombre_y_apellido($author->ID, true); ?></a></h2>
                            <p>Usuario desde el <em><?php echo date_i18n( "d F, Y", strtotime($author->user_registered) ); ?></em></p>
                            <a class="permalink" href="/perfil-de-usuario/?user=<?php echo $autor->user_id; ?>" title="Ver información del usuario">Ver información del Usuario</a>
                        </div>
                    </li>

                    <?php endforeach; ?>
                </ul>
            </section>

            <?php else : ?>

            <div class="message fail" >
                <p><strong>¡Lo sentimos!</strong></p>
                <p>No hemos encontrado ningún resultado para la búsqueda del usuario <strong>"<?php echo $_GET['s'] ?>"</strong>. Te sugerimos realizar una nueva búsqueda:</p>
                <?php get_search_form(); ?>
            </div>

            <?php endif; ?>
        
        
        <?php endif; ?>
        
    </section>

</section>
</div>

<?php get_footer();?>
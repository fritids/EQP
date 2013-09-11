    <?php 
        wp_reset_query();
        wp_reset_postdata(); 
        rewind_posts();

        switch ($post->post_name){
            case 'fotos' :
                $publica = 'Publica una Foto';
                $posty = 'post_fotos';
                $type = 'photos';
                $catTitle = $post->post_name;
                $optName = 'Fotos';
                break;
            case 'videos' :
                $publica = 'Publica un Video';
                $posty = 'post_videos';
                $type = 'videos';
                $catTitle = $post->post_name;
                $optName = 'Videos';
                break;
            case 'acciones-home' :
                $posty = 'post_acciones';
                $type = 'acciones';
                $catTitle = $post->post_title;
                $optName = 'Acciones';
                break;
            default :
                $publica = 'Publica una Entrada';
                $posty = 'post';
                $type = 'entry';
                $catTitle = 'entradas';
                $optName = 'Entradas';
        }
        
        if(is_user_logged_in() ) { $current_user = wp_get_current_user(); }
        if($_GET['tipo']){$catTitle = $_GET['tipo'];}
                
    ?>
    
    <aside class="four">
        <?php if($post->post_name != 'acciones-home' && !$_GET['tipo']){ ?>
        <div class="call-to-action">
            <a href="#" class="<?php echo $type; ?> evt" data-func="showPublishForm" data-ga_opt_label="BtnPubli_<?php echo $optName; ?>" data-posttype="<?php echo $posty; ?>" data-autor="<?php if($current_user) { echo $current_user->ID; } ?>"><?php echo $publica; ?></a>
        </div>
        <ul id="corp-rules-holder">
            <li><a href="/que-es-el-quinto-poder">¿Qué es el Quinto Poder?</a></li>
            <li><a href="/reglas-de-la-comunidad">Reglas de la Comunidad</a></li>
        </ul>
        <?php } ?>
        <h2 class="label"><?php echo ucfirst($catTitle. ' por tema'); ?></h2>
            <ol id="temaRank" class="themes-ranking">
                <?php temasCount($catTitle); ?>
            </ol>
    </aside>

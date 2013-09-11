
<?php get_header();?>
<?php
    if($_GET['tipo'] != ""){
        switch ($_GET['tipo']) {
            case "entradas":
                $postType = 'post';
                break;
            case "fotos":
                $postType = 'post_fotos';
                break;
            case "videos":
                $postType = 'post_videos';
                break;
            case "Acciones":
                $postType = 'post_acciones';
                break;
        }
    }
    else {
        $postType = array('post', 'post_fotos', 'post_videos');
    }
    $categoria = get_term_by('name', single_cat_title("", false), 'category');
?>

    <section id="content" class="inside">

        <section id="inside-showcased-items">
            <div class="section-header entries">
                <h1 class="pseudo-breadcrumb subjects">
                    <?php echo breadcrumb(); ?>
                </h1>
                <?php waysToConnect(); ?>
            </div>
            <?php if(get_field('banner_header_desarrollo-regional', 'options') && is_category('desarrollo-regional')){?>
            <div class="banner_top">
                <a class="ganalytics" data-ga-category="CampanasInternas" data-ga_action="EntradaSingle" data-ga_opt_label="BannerSidebar_dr" href="<?php the_field('url_banner_header','options');?>" rel="external" title="link externo">
                    <?php echo wp_get_attachment_image(get_field('banner_header_desarrollo-regional', 'options'), 'full');?>
                </a>
            </div>
            <?php }?>
            <?php if(!$_GET['tipo']){ ?>
            <ul class="article-list vertical">
                <?php

                    $args = array(
                        'post_type' => $postType,
                        'offset' => 0,
                        'postCount' => 4,
                        'formato' => 'portada',
                        'categoria' => $categoria->slug
                    );
                    echo getPostsBlocks($args);

                ?>
            </ul>
            <?php } ?>

        </section>
        
        <?php
      
        if($_GET['tipo'] != ""){
            switch ($_GET['tipo']) {
                case "entradas":
                    $postType = 'entradas';
                    break;
                case "fotos":
                    $postType = 'fotos';
                    break;
                case "videos":
                    $postType = 'videos';
                    break;
                case "Acciones":
                    $postType = 'acciones';
                    break;
            }
        }
        else {
            $postType = false;
            
        }
        $tab = 'getTab';
        $tabTitle = 'Lo más Activo';
        $ordenTab = 'masActivos';
        if($postType == 'acciones'){
            $tab = 'accionesTabs';
            $tabTitle = 'Mayor adherencia';
            $ordenTab = 'masAdhesiones';
        }
        
        ?>
        <section id="secondary-content">
            <section id="the-most" class="col eight inside">
                <div id="tabs-holder">  

                    <ul class="menu">  
                        <li id="more-active"><a href="#" title="<?php echo $tabTitle ?>" class="evt" data-func="<?php echo $tab ?>" data-orden="<?php echo $ordenTab ?>" data-cat="<?php echo $categoria->slug; ?>" data-postType="<?php echo $postType; ?>"><?php echo $tabTitle ?></a></li>  
                        <li id="more-recent" class="current"><a href="#" title="Lo más nuevo" class="evt" data-func="<?php echo $tab ?>" data-orden="masNuevos" data-cat="<?php echo $categoria->slug; ?>" data-postType="<?php echo $postType; ?>">Lo m&aacute;s nuevo</a></li>  
                    </ul>  
                    
                    <span id="preTabs" class="clear"></span>
                    <?php
                            if($postType != 'acciones'){
                                echo get_postTabs('masNuevos', $categoria->slug, $postType, $temas = true); 
                            }else{
                                echo get_ActionTabs('masNuevos', 0, false, $categoria->slug );
                            }
                     ?>
                </div>
            </section>
                <?php if(!$_GET['tipo']){
                        get_sidebar('category'); 
                    }else{
                        get_sidebar('portadas');
                    }
                ?>

        </section>
    </section>

    </div>


<?php get_footer();?>
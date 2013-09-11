<?php

    $categories = get_the_category( $post->ID );
    
    switch ( $post->post_type ) {
        case 'post_fotos':
            $sidebarTitle = 'Fotos Relacionadas';
            break;
        case 'post_videos':
            $sidebarTitle = 'Videos Relacionados';
            break;
        default:
            $sidebarTitle = 'Entradas Relacionadas';
            break;
    }
    
?>
<aside class="column3 asideEntry only-landscape last">
    <h2><?php echo $sidebarTitle; ?></h2>
    <ul class="entryList">
        <?php echo mobile_get_related(array(
            'pType' => $post->post_type,
            'items' => 6,
            'currentPost' => array( $post->ID ),
            'categorySlug' => $categories[0]->slug
        )); ?>
    </ul>
</aside>
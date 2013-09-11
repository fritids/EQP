<?php include_once("meta-header.php"); ?>
<body <?php body_class($class); ?>>
    <header id="mainHeader" class="mainHeader">
        <div class="row headWrap"> 
            <a id="siteLogo" class="logo" title="El Quinto Poder" href="/" rel="index">
                <img src="<?php bloginfo('stylesheet_directory'); ?>/img/logo.svg" data-svgfallBack="<?php bloginfo('stylesheet_directory'); ?>/img/logo.png" alt="El Quinto Poder" title="El Quinto Poder"/>
            </a>		
            <form id="mainSearchForm" action="/" method="get" class="searchMain clearfix">
                <input class="campoSearch" type="search" placeholder="Búsqueda" required name="s">
                <button id="searchsubmit" class="submit evt" data-func="searchBtnHandler" title="Buscar"></button>
                <input style="display: none; opacity: 0;" type="submit" value="Buscar">
            </form>
        </div>

        <?php echo mobile_inputEmailMessage(); ?>
        
        <?php wp_nav_menu(array( 'menu' => 'menu-header', 'container'=>'nav', 'container_class' => 'mainNav','items_wrap'=>'<ul class="row">%3$s</ul>', )); ?> 
        <?php wp_nav_menu(array( 'menu' => 'menu-header-fixed', 'container_id' => 'alterNav','container'=>'nav', 'container_class' => 'fixedNav hidden-nav only-on-phones','items_wrap'=>'<ul>%3$s</ul>', )); ?> 
    </header>
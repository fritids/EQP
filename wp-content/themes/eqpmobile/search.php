<?php get_header(); ?>
        <article>
            <header class="topEntry">
                <div class="row topEntryWrap">
                    <?php echo mobile_breadcrumbs('Resultados de Búsqueda'); ?>
                    <h1 class="perfilTitle">Resultados de Búsqueda</h1>
                    <div class="search-term-info">
                        <p class="search-resume">
                            Se han encontrado <?php echo $wp_query->found_posts; ?> resultados para la búsqueda <strong>"<?php echo $_GET['s']; ?>"</strong>
                        </p>
                    </div>
                </div>
            </header>
            <div class="row entryWrap">
                <div id="search-results-holder" class="publiContent column10 centered-col">
                    <h2 id="search-results-title">CONTENIDOS PUBLICADOS</h2>
                    <ul id="search-results-list" >
                        <?php mobile_list_contents(array(
                            'items' => 10,
                            'searchString' => $_GET['s'],
                            'echo' => true
                        )); ?>
                    </ul>
                    <a id="see-more-content" class="verMas evt" data-func="loadMoreSearchResults" data-offset="10" data-searchTerm="<?php echo $_GET['s']; ?>">Cargar más</a>
                </div>
            </div>
        </article>
     <?php get_footer(); ?>
<?php
/*
Template Name: Pronto
*/
?>

<?php include("meta-header.php"); ?>
<body class="error404">
    
    <div class="siteWrapper">
        <header>
            <img src="<?php bloginfo('template_directory') ?>/css/ui/pronto.png" alt="Pronto" />
        </header>
        <section id="pronto" >
            <div>
                <h1><?php the_title(); ?></h1>
                <p><?php the_content(); ?></p>
                <img class="prontoImg" src="<?php bloginfo('template_directory') ?>/css/ui/logo.png" alt="El Quinto Poder" />
            </div>
        </section>
    </div>
    
</body>
<?php wp_footer();?>
</html>
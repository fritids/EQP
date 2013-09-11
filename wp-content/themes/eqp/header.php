<?php include_once("meta-header.php"); ?>
<body <?php body_class($class); ?> > 
    <div id="fb-root"></div>
    <?php 
    if( is_user_logged_in() ) { $classHeader = 'registered'; }
    else{ $classHeader = 'noRegistered'; }
    ?>

    <section id="top-section" class="clearfix <?php echo $classHeader ?>">
        <?php if( !is_user_logged_in() ) : ?>
            <div id="usr-login-holder" class="wrapper">
                <div id="usr-login-alts" class="clearfix">
                    <div class="col six">
                        <form id="regularLoginForm" class="clearfix" action="" method="post" >
                            <p class="main-Titles">¿Ya estás registrado/a? <br> Ingresa Aqui</p>
                            <input type="text" name="usrName" value="" placeholder="Nombre de usuario" >
                            <input type="password" name="usrPass" value="" placeholder="Clave">
                            <a id="forgotPass" href="#" title="¿Olvidaste tu clave?" class="evt" data-register="si" data-func="showPassRetrieval">¿Olvidaste tu clave?</a>
                            <a href="#" title="Ingresar" class="action-ca evt" data-func="regularLogin" >Ingresar</a>
                            
                            <ul class="allogin clearfix">
                                <li class="twitterLogin"><a class="evt" data-func="openLightBox" data-width="560" data-height="800"  href="/?authenticate=1">Twitter</a></li>
                                <li class="facebookLogin" ><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?loginfacebook=1">Facebook</a></li>
                                <li class="googleLogin"><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?logingoogle=1">Google</a></li>
                            </ul>   
                            
                            <p class="contingencia">Si eres usuario registrado en elquintopoder antiguo, deberás obtener una nueva clave haciendo clic en <a id="forgotPass2" class="evt" data-func="showPassRetrieval2" data-register="si" title="¿Olvidaste tu clave?" href="#">"¿Olvidaste tu clave?"</a></p>
                        </form>
                    </div>
                    <div class="col six ultima">

                        <form id="regularSignUpForm" class="clearfix" action="" method="post" >
                            <p class="main-Titles">¿No estás registrado/a? <br>Regístrate Aquí</p>
                            <ul class="accountType">
                                <li><input type="radio" id="accountType-persona" class="evt" data-func="newAccountType" data-event="change" name="accountType" value="persona" checked ><label for="accountType-persona" >Persona</label></li>
                                <li><input type="radio" id="accountType-organizacion" class="evt" data-func="newAccountType" data-event="change" name="accountType" value="organizacion" ><label for="accountType-organizacion" >Organización</label></li>
                            </ul>
                            <input type="text" name="usrLogin" value="" placeholder="Usuario" required autocomplete="off" >
                            <span class="helperMessage">Sólo letras y números (a-z, A-Z, 0-9)</span>
                            <input type="text" name="usrName" value="" placeholder="Nombre" required autocomplete="off" >
                            <input type="text" name="usrLastName" value="" placeholder="Apellidos" required autocomplete="off" >
                            <input type="email" name="usrEmail" value="" placeholder="Email" required autocomplete="off" >
                            <input type="password" name="usrPassword" value="" placeholder="Clave" required autocomplete="off" >
                            <ul class="accountType">
                                <li><input type="checkbox" id="suscribme" name="suscribme" value="suscriptor" checked ><label class="tiny-label" for="suscribme" >Quiero suscribirme al Newsletter</label></li>
                            </ul>
                            <a href="#" title="Registrar" class="action-ca evt ganalytics" data-ga-category="Registro_Login" data-ga_action="Registrar" data-ga_opt_label="Btn_Topformlogin" data-func="sendRegistroMail" >Registrar</a> 
                        </form>

                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="login-btn-holder wrapper">
            <div class="login-btn-wrapper <?php if(is_user_logged_in()) { echo 'logueado'; } ?>">
                <?php if( !is_user_logged_in() ) : ?>
                    <a href="#" id="main-login-btn" class="evt gac" data-goal="login-registro-top" data-func="showLoginOptions">Regístrate / Ingresa</a>
                <?php else : ?>
                    <div id="userInfoBtns" class="clearfix">
                    <?php 
                        $current_user = wp_get_current_user();
                        $avatar = get_simple_local_avatar( $current_user->ID, 40 );
                        $authorName = nombre_y_apellido($current_user->ID);
                        $userProvider = get_user_meta($current_user->ID, 'oa_social_login_identity_provider', true);
                        
                        $out .= '<div class="loginavatar">'. $avatar .'</div>';
                        $out .= 'Hola';
                        $out .= '<a href="/perfil-de-usuario">'. cortar($authorName, 20) .'</a>';
                        $out .= '<a href="#" data-home="'.network_site_url( '/?userCache='.$current_user->ID ).'" class="evt" data-func="logOut" title="Cerrar Sesión" data-userid="'. $current_user->ID .'" >Cerrar Sesión</a>';
                        $out .= '<input type="hidden" id="userID" name="userID" value="'. $current_user->ID .'" >';
                        echo $out;
                        
                    ?>
                    </div>
                <?php endif; ?>
           </div>
        </div>
    </section>
        <section id="publishing-options-holder"><?php if( !is_tax('categorias_blog') && !is_page('blog') && !is_singular('blog') ) : ?>
            <div class="wrapper">
                <ul id="publishing-options">
                    <li><a href="#" class="entry-publish-s evt" title="Publica una Entrada" data-func="showPublishForm" data-ga_opt_label="Menutop_Publi_Entradas" data-autor="<?php echo $current_user->ID; ?>" data-posttype="post">Publica una Entrada</a></li>
                    <li><a href="#" class="photo-publish-s evt" title="Publica una Foto" data-func="showPublishForm" data-ga_opt_label="Menutop_Publi_Fotos" data-autor="<?php echo $current_user->ID; ?>" data-posttype="post_fotos">Publica una Foto</a></li>
                    <li><a href="#" class="video-publish-s evt" title="Publica un Video" data-func="showPublishForm" data-ga_opt_label="Menutop_Publi_Videos" data-autor="<?php echo $current_user->ID; ?>" data-posttype="post_videos">Publica un Video</a></li>
                    <li><a href="#" class="action-publish-s evt" title="Crea una Acci&oacute;n" data-func="showPublishForm" data-ga_opt_label="Menutop_Publi_Acciones" data-autor="<?php echo $current_user->ID; ?>" data-posttype="post_acciones">Crea una Acci&oacute;n</a></li>
                </ul>
            </div>
    <?php endif; ?></section>
    <div class="wrapper">
        <ul id="corp-rules-holder">
            <li><a href="/que-es-el-quinto-poder" title="¿Qué es el Quinto Poder?" >¿Qué es El Quinto Poder?</a></li>
            <li><a href="/reglas-de-la-comunidad" title="Reglas de la Comunidad">Reglas de la Comunidad</a></li>
        </ul>
    </div>
    
    <div class="siteWrapper">
        
        <header id="site-header">
            <div class="clearfix" >
                <a href="<?php echo home_url(); ?>" class="logo" title="El Quinto Poder">El Quinto Poder</a>
                <div id="search-holder">
                    <?php get_search_form(); ?>
                </div>
            </div>
            
            <?php echo inputEmailMessage(); ?>        

            <nav id="mainNav">
                <ul class="clearfix">
                <?php get_mainNav(); ?>
                </ul>
            </nav>
        </header>
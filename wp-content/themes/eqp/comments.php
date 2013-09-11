<?php

	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if (!empty($post->post_password)) {
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {
                    
?>

<div class="mensaje">
	<p class="mensaje-exclama">Este Post está protegido con password. Ingresa el password para ver los comentarios</p>
</div>

<?php return; }} ?>
<?php

    switch ( $post->post_type ) {
        case 'post_fotos':
            $optLabel = 'Fotos';
            break;
        case 'post_videos':
            $optLabel = 'Videos';
            break;
        case 'post_acciones':
            $optLabel = 'Accion';
            break;
        default:
            $optLabel = 'Entrada';
    }

?>


<!------------------------------------------------------------------------------Formulario-->
<?php if ('open' == $post->comment_status) { ?>

<div id="respond">
    <form id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
        <?php do_action( 'comment_form_top' ); ?>
        
        <textarea id="comment" name="comment" placeholder="Deja tu comentario aqui..."></textarea>
        
        <?php if ( $user_ID ) : $user = get_userdata($user_ID); ?>
            <div class="mensaje clearfix">
                <p class="mensaje-exclama">Has ingresado como <a href="/perfil-de-usuario/?user=<?php echo $user_ID; ?>"><?php nombre_y_apellido($user->ID, true); ?></a>.</p>
                <p class="closeBot"><a  href="#" class="evt action-ca" style="float: left;" data-func="logOut" title="Cerrar Sesión" data-userid="<?php echo $user_ID; ?>">Cerrar sesión &raquo;</a></p>
            </div>
	<?php else : ?>
        
        <?php do_action( 'comment_form_before_fields' ); ?>
        
            <div class="comment-form-identity">
                <div class="comment-login-options">
                    <p>Llena los datos que se piden o haz clic en alguno de los &iacute;conos para ingresar:</p>
                    <div class="post-options">
                        <ul class="allogin clearfix">
                            <li class="twitterLogin"><a class="evt" data-func="openLightBox" data-width="560" data-height="800"  href="/?authenticate=1">Twitter</a></li>
                            <li class="facebookLogin" ><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?loginfacebook=1">Facebook</a></li>
                            <li class="googleLogin"><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?logingoogle=1">Google</a></li>
                            <li class="eqpLogin"><a href="#" id="postas-eqp" title="Usuario El Quinto Poder" class="evt" data-func="showLoginForm" >Comenta como usuario de El Quinto Poder</a></li>
                        </ul>
                    </div>
                </div>
                <div class="comment-form-service selected login-area">
                    <label for="email">Correo electrónico <span class="required">(requerido)</span> </label>
                    <input required type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" placeholder="ejemplo@email.com" />
                    <label for="author">Nombre completo <span class="required">(requerido)</span></label>
                    <input required type="text" name="author" id="author" value="<?php echo $comment_author; ?>" placeholder="Escribe tu nombre" />
                </div>
            </div>
        <?php do_action( 'comment_form_after_fields' ); ?>
        
        <?php endif; ?>
        
        <input type="hidden" name="anadirComentario" value="true" />
        
        <?php comment_id_fields( $post_id ); ?>
        
        <div class="form-submit clearfix">
            <a href="#" id="cancel-comment-reply-link" class="action-ca">Cancelar</a>
            <a href="#" id="submit" class="action-ca evt ganalytics" data-func="notificarComentario" data-post_type="<?php echo $post->post_type; ?>" data-ga-category="Participacion" data-ga_action="Comentarios" data-ga_opt_label="BtnComentar_<?php echo $optLabel; ?>" data-ga_value="<?php echo is_user_logged_in() ? '1' : '0'; ?>">Publicar Comentario</a>
        </div>
        <?php do_action('comment_form', $post->ID); ?>
    </form>
</div>

<?php } ?>


<!------------------------------------------------------------------------------Comentarios-->
<?php if ( have_comments() ) : ?>
        <div id="posted-comments">
            <ol id="comment_parent">
                <?php wp_list_comments('style=ul&callback=comentariosLoop&reply_text=responder');?>
            </ol>
        </div>
        <div id="respondBottom">
        <form id="commentformBottom" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
            <?php do_action('comment_form_top'); ?>

            <textarea id="comment" name="comment" placeholder="Deja tu comentario aqui..."></textarea>

            <?php if ($user_ID) : $user = get_userdata($user_ID); ?>
                <div class="mensaje clearfix">
                    <p class="mensaje-exclama">Has ingresado como <a href="/perfil-de-usuario/?user=<?php echo $user_ID; ?>"><?php nombre_y_apellido($user->ID, true); ?></a>.</p>
                    <p class="closeBot" style="margin-top: 10px;"><a  href="#" class="evt action-ca" style="float: left;" data-func="logOut" title="Cerrar Sesión" data-userid="<?php echo $user_ID; ?>">Cerrar sesión &raquo;</a></p>
                </div>
            <?php else : ?>

                <?php do_action('comment_form_before_fields'); ?>

                <div class="comment-form-identity">
                    <div class="comment-login-options">
                        <p>Llena los datos que se piden o haz clic en alguno de los &iacute;conos para ingresar:</p>
                        <div class="post-options">
                            <ul class="allogin clearfix">
                                <li class="twitterLogin"><a class="evt" data-func="openLightBox" data-width="560" data-height="800"  href="/?authenticate=1">Twitter</a></li>
                                <li class="facebookLogin" ><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?loginfacebook=1">Facebook</a></li>
                                <li class="googleLogin"><a class="evt" data-func="openLightBox" data-width="430" data-height="330" href="/?logingoogle=1">Google</a></li>
                                <li class="eqpLogin"><a href="#" id="postas-eqp" title="Usuario El Quinto Poder" class="evt" data-func="showLoginForm" >Comenta como usuario de El Quinto Poder</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="comment-form-service selected login-area">
                        <label for="email">Correo electrónico <span class="required">(requerido)</span> </label>
                        <input required type="text" name="email" id="emailBottom" value="<?php echo $comment_author_email; ?>" placeholder="ejemplo@email.com" />
                        <label for="author">Nombre completo <span class="requiredBottom">(requerido)</span></label>
                        <input required type="text" name="author" id="authorBottom" value="<?php echo $comment_author; ?>" placeholder="Escribe tu nombre" />
                    </div>
                </div>
                <input id="comment_post_ID_bottom" type="hidden" value="<?php echo $post->ID; ?>" name="comment_post_ID">

            <?php endif; ?>

            <input type="hidden" name="anadirComentario" value="true" />

            <?php comment_id_fields($post_id); ?>

            <div class="form-submit clearfix">
                <a href="#" id="cancel-comment-reply-link" class="action-ca">Cancelar</a>
                <a href="#" id="submit" class="action-ca evt ganalytics" data-func="notificarComentario" data-post_type="<?php echo $post->post_type; ?>" data-ga-category="Participacion" data-ga_action="Comentarios" data-ga_opt_label="BtnComentar_<?php echo $optLabel; ?>" data-ga_value="<?php echo is_user_logged_in() ? '1' : '0'; ?>">Publicar Comentario</a>
            </div>
            <?php do_action('comment_form', $post->ID); ?>
        </form>
        </div>
<?php else : // this is displayed if there are no comments so far ?>
	<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->
<!--		<p class="nocomments">Quieres ser el primero en comentar.</p>-->
	 <?php else : // comments are closed ?>
		<p class="nocomments">Los comentarios est&aacute;n cerrados.</p>
	<?php endif; ?>
                
<?php endif; ?>

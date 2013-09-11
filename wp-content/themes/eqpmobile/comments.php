<?php
        if( $post->post_type === 'post_acciones' ){ $acctionClass = 'accion'; }
        else { $acctionClass = ''; }

	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');
	if (!empty($post->post_password)) {
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {
?>

<div class="mensaje">
	<p class="mensaje-exclama">Este Post está protegido con password. Ingresa el password para ver los comentarios</p>
</div>
<?php return; }} ?>


<!------------------------------------------------------------------------------Formulario-->
<?php if ('open' == $post->comment_status) { ?>
    <div class="greyBorder <?php echo $acctionClass; ?>">
        <?php 

            switch ( $post->post_type ) {
                case 'post_fotos':
                    $gac_action = 'BtnMobComentFotos';
                    break;
                case 'post_videos':
                    $gac_action = 'BtnMobComentVideos';
                    break;
                case 'post_acciones':
                    $gac_action = 'BtnMobComentAcciones';
                    break;
                default:
                    $gac_action = 'BtnMobComentEntradas';
                    break;
            }

        ?>
        <form class="entryComment clearfix" data-gac_action="<?php echo $gac_action; ?>" id="commentform" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">
            <?php do_action( 'comment_form_top' ); ?>
            
            <div class="column6 comment-form-fields">
                <p><strong>Comenta esta entrada</strong></p>
                <?php if ( $user_ID ) : $user = get_userdata($user_ID); ?>
                    <p class="logued-as clearfix" >
                        Has ingresado como: <br><strong><?php nombre_y_apellido( $user_ID, true ); ?></strong>
                        <button class="sendForm aRight log-out-btn no-margin evt" data-func="logOut" data-userid="<?php echo $user_ID; ?>">Cerrar Sesión</button>
                    </p>
                <?php else : ?>
                
                <?php do_action( 'comment_form_before_fields' ); ?>
                <p class="hide-on-phones">Para publicar un comentario debes estar ingresado como usuario de El Quinto Poder, puedes hacerlo a través de:</p>
                <p class="only-on-phones">Ingresa con tu cuenta El Quinto poder o con tus redes sociales:</p>
                <ul class="socialLog">
                    <li><a class="eqpLogS evt " data-func="regularLoginAction" href="#">El Quinto Poder</a></li>
                    <li><a class="faceLogS evt " data-func="socialLoginAction" data-href="/?loginfacebook=1" href="#">Facebook</a></li>
                    <li><a class="twitterLogS evt " data-func="socialLoginAction" data-href="/?authenticate=1" href="#">Twitter</a></li>
                </ul>
                <p class="hide-on-phones">Si no eres un usuario de El Quinto Poder debes indicar tu nombre y tu e-mail:</p>
                <p class="only-on-phones">Si no eres un usuario de El Quinto Poder comenta indicando tu nombre y tu email:</p>
                <span class="commentName last">
                    <label for="author">Nombre</label>
                    <input id="author" type="text" placeholder="Escribe tu nombre" value="<?php echo $comment_author; ?>" name="author" required >
                </span>
                <span class="commentEmail">
                    <label for="email">e-mail</label>
                    <input id="email" type="email" placeholder="ejemplo@email.com" id="email" name="email" value="<?php echo $comment_author_email; ?>" required >
                </span>
                <?php do_action( 'comment_form_after_fields' ); ?>
                
                <?php endif; ?>
                
                <?php comment_id_fields( $post_id ); ?>
                <input type="hidden" name="anadirComentario" value="true" />
            </div>
            <textarea id="comment" name="comment" class="column6 last comentArea" placeholder="Escribe tu comentario..." required></textarea>
            <button id="cancel-comment-reply-link" class="sendForm aLeft">Cancelar</button>
            <input type="submit" class="sendForm aRight" value="Publicar Comentario">
        </form>
    </div>

<?php } ?>





<!------------------------------------------------------------------------------Comentarios-->
<div class="comentarios <?php echo $acctionClass; ?>">
    <h2>COMENTARIOS</h2>
    <div id="comment_parent" >
<?php
    if ( have_comments() ){
        wp_list_comments(array(
            'style'             => 'div',
            'callback'          => 'mobile_getComments',
            'reply_text'        => 'Responder',
            'avatar_size'       => 40,
        ));
    }
    else { echo '<p class="no-comments">No hay comentarios en esta entrada.</p>'; }
?>
    </div>
</div>
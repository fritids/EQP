//polyfill para Array.indexOf
if(!Array.prototype.indexOf){Array.prototype.indexOf=function(e){"use strict";if(this==null){throw new TypeError}var t,n,r=Object(this),i=r.length>>>0;if(i===0){return-1}t=0;if(arguments.length>1){t=Number(arguments[1]);if(t!=t){t=0}else if(t!=0&&t!=Infinity&&t!=-Infinity){t=(t>0||-1)*Math.floor(Math.abs(t))}}if(t>=i){return-1}for(n=t>=0?t:Math.max(i-Math.abs(t),0);n<i;n++){if(n in r&&r[n]===e){return n}}return-1}}

var global = {
    intervalo : false
}
var ajaxLoader = '<div id="ajaxLoader" ><img src="/wp-content/themes/eqp/css/ui/ajax-loader.gif" ></div>';
var js = {
    firstCopy : true,
    
    openLightBox : function(item){
        var url = $(item).attr("href");
        var windowName = "loginSocial";//$(this).attr("name");
        var width = $(item).attr("data-width");
        var height = $(item).attr("data-height");
        
        window.open(url, windowName, "width="+ width +",height="+ height +",location=no,menubarno,top=300,left=400");
    },
    
    showEmailSendingForm : function(item) {
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'showEmailSendingForm',
                currUsId : $(item).attr('data-currentUser'),
                usid : $(item).data('usid'),
                logged : $(item).data('logged')
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').fadeIn(1000);
            },
            success: function( data ) {
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="publishForm"></div>');
                $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
                $('#lightBox-content').append(data);
                $('.evtjs').evt();
            },
            error : function() {
                $('#lightBox-wrapper').remove();
            }
        });
    },
    sendEmailToUser : function(item) {
        var formObj = $(item).parents('form');
        var requiredFields = $(formObj).find('[required]');
        var valid = true;
        
        $('.invalidInput').removeClass('invalidInput');
        
        $.each( requiredFields, function(index, elm){
            if( $(elm).val() == "" || $(elm).val() == $(elm).attr('placeholder') ){
                valid = false;
                $(elm).addClass('invalidInput');
            }
            if( $(elm).val() != "" && $(elm).attr('type') == 'email' ) {
                AtPos = $(elm).val().indexOf("@")
                StopPos = $(elm).val().lastIndexOf(".")
                if ( AtPos == -1 || StopPos == -1 ) {
                    valid = false;
                    $(elm).addClass('invalidInput');
                }
            }
        });
        
        if( valid ) {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_zone&func=sendEmailToUser&'+ $(item).parents('form').serialize(),
                dataType: "html",
                beforeSend: function( xhr ) {
                    $('#sendEmailForm').fadeOut(300).promise().done(function(){
                        $('#sendEmailForm').remove();
                    });
                },
                success: function( data ) {
                    $('#succsessResponse').append(data);
                },
                error : function() {}
            });
        }
    },
    lightBoxPortada : function(item){
        var pid = $(item).attr('data-pid');
        var gallery = $(item).data('gallery');
        var postType = $(item).attr('data-ptype');
        var nextPid = $('#postsHolderList').find('a[data-gallery="'+(gallery + 1)+'"]').length ? $('#postsHolderList').find('a[data-gallery="'+(gallery + 1)+'"]') : $('#postsHolderList').find('a[data-gallery="0"]');
        var prevPid = $('#postsHolderList').find('a[data-gallery="'+(gallery - 1)+'"]').length ?  $('#postsHolderList').find('a[data-gallery="'+(gallery - 1)+'"]') : $('#postsHolderList').find('a[data-gallery="11"]');
        
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'lightBoxPortada',
                pType : postType,
                pid : pid,
                nextPid: $(nextPid).attr('data-pid'),
                prevPid: $(prevPid).attr('data-pid'),
                galleryNext: $(nextPid).attr('data-gallery'),
                galleryPrev: $(prevPid).attr('data-gallery')
                    
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('html, body').animate({ 'scrollTop' : 0 });
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').fadeIn(1000);
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="publishForm"></div>');
                $('#lightBox-content').height(600);
                $('#lightBox-content').html(ajaxLoader);
                $('#ajaxLoader').addClass('cargandoPosts');
            },
            success: function( data ) {

                var position = $(document).scrollTop();
                position = position * 1 + 50;

                $('#lightBox-content').addClass('portadas');  
                $('#lightBox-content').html(data).css('height','auto');
                  
                $('#lightBox-content').find('img').css('max-width','800px').css('height','auto');
                  
                if(postType == 'post_fotos'){
                    var imgWidth =$('#lightBox-content').find('img').width();
                    setTimeout(function(){
                        $('#lightBox-content')
                        .width(800)
                        .css({
                            'margin-left' : '-400px',
                            'padding' : '20px',
                            'top' : position+'px'
                        });
                    }, 500);
                }
                else if(postType == 'post_videos'){
                    $('#lightBox-content').width(600).css('padding','20px').css('top', position+'px');
                }
                
                $('.evtjs').evt();
            },
            error : function() {
                $('#lightBox-wrapper').remove();
            }
        });
    },
    cancelPerfil : function(item){
        $('#lightBox-wrapper.data-socialPerfil').hide();      
    },
    closeHoldingDiv :  function(item){
        var contingenciaStatus = {
                status : 'cerrado',
                lastTime : $.now() / 1000
            };

        $(item).parent().remove();
        localStorage.setItem('contingencia_status', JSON.stringify( contingenciaStatus ));
    },
    checkContingenciaMessageStatus : function(){
        var $daMessageBox = $('#mensaje-contingencia-box'),
            previousState = localStorage.getItem('contingencia_status'),
            daState = previousState ? $.parseJSON( previousState ) : false,
            nowStamp = $.now() / 1000;

        if( ! previousState || ! daState ){ return; }

        // dos semanas de validez del estado
        if( (nowStamp - daState.lastTime) >= 1209600 ){
            localStorage.removeItem('contingencia_status');
            return;
        }

        if( $daMessageBox.length && daState.status === 'cerrado' ){
            $daMessageBox.remove();
        }
    },
    checkBloqueos : function( blocksString ){
        if( !blocksString ){ return; }

        var userBlocks = blocksString.split(' ');
        js.block_publicaciones = userBlocks.indexOf('publicaciones') > -1 ? true : false;
        js.block_firmas = userBlocks.indexOf('firmas') > -1 ? true : false;
        js.block_comentarios = userBlocks.indexOf('comentarios') > -1 ? true : false;
        js.block_registros = userBlocks.indexOf('registros') > -1 ? true : false;
        js.block_usuarios = userBlocks.indexOf('usuarios') > -1 ? true : false;
    },
    retrievePass : function(item){
        var register = $(item).attr('data-register');
        var email = $(item).parent().find('input[name="retrieveMail"]').val();
        var AtPos = email.indexOf("@");
        var StopPos = email.lastIndexOf(".");
        

        $(item).parent().find('input[name="retrieveMail"]').removeClass('invalidInput');

        
        if( email && (AtPos != -1 && StopPos != -1) ){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'retrievePass',
                    email : email,
                    message: 'no'
                },
                dataType: "html",
                beforeSend: function( xhr ) {},
                success: function( data ) {
                    $(item).parent().children().fadeOut();
                    $(item).parent().append(data);
                    setTimeout(function(){
                        $(item).parent().children().fadeOut();
                        if(register == 'si'){
                            $('#usr-login-alts').find('div.col').not('.ultima').find('#retrievePassForm').remove();
                            $('#usr-login-alts').find('div.col').not('.ultima').find('#regularLoginForm').fadeIn();
                        }else{
                            $(item).parent().remove();
                            $('#ajaxLoginForm').fadeIn();
                        }     
                    }, 3000);
                },
                error : function() {}
            });
        } else {
            $('#ErrorMessage').remove();
            $('#retrievePassForm').find('.main-Titles').after('<div id="ErrorMessage">!Error¡ Email no corresponde o dato ingresado no es válido</div>');
            $('#retrievePassForm').find('input[name="retrieveMail"]').addClass('invalidInput');
        }
    },
    retrievePass2 : function(item){
        var email = $('input[name="retrieveMail"]').val();
        
        var AtPos = email.indexOf("@");
        var StopPos = email.lastIndexOf(".");

        if( email && (AtPos != -1 && StopPos != -1) ){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'retrievePass',
                    email : email,
                    message: 'si'
                },
                dataType: "html",
                beforeSend: function( xhr ) {},
                success: function( data ) {
                    
                    if(data == 'Hemos enviado una nueva clave a tu correo electrónico' ){
                        $('.message.alert').find('p').text(data);
                        $('.message.alert').find('a').remove();
                        $('.message.alert').find('input').remove();
                        setTimeout(function(){ 
                            $('.message.alert').remove();
                        }, 3000);
                    }else{
                        $('.message.alert').find('p').text(data); 
                    }
                    
                },
                error : function() {}
            });
        } else {
            $('.message.alert').find('p').text('!Error¡ Email no corresponde o dato ingresado no es válido');
        }
        
    },
    cancelRetrievePass : function(item){
        var register = $(item).attr('data-register');

        $(item).parent().hide();
        if(register == 'si'){
            $('#usr-login-alts').find('div.col').not('.ultima').find('#regularLoginForm').show();
        }else{
            $('#ajaxLoginForm').show();
            $('#retrievePassForm').remove();
        } 
    },
    showPassRetrieval : function(item){
        
        var register = $(item).attr('data-register');
        var retrievepassForm = '<form id="retrievePassForm" class="clearfix" method="post" action="">';
        retrievepassForm += '<p class="main-Titles">¿Haz Olvidado tu clave?</p>';
        retrievepassForm += '<input type="email" placeholder="Ingresa tu email" name="retrieveMail">';
        retrievepassForm += '<a class="action-ca evtjs" data-register="'+register+'" data-func="retrievePass" title="Recuperar Clave" href="#">Recuperar Clave</a>';
        retrievepassForm += '<a class="action-ca evtjs" data-register="'+register+'" style="float: left;" data-func="cancelRetrievePass" title="Cancelar" href="#">Cancelar</a>';
        retrievepassForm += '</form>';

        $(item).parent().hide();
        if(register == 'si'){
            $('#usr-login-alts').find('div.col').not('.ultima').append(retrievepassForm);
        }else{
            $('#lightBox-content').find('div.lightRegister').append(retrievepassForm);
        }
        $('.evtjs').off('click');
        $('.evtjs').evt();
    },
    showPassRetrieval2 : function(item){
        $('#forgotPass').trigger('click');
        
    },
    showUserEditForm : function(item) {
        if( js.block_usuarios ){ return js.getBloqueoMessage( 'usuarios' ); }

        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'showUserEditForm',
                usid : $(item).data('usid')
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('#inside-showcased-items').find('div.userInfoBox').css('opacity', '0.2');
            },
            success: function( data ) {
                $('#inside-showcased-items').find('div.userInfoBox').remove();
                $('#inside-showcased-items').find('div.section-header').prepend(data);
                
                $(item).text('Guardar Datos');
                $(item).attr({
                    'title' : 'Guardar Datos',
                    'data-func' : 'SaveUsrData'
                });
                $(item).off('click');
                $(item).on('click', function(e){
                    e.preventDefault();
                    js.SaveUsrData();
                });
                $('#changeAvatarAction').evt();
                $('#changePassBtn').evt();
            },
            error : function() {
                $('#inside-showcased-items').find('div.userInfoBox').css('opacity', '1');
            }
        });
    },
    SaveUsrData : function(item){
        var pass = $('input[name="usrPass"]').val(),
            rePass = $('input[name="usrPassRepeat"]').val(),
            twitter = $('#usrTwitter').val(),
            facebook = $('#usrFacebook').val();
            
        if( pass || rePass ){
            if( pass == rePass ) {
                $('#usrEditForm').submit();
            }
            else {
                $('input[name="usrPass"]').addClass('invalidInput');
                $('input[name="usrPassRepeat"]').addClass('invalidInput');
                $('input[name="usrPassRepeat"]').after('<div class="formError"><em>Las contraseñas no coinciden</em></div>');
            }
        }
        else {
            $('#usrEditForm').submit();
        }
    },
    regularLogin : function(item) {
        
        var placeholderItems = $('#regularLoginForm').find('[placeholder]');
        $.each( placeholderItems ,function(index,elm){
            if( $(elm).val() == $(elm).attr('placeholder') ) {
                $(elm).val("");
            }
        });
        
        
        var formdata = $('#regularLoginForm').serialize();
        var secHeight = $('#top-section').height();
        var pass = $('#regularLoginForm').find('input[name="usrPass"]').val()
        var usrName = $('#regularLoginForm').find('input[name="usrName"]').val();
        var AtPos = usrName.indexOf("@");
        var StopPos = usrName.lastIndexOf(".");
        
        $('.invalidInput').removeClass('invalidInput');
        $('#ErrorMessage').remove();
        
        if( pass && usrName ){
            _gaq.push(['_trackEvent', 'Registro_Login', 'Login', 'Btn_Topformlogin']);
            
            // _gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'Si', 2]);
            // _gaq.push(['_trackPageview']);
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'regularLogin',
                    usrName : usrName,
                    usrPass : pass
                },
                dataType: "html",
                beforeSend: function( xhr ) {
                    $('#top-section').css('height',secHeight);
                    $('#usr-login-holder').fadeOut(500);
                    $('#top-section').append(ajaxLoader);
                },
                success: function( data ) {
                    if (data != "error") {
                        $('#top-section').css('height','auto');
                        $('#ajaxLoader').remove();
                        $('#ErrorMessage').remove();
                        window.location.reload();
                    }
                    else {
                        $('#top-section').css('height','auto');
                        $('#usr-login-holder').fadeIn(500);
                        $('#ajaxLoader').remove();
                        $('#ErrorMessage').remove();
                        $('#regularLoginForm').find('input[name="usrName"]').addClass('invalidInput');
                        $('#regularLoginForm').find('input[name="usrPass"]').addClass('invalidInput');
                        $('#regularLoginForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Nombre de usuario o Contraseña Incorrectos</div>');
                    }
                },
                error : function() {
                    $('#ajaxLoader').remove();
                    $('#usr-login-holder').fadeIn(500);
                }
            });
        }
        else if ( !pass || !usrName ) {
            $('#regularLoginForm').find('input[name="usrName"]').addClass('invalidInput');
            $('#regularLoginForm').find('input[name="usrPass"]').addClass('invalidInput');
            $('#regularLoginForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Ingrese los campos requeridos</div>');
        }
        else if ( usrName && (AtPos == -1 || StopPos == -1) ) {
            $('#regularLoginForm').find('input[name="usrName"]').addClass('invalidInput');
            $('#regularLoginForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Ingrese un Email Válido</div>');
        }
    },
    logOut : function(item) {
        var url = $(item).attr('data-home');
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'logOut'
            },
            dataType: "html",
            beforeSend: function( xhr ) { },
            success: function( data ) {
                window.location.reload();
            },
            error : function() { }
        });
    },
    ChangeAvatar : function(item){
        $('#uploadAvatar').trigger('click');
    },
    showCats : function(item) {
        $(item).find('ul').toggle();
    },
    carouselControl : function(item){
        var slide = $(item).attr('data-item');
        $('#carousel').find('div.carousel-item[data-item="'+ slide +'"]').addClass('current').fadeIn(500);
        $('#carousel').find('div.carousel-item[data-item!="'+ slide +'"]').removeClass('current').fadeOut(500);
        
        $('#carousel-nav').find('a[data-item!="'+ slide +'"]').removeClass('current');
        $(item).addClass('current');
    },
    showCommentForm : function(item){
        $('#respond').slideToggle(500);
    },
    getTab : function(item) {
        var orden = $(item).attr('data-orden');
        var cate = $(item).attr('data-cat');
        var postType = $(item).attr('data-postType');
        var home;
        if($('body').hasClass('home')){
            home = 'home';
        }else{
            home = 'notHome';
        }
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'getTab',
                order : orden,
                category : cate,
                pType : postType,
                home : home
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('#currTab').css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).parent().parent().find('li').removeClass('current');
                $(item).parent().addClass('current');
                $('#currTab').remove();
                $('#preTabs').after(data);
                $('#currTab > section > ul.vertical > li').equalHeights();
                $('#currTab').find('.evt').off('click');
                $('#currTab').find('.evt').evt();
            },
            error : function() {
                $('#currTab').css('opacity', '1');
            }
        });
    },
    accionesTabs : function(item) {
        var orden = $(item).attr('data-orden'),
        category = $(item).attr('data-cat');
        
        $('#verMasBtn').attr('data-orden', orden);
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'accionesTabs',
                order : orden,
                category: category
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('#currTab').css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).parent().parent().find('li').removeClass('current');
                $(item).parent().addClass('current');
                $('#currTab').remove();
                $('#preTabs').after(data);
            },
            error : function() {
                $('#currTab').css('opacity', '1');
            }
        });
    },
    getEntradasTabs : function(item) {
        var orden = $(item).attr('data-orden');
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'getEntradasTabs',
                order : orden
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('#currTab').css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).parent().parent().find('li').removeClass('current');
                $(item).parent().addClass('current');
                $('#currTab').remove();
                $('#preTabs').after(data);
            },
            error : function() {
                $('#currTab').css('opacity', '1');
            }
        });
        
    },
    getPropuestasTab : function(item ){
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'getPropuestasTab',
                order : $(item).attr('data-orden'),
                comuna : $(item).attr('data-cat')
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('#currTab').css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).parent().parent().find('li').removeClass('current');
                $(item).parent().addClass('current');
                $('#currTab').remove();
                $('#preTabs').after(data);
            },
            error : function() {
                $('#currTab').css('opacity', '1');
            }
        });
    },
    temasRank : function(){
        var lis = $('#temaRank').find('li');
        var maximo = 0;
        var calc;
        $.each(lis, function(index, elm){
            maximo += $(elm).attr('data-count') * 1;
        });
        $.each(lis, function(index, elm){
            calc = ( $(elm).attr('data-count') * 100 ) / maximo;
            if( calc < 10 ){ calc += 20; }
            else if( calc < 20 ){ calc += 18; }
            else if( calc < 30 ){ calc += 16; }
            else if( calc < 40 ){ calc += 14; }
            else if( calc < 50 ){ calc += 12; }
            else if( calc < 60 ){ calc += 10; }
            else if( calc < 70 ){ calc += 8; }
            else if( calc < 80 ){ calc += 6; }
            else if( calc < 90 ){ calc += 4; }
            else if( calc < 100 ){ calc += 2; }
            
            $(elm).css('width', calc +'%');
        });
        
    },
    orderCommentsReplies : function() {
        var replies = $('li.comment-reply');
        var replyTo;
        var parent;
        
        $.each(replies, function(){
            replyTo = $(this).attr('data-replyTo');
            parent = $(this).parent().find('li[data-commentid="'+ replyTo +'"]');
            $(parent).after($(this));
        });
    },
    showLoginOptions : function( item ) {
        $('body').unbind('click');
        
        var top = $('#top-section').css('top');
        var height = $('#top-section').outerHeight();
        
        if( top == '0px' ){
            $('#top-section').animate({
                'top' : (height *-1 +5)+'px'
            }, 500);
            $('#top-section').removeClass('uncollapsed');
        }
        else {
            $('#top-section').animate({
                'top' : 0
            }, 500);
            $('#top-section').addClass('uncollapsed');
        }
        if( $('#main-login-btn').attr('data-response') != 'true' ){
            if($('#main-login-btn').text() != 'Cerrar') {
                $('#main-login-btn').text('Cerrar')
            }
            else {
                $('#main-login-btn').text('Regístrate / Ingresa')
            }
        }
        if( $('#top-section').hasClass('uncollapsed') ){
            $('body').bind('click', function(event){
                if( event.pageY > (height + 100) ){
                    js.showLoginOptions();
                }
            });
        }
    },
    showPassMessage : function() {
        
    },
    hideTopSection : function() {
        var altura = $('#top-section').height();
        $('#top-section').css({
            'position' : 'absolute', 
            'top' : (altura *-1 + 5)+'px'
        });
    },
    actionSignature : function(item) {
        if( js.block_firmas ){ return js.getBloqueoMessage( 'firmas' ); }

        var pid = $('#main-content').attr('data-postid');
        var userid = $('#userID').val();
        userid = userid ? userid : 0;
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'actionSignature',
                user : userid,
                postid : pid
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').append(ajaxLoader);
                $('#lightBox-wrapper').fadeIn(1000);
                $('html, body').animate({ 'scrollTop' : 0 });
            },
            success: function( data ) {
                $('#ajaxLoader').remove();
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="actionsVote"></div>');
                if(userid != 0) {
                    $('#lightBox-content').removeClass('actionsVote');
                }
                $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
                $('#lightBox-content').append(data);
                $('#lightBox-content').css({
                    'position' : 'absolute',
                    'top' : '50px',
                    'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2),
                    'margin-left' : '0px'
                });
                $('.evtjs').evt();
//                $('input, textarea').placeholder();
            },
            error : function() {
                $('#lightBox-wrapper').remove();
            }
        });
    },
    ajaxLoginAndVote : function(item){
        var originalHeight = $('#lightBox-content').height();
        var placeholderItems = $('#ajaxLoginForm').find('[placeholder]');
        var $requiredFields = $('#ajaxLoginForm').find('[required]');
        $.each( placeholderItems ,function(index,elm){
            if( $(elm).val() == $(elm).attr('placeholder') ) {
                $(elm).val("");
            }
        });
        
        $.each($requiredFields, function( index, elem ){ js.validateInput( $(elem) ); });
        
        if( $('#ajaxLoginForm').find('.invalidInput').length === 0 ){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'ajaxLoginAndVote',
                    usrName : $('#ajaxLoginForm').find('input[name="usrName"]').val(),
                    usrPass : $('#ajaxLoginForm').find('input[name="usrPass"]').val(),
                    postid : $('#main-content').attr('data-postid')
                },
                dataType: "json",
                beforeSend: function( xhr ) {
                    $('#lightBox-content').find('.inLightBox').fadeOut(500);
                    $('#lightBox-content').append(ajaxLoader);
                    $('#lightBox-content').css('height',originalHeight);
                },
                success: function( data ) {
                    _gaq.push(['_trackEvent', 'Participacion', 'Firmas', 'BtnFirma_SinRegIngresar']);

                    var outMessage = "";
                    $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);
                    $('#lightBox-content').addClass('registro');
                        
                    $('#lightBox-content').css({
                        'position' : 'fixed',
                        'top' : '200px',
                        'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2),
                        'margin-left' : '0px'
                    });
                    
                    if (data.status != "error" && data.status != "repetido") {
                        
                        // _gaq.push(['_setCustomVar', 1, 'Usuarios Logueados', 'Si', 2]);
                        // _gaq.push(['_trackPageview']);
                        
                        $('#numberOfVotes').find('span').text(''+ data.voteNum +'');
                        $('#lightBox-content').find('.inLightBox').empty();
                        
                        outMessage += '<div class="message success" >';
                        outMessage += '<p class="main-Titles">¡Gracias por firmar!</p>';
                        outMessage += '<p>Ayuda a difundir esta Acción compartiéndola en tus redes sociales</p>';
                        outMessage += data.socialUl;
                        outMessage += '</div>';
                        
                        $('#lightBox-content').find('.inLightBox').append( outMessage );
                    }
                    else if (data.status == 'repetido') {
                        $('#ErrorMessage').remove();
                        $('#ajaxLoginForm').find('input[name="usrName"]').addClass('invalidInput');
                        $('#ajaxLoginForm').find('input[name="usrPass"]').addClass('invalidInput');
                        $('#ajaxLoginForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Usted ya ha firmado esta Acción</div>');
                    }
                    else {
                        $('#ErrorMessage').remove();
                        $('#ajaxLoginForm').find('input[name="usrName"]').addClass('invalidInput');
                        $('#ajaxLoginForm').find('input[name="usrPass"]').addClass('invalidInput');
                        $('#ajaxLoginForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Nombre de usuario o Contraseña Incorrectos</div>');
                    }
                },
                error : function() {
                    $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);
                }
            });
        }
        else {
            $('#ErrorMessage').remove();
            $('#ajaxLoginForm').find('label[for="log_usrName"]').before('<div id="ErrorMessage" >Ingrese los campos requeridos</div>');
        }
    },
    voteForActionNoLogued : function(item) {
        var placeholderItems = $('#ajaxVoteForm').find('[placeholder]');
        var originalHeight = $('#lightBox-content').height();
        var $requiredFields = $('#ajaxVoteForm').find('[required]');
        
        $.each( placeholderItems ,function(index,elm){
            if( $(elm).val() == $(elm).attr('placeholder') ) {
                $(elm).val("");
            }
        });
        
        $.each($requiredFields, function( index, elem ){ js.validateInput( $(elem) ); });
        
        if ( $('#ajaxVoteForm').find('.invalidInput').length === 0 ) {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data : 'action=ajax_zone&func=voteForActionNoLogued&postid=' + $('#main-content').attr('data-postid') + '&' + $('#ajaxVoteForm').serialize(),
                dataType: "json",
                beforeSend: function( xhr ) {
                    $('#lightBox-content').find('.inLightBox').fadeOut(500);
                    $('#lightBox-content').append(ajaxLoader);
                    $('#lightBox-content').css('height',originalHeight);
                },
                success: function( data ) {
                    _gaq.push(['_trackEvent', 'Participacion', 'Firmas', 'BtnFirma_SinRegFirmar']);

                    var outMessage = "";
                    $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);
                    $('#lightBox-content').addClass('registro');
                        
                    $('#lightBox-content').css({
                        'position' : 'fixed',
                        'top' : '200px',
                        'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2),
                        'margin-left' : '0px'
                    });
                        
                    if (data.status != "repetido") {
                        outMessage += '<div class="message success" >';
                        outMessage += '<p class="main-Titles">¡Gracias por firmar!</p>';
                        outMessage += '<p>Ayuda a difundir esta Acción compartiéndola en tus redes sociales</p>';
                        outMessage += data.socialUl;
                        outMessage += '</div>';
                            
                        $('#numberOfVotes').find('span').text(''+ data.voteNum +'');
                        $('#lightBox-content').find('.inLightBox').empty();
                        $('#lightBox-content').find('.inLightBox').append( outMessage );
                    }
                    else if (data.status == 'repetido') {
                        $('#ErrorMessage').remove();
                        $('#ajaxVoteForm').find('input[name="usrName"]').addClass('invalidInput');
                        $('#ajaxVoteForm').find('input[name="usrEmail"]').addClass('invalidInput');
                        $('#ajaxVoteForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >\n\
                                                                                     <p class="main-Titles">¡Lo Sentimos!</p>\n\
                                                                                     <p>Usted ya ha firmado esta Acción</p>\n\
                                                                                    </div>');
                    }
                },
                error : function() {
                    $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);
                }
            });
        }
        else {
            $('#ErrorMessage').remove();
            $('#ajaxVoteForm').find('input[name="usrName"]').before('<div id="ErrorMessage" >Ingrese los campos requeridos</div>');
        }
            
    },
    voteForAction : function(item){
        var userName = $(item).attr('data-username');
        var userEmail = $(item).attr('data-useremail');
        var userID = $(item).attr('data-userid');
        var post_ID = $(item).attr('data-postid');
        var validity;
        
        if( $('#voteActionLogued').length ){
            $.each($('#voteActionLogued').find('[required]'), function( index, elem ){ js.validateInput( $(elem) ); });
        }
        
        validity = (function(){
            if( $('#voteActionLogued').length ){ return $('#voteActionLogued').find('.invalidInput').length === 0; }
            else { return true; }
        }());
        
        if( validity ){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'voteForActionLogued',
                    usrName :userName,
                    usrMail : userEmail,
                    usrID : userID,
                    postid : post_ID
                },
                dataType: "json",
                beforeSend: function( xhr ) {
                    $('#lightBox-content').find('.inLightBox').fadeOut(500);
                //                $('#lightBox-content').append(ajaxLoader);
                },
                success: function( data ) {
                    var outMessage = "";
                    //                $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);

                    $('#lightBox-content').addClass('registro');

                    $('#lightBox-content').css({
                        'position' : 'fixed',
                        'top' : '200px',
                        'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2),
                        'margin-left' : '0px'
                    });

                    if (data.status != "repetido") {
                        outMessage += '<div class="message success" >';
                        outMessage += '<p class="main-Titles">¡Gracias por firmar!</p>';
                        outMessage += '<p>Ayuda a difundir esta Acción compartiéndola en tus redes sociales</p>';
                        outMessage += data.socialUl;
                        outMessage += '</div>';

                        $('#numberOfVotes').find('span').text(''+ data.voteNum +'');
                        $('#lightBox-content').find('#usr-login-holder').empty();
                        $('#lightBox-content').find('#usr-login-holder').append(outMessage);
                    }
                    else if (data.status == 'repetido') {
                        $('#ErrorMessage').remove();
                        $('#lightBox-content').find('#usr-login-holder').empty();
                        $('#lightBox-content').find('#usr-login-holder').append('<div id="message" >\n\
                                                                                <p class="main-Titles">¡Lo Sentimos!</p>\n\
                                                                                <p>Usted ya ha firmado esta Acción</p>\n\
                                                                                </div>');
                    }
                    else {
                        $('#lightBox-wrapper').remove();
                    }
                },
                error : function() {
                    $('#ajaxLoader').remove();
                    $('#lightBox-content').css('height','auto');
                    $('#lightBox-content').find('.inLightBox').fadeIn(500);
                }
            });
        } else {
            console.log('no valido');
        }
        
    },
    fullsizeImage : function(item) {
        var pid = $(item).attr('data-pid');
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'fullsizeImage',
                postid : pid
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="publishForm"></div>');
                $('#lightBox-content').append(ajaxLoader);
                $('#lightBox-wrapper').fadeIn(1000);
            },
            success: function( data ) {
                $('#ajaxLoader').remove();
                
                var variableWidth = 800;
                
                $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
                $('#lightBox-content').append(data);
                $('#lightBox-content').width();
                
                if($('#lightBox-content').find('img').width() < 800){ 
                    variableWidth = $('#lightBox-content').find('img').width(); 
                }
                $('#lightBox-content').width(variableWidth);


                $('#lightBox-content').css({
                    'margin-left' : '0px',
                    'border' : '15px solid #01334D',
                    'border-radius' : '15px'
                });
                $('#lightBox-content').find('img').css('max-width','800px').css('height','auto');
                $('#lightBox-content').css({
                    'position' : 'absolute',
                    'top' : 20,
                    'left' : ($(window).width()/2) - (($('#lightBox-content').find('img').width()+30)/2)
                });
                $('.evtjs').evt();
            },
            error : function() {
                $('#lightBox-wrapper').remove();
            }
        });
    },
    showPublishForm : function(item){
        var autor = $(item).attr('data-autor');
        var postType = $(item).attr('data-posttype');

        if( js.block_publicaciones ){ return js.getBloqueoMessage( 'publicaciones' ); }
        
        if(autor == "" || autor == "0") {
            _gaq.push(['_trackEvent', 'Participacion', 'Publicaciones', $(item).attr('data-ga_opt_label'), 0]); 
            $('#main-login-btn').trigger('click');
            $('html, body').animate({ 'scrollTop' : 0 }, 200);
        }
        else {
             _gaq.push(['_trackEvent', 'Participacion', 'Publicaciones', $(item).attr('data-ga_opt_label'), 1]);
            switch( postType ){
                case 'post_fotos' :
                    window.location.href = window.location.origin + '/publicar-contenido/?tipo=foto';
                    break;
                case 'post_videos' :
                    window.location.href = window.location.origin + '/publicar-contenido/?tipo=video';
                    break;
                case 'post_acciones' :
                    window.location.href = window.location.origin + '/publicar-contenido/?tipo=accion';
                    break;
                default :
                    window.location.href = window.location.origin + '/publicar-contenido/';
                    break;
            }
        }
    },
    closeLightBox : function (item){
        $('#lightBox-wrapper').fadeOut(300).promise().done(function() {
            $('#lightBox-wrapper').remove();
        });
    },
    sendRegistroMail : function (item) {
        if( js.block_registros ){ return js.getBloqueoMessage( 'registros' ); }
        
        var formulario = $('#regularSignUpForm');
        var formData = $(formulario).serialize();
        var requeridos = $(formulario).find('[required=""]');
        var secHeight = $('#top-section').height();
        var valid = true;
        var type;
        var AtPos;
        var StopPos;
        
        var placeholderItems = $('#regularSignUpForm').find('[placeholder]');
        $.each( placeholderItems ,function(index,elm){
            if( $(elm).val() == $(elm).attr('placeholder') ) {
                $(elm).val("");
            }
        });
        
        $.each(requeridos, function(){
            type = $(this).attr('type') || $(this).attr('data-type');
            $(this).removeClass('invalidInput');

            if( $(this).val() == "" || $(this).val() == " " ) {
                valid = false
                $(this).addClass('invalidInput');
            }
            if( type == 'email' && $(this).val() != "" ) {
                var emailval = $(this).val();
                AtPos = emailval.indexOf("@");
                StopPos = emailval.lastIndexOf(".");
                if( AtPos == -1 || StopPos == -1 ) {
                    valid = false
                    $(this).addClass('invalidInput');
                }
            }
        });
        
        if (valid){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_zone&func=sendRegistroMail&'+formData,
                dataType: "html",
                beforeSend: function( xhr ) {
                    $('#top-section').css('height',secHeight);
                    $('#usr-login-alts').fadeOut(500);
                    $('#top-section').append(ajaxLoader);
                },
                success: function( data ) {
                    
                    if(data != 'userExist'){
                        $('#usr-login-alts').hide();
                        $('#ajaxLoader').remove();
                        $('#usr-login-holder').append(data)
                        $('#usr-login-holder').find('.evtjs').evt();
                    }else{
                        $('#top-section').css('height','auto');
                        $('#ErrorMessage').remove();
                        $('#ajaxLoader').remove();
                        $('#usr-login-alts').show();
                        $('#regularSignUpForm').find('.accountType').after('<div id="ErrorMessage">El nombre de usuario o e-mail ingresado ya existe!!</div>');
                        
                    }
                    
                },
                error : function() {
                    $('#ajaxLoader').remove();
                    $('#usr-login-alts').fadeIn(500);
                }
            });
        }
    },
    showLogin : function(item) {
        $('#main-login-btn').trigger('click');
    },
    showLoginForm : function(item) {
        var form = '<div class="lightRegister">';
        form += '<form id="ajaxLoginForm" class="clearfix" action="" method="post" >';
        form += '<p class="main-Titles">¿Ya estás registrado/a? <br> Ingresa Aqui</p>';
        form += '<input type="text" name="usrName" value="" placeholder="Nombre de usuario" >';
        form += '<input type="password" name="usrPass" value="" placeholder="Clave" >';
        form += '<a id="forgotPass" class="evtjs" data-register="no" data-func="showPassRetrieval"  href="#" title="¿Olvidaste tu clave?">¿Olvidaste tu clave?</a>';
        form += '<a href="#" title="Ingresar" class="action-ca evtjs" data-func="ajaxLogin" >Ingresar</a> ';
        form += '</form>';
        form += '</div>';
        
        $('body').append('<div id="lightBox-wrapper" ></div>');
        $('#lightBox-wrapper').height($(document).height());
        $('#lightBox-wrapper').fadeIn(1000);
        $('#lightBox-wrapper').append('<div id="lightBox-content"></div>');
        
        $('#lightBox-content').addClass('registro');
        
        $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
        $('#lightBox-content').append(form);
        
        $('#lightBox-content').css({
            'position' : 'fixed',
            'top' : ($(window).height()/2) - ($('#lightBox-content').height()/2),
            'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2)
        });
        
        $('.evtjs').evt();
    },
    ajaxLogin : function(item) {
        
        var placeholderItems = $('#ajaxLoginForm').find('[placeholder]');
        $.each( placeholderItems ,function(index,elm){
            if( $(elm).val() == $(elm).attr('placeholder') ) {
                $(elm).val("");
            }
        });
        
        var login = $('#ajaxLoginForm').find('input[name="usrName"]').val();
        var pass = $('#ajaxLoginForm').find('input[name="usrPass"]').val();
        
        var AtPos = login.indexOf("@");
        var StopPos = login.lastIndexOf(".");
        var valid = true;
        
        
        
        
        $('#ajaxLoginForm').find('.invalidInput').removeClass('invalidInput');
        
        if( login == "" ){
            $('#ajaxLoginForm').find('input[name="usrName"]').addClass('invalidInput');
            valid = false;
        }
        
        if( login && (AtPos == -1 || StopPos == -1) ){
            $('#ajaxLoginForm').find('input[name="usrName"]').addClass('invalidInput');
            valid = false;
        }
        
        if( pass == "" ){
            $('#ajaxLoginForm').find('input[name="usrPass"]').addClass('invalidInput');
            valid = false;
        }
        
        if(valid) {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'ajaxLogin',
                    usrName : login,
                    usrPass : pass
                },
                dataType: "html",
                beforeSend: function( xhr ) {
                    $('#ajaxLoginForm').fadeOut();
                    $('#lightBox-content > div').append(ajaxLoader);
                },
                success: function( data ) {
                    window.location.reload();
                },
                error : function() {}
            });
        }
    },
    newAccountType : function(item) {
        var tipocuenta = $(item).val();
        
        if( $(item).is(':checked') && tipocuenta == 'organizacion' ){
            $('#regularSignUpForm').find('input[name="usrLastName"]').hide();
            $('#regularSignUpForm').find('input[name="usrLastName"]').removeAttr('required');
        }
        else {
            $('#regularSignUpForm').find('input[name="usrLastName"]').show();
            $('#regularSignUpForm').find('input[name="usrLastName"]').attr('required');
        }
    },
    autoCarousel : function() {
        var current = $('#carousel-nav').find('a.current');
        var currentParent = $(current).parent();
        
        if( $(currentParent).next().length ){
            $(currentParent).next().find('a').trigger('click');
        }
        else {
            $('#carousel-nav ul li:first').find('a').trigger('click');
        }
    },
    anadirShares : function(item) {
        var provider = $(item).attr('data-provider');
        var pid = $(item).attr('data-pid');
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'anadirShare',
                provider : provider,
                postid : pid
            },
            dataType: "html",
            success : function(data) {
                $(item).text(data);
            }
        });
    },
    publishFormValidation : function(){
        var form = $('#publicationForm');
        var requeridos = $(form).find('[required=""]');
        var valid = true;
        var type;
        $(form).find('#ajaxLoader').remove();

        $(form).find('.invalidInput').removeClass('invalidInput');
        $(form).find('#formError').remove();
        $.each(requeridos, function(index, value){
            type = $(this).attr('type') || $(this).attr('data-type');

            if( $(this).val() == "" || $(this).val() == " " ) {
                valid = false
                $(this).addClass('invalidInput');
            }
            else if( type == 'select' && $(this).find('option:selected').val() == "" ) {
                valid = false
                $(this).addClass('invalidInput');
            }
            else if ( type == 'checkbox' && !$(this).is(':checked') ) {
                valid = false
            }
        });
        
        if ( $(form).find('input[name="actionGoal"]').length && isNaN($(form).find('input[name="actionGoal"]').val())) { 
            valid = false
            $(form).find('input[name="actionGoal"]').addClass('invalidInput');
        }
        
        //        console.log($(form).find('input[name="actionGoal"]').val());
        //        if($(form).find('input[name="actionGoal"]').val() < 100){
        //            valid = false 
        //            $(form).find('input[name="actionGoal"]').addClass('invalidInput');
        //        }
        
        if( valid ) {
            //            $('#SendStuff').show();
            $(form).find('#formError').remove();
        }
        else {
            //            $('#SendStuff').hide();
            $('#SendStuff').before('<div id="formError" class="message"><p>Debe llenar todos los campos marcados como "Obligatorio"</p></div>');
        }
    },
    notificarComentario : function(item) {
        if( js.block_comentarios ){ return js.getBloqueoMessage( 'comentarios' ); }

        var $formulario = $(item).parents('form');
        var autor = $formulario.find('input[name="author"]').val();
        var postId = $formulario.find('#comment_post_ID').length ? $formulario.find('#comment_post_ID').val() : $formulario.find('#comment_post_ID_bottom').val();
        var currenPostType = $(item).attr('data-post_type');
        
        autor = autor ? autor : 0 ;
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'notificarComentario',
                author : autor,
                pid : postId
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                var contentType = "";
                if( currenPostType === 'post' || currenPostType === 'post_acciones' ){
                    contentType = currenPostType === 'post' ? 'Entradas' : 'Acciones';
                    _gaq.push(['_trackEvent', 'Comentarios', 'Comentar', 'Btn_comentario'+ contentType]);
                }
            },
            success: function( data ) {
                $formulario.submit();
            },
            error : function() {}
        });
    },
    verMasPostPortadas : function(item) {
        var imgType = $(item).attr('data-imgtype');
        var postType = $(item).attr('data-postType');
        var offset = $('#postsHolderList > li').length;
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'verMasPostPortadas',
                imgType : imgType,
                postType : postType,
                offset : offset
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).before('<div id="cargandoPosts"></div>');
                $('#cargandoPosts').append(ajaxLoader);
                $('#ajaxLoader').addClass('cargandoPosts');
            },
            success: function( data ) {
                $('#cargandoPosts').remove();
                $('#postsHolderList').append(data);
                $('#postsHolderList > li').equalHeights();
            },
            error : function() {}
        });
    },
    verMasPostAcciones : function(item) {
        var orden = $(item).attr('data-orden');
        var offset = $('#currTab > ul > li').length;
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'verMasPostAcciones',
                offset : offset,
                orden : orden
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).before('<div id="cargandoPosts"></div>');
                $('#cargandoPosts').append(ajaxLoader);
                $('#ajaxLoader').addClass('cargandoPosts');
            },
            success: function( data ) {
                $('#cargandoPosts').remove();
                $('#currTab > ul').append(data);
            },
            error : function() {}
        });
    },
    verMasPostEspeciales : function(item) {
        var offset = $('#listaEspeciales > li').length;
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'verMasPostEspeciales',
                offset : offset
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).before('<div id="cargandoPosts"></div>');
                $('#cargandoPosts').append(ajaxLoader);
                $('#ajaxLoader').addClass('cargandoPosts');
            },
            success: function( data ) {
                $('#cargandoPosts').remove();
                $('#listaEspeciales').append(data);
            },
            error : function() {}
        });
    },
    showMoreUserVotes : function(item) {
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'showMoreUserVotes',
                offset : $(item).prev().children().length,
                usid : $(item).data('usid'),
                logued : $(item).data('logued')
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).prev().css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).prev().css('opacity', '1');
                
                if( data != "" ) {
                    $(item).prev().append(data);
                    $('.evtjs').evt();
                }
                else {
                    $(item).remove();
                }
            },
            error : function() {
                $(item).prev().css('opacity', '1');
            }
        });
    },
    unVoteForAction : function(item ) {
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'unVoteForAction',
                pid : $(item).data('pid'),
                usid : $(item).data('usid')
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).parent().css('opacity', '0.2');
            },
            success: function( data ) {
                $(item).parent().fadeOut(200).promise().done(function(){
                    $(item).parent().remove();
                });
            },
            error : function() {
                $(item).parent().css('opacity', '1');
            }
        });
    },
    cargarPost : function(item){
        
        var category = $(item).attr('data-cat'),    
        offset = $(item).prev().find('li').has('.social-echoes').length || $(item).prev().find('li').has('.article-holder').length || $(item).prev().find('li.count').length,
        orden =  $(item).attr('data-order'),
        postType = $(item).attr('data-type'),
        tab = $(item).attr('data-tab'),
        author = $(item).attr('data-author'),
        user = $(item).attr('data-user');
            
        if(postType != "post_acciones" && user != 'user'){
            offset = (offset * 1) + ($(item).attr('data-offset') * 1);
        }

        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'cargarPostTema',
                offset : offset,
                category : category,
                order : orden,
                postType : postType,
                tab : tab,
                author : author
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                $(item).parent().css('opacity','0.5');
            },
            success: function( data ) {
                $(item).parent().css('opacity','1.0');
                $(item).parent().find('ul.article-list').append(data);

                if ( $('.article-list.vertical > li').length ) {
                    $('.article-list.vertical > li').equalHeights();
                }
                
                if(data == ""){
                    $(item).text('No se han encontrado mas entradas').removeAttr('href').addClass('desactive').removeClass('evt').removeAttr('data-func');
                    
                }
                
            },
            error : function() {}
        });
    },
    validateCharCont : function(){
        //cuanta cuantos caracteres faltan para el limite
        var total_letras = 70;
        $('#postTitle').keyup(function() {
            var longitud = $(this).val().length;
            var resto = total_letras - longitud;

            $('.charCont').html(resto);
            if(resto <= 0){
                $('#postTitle').attr("maxlength", 70);
            }
        });
    },
    autoBanners : function() {
        var currentBanner = $('#bannersCont').find('a.current');
        var nextBanner = $(currentBanner).next().length ? $(currentBanner).next() : $('#bannersCont').find('a:first');
        if( $('#bannersCont').find('a').length > 1 ){
            $(currentBanner).fadeOut(1000).removeClass('current');
            $(nextBanner).fadeIn(1000).addClass('current');
        }
    },
    showPaswordsInput : function(item) {
        $('.changePass').show();
        $(item).remove();
    },
    validateInput : function( $input ){
        var emailRegex = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/,
            value = $input.val(),
            condicion = (function(){
                var inputType = $input.data('inputtype') ? $input.data('inputtype') : $input.attr('type');
                
                switch( inputType ){
                    case 'email' :
                        return value && emailRegex.test( value );
                        break;
                    case 'rut' :
                        $input.val( jQuery.Rut.formatear( value, true ) );
                        return jQuery.Rut.validar( value );
                        break;
                    default :
                        return value;
                        break;
                }               
            }());
                    
        if( condicion ){
            $input.removeClass('invalidInput');
            return true;
        }
        $input.addClass('invalidInput');
        return false;
    },
    
    // municipales!
    apoyarPropuesta : function(item ) {
        if( localStorage.getItem('usuarioApoyo_' + $(item).attr('data-pid') ) ){
            alert('¡Gracias!, pero sólo puedes apoyar esta propuesta una vez');
        }
        else {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'apoyarPropuesta',
                    pid : $(item).data('pid')
                },
                dataType: "json",
                success: function( data ) {
                    var messageSucces = "";
                    
                    localStorage.setItem('usuarioApoyo_' + $(item).attr('data-pid'), 'true');
                    $('#numberOfVotes').find('span').text(data.apoyos);
                    $(item).text('¡Gracias!');
                    
                    $('body').append('<div id="lightBox-wrapper" ></div>');
                    $('#lightBox-wrapper').height($(document).height());
                    $('#lightBox-wrapper').append(ajaxLoader);
                    $('#lightBox-wrapper').fadeIn(1000);
                    $('#ajaxLoader').remove();
                    $('#lightBox-wrapper').append('<div id="lightBox-content" class="registro"></div>');
                    $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
                    
                    messageSucces += '<div id="succsessResponse" class="clearfix inLightBox">';
                    messageSucces += '<h2>¡Gracias!</h2>';
                    messageSucces += '<p>Ayuda a difundir esta Propuesta y compromete a tus candidatos compartiéndola en tus redes sociales</p>';
                    messageSucces += data.socialUl;
                    messageSucces += '</div>';
                    
                    $('#lightBox-content').append(messageSucces);
                    $('#lightBox-content').css({
                        'position' : 'fixed',
                        'top' : ($(window).height()/2) - ($('#lightBox-content').height()/2),
                        'left' : ($(window).width()/2) - ($('#lightBox-content').width()/2),
                        'margin-left' : '0px'
                    });
                    $('.evtjs').evt();
                }
            });
        }
    }, // +1 en single de propuesta
    responderPropuesta : function(item) {
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'responderPropuesta',
                propuestapid : $(item).attr('data-propuestaPid'),
                comunaSlug : $(item).attr('data-comunaSlug'),
                candidatoID : $(item).attr('data-candidatoID')
            },
            dataType: "html",
            beforeSend : function() {
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').fadeIn(1000);
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="publishForm"></div>');
                $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
            },
            success: function( data ) {
                $('#lightBox-content').append(data);
                $('.evtjs').evt();
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }
        });
    },
    publicarRespuesta : function(item) {
        var requeridos = $(item).parent().find('[required]');
        $.each(requeridos , function(i, elm) {
            if( $(elm).val() == '' ){
                $(elm).addClass('invalidInput');
            }
            else {
                $(elm).removeClass('invalidInput');
            }
        });
        if( $(item).parent().find('.invalidInput').length == 0 ){
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_zone&func=publicarRespuesta&'+ $(item).parent().serialize(),
                dataType: "html",
                beforeSend : function() {
                },
                success: function( data ) {
                    $('#succsessResponse').children().fadeOut(300);
                    $('#succsessResponse').append(data);
                    setTimeout(function(){
                        window.location.reload();
                    }, 3000);
                }
            });
        }
    },
    apoyarRespuesta : function(item) {
        if( localStorage.getItem( $(item).attr('data-action') + '_' + $(item).attr('data-pid') ) ){
            alert('¡Gracias!, pero sólo puedes apoyar esta Respuesta una vez');
        }
        else {
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'apoyarRespuesta',
                    metaKey : $(item).attr('data-action'),
                    pid :  $(item).attr('data-pid')
                },
                dataType: "html",
                success: function( data ) {
                    $(item).next().text(data);
                    localStorage.setItem( $(item).attr('data-action') + '_' + $(item).attr('data-pid'), 'true' );
                }
            });
        }
    }, // +1 -1 en respuestas de candidatos
    verRespuestaCompleta : function(item) {
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_zone',
                func : 'verRespuestaCompleta',
                pid :  $(item).attr('data-pid')
            },
            dataType: "html",
            beforeSend : function() {
                $('body').append('<div id="lightBox-wrapper" ></div>');
                $('#lightBox-wrapper').height($(document).height());
                $('#lightBox-wrapper').fadeIn(1000);
                $('#lightBox-wrapper').append('<div id="lightBox-content" class="publishForm"></div>');
                $('#lightBox-content').append('<a href="#" id="cerrarLightbox" class="evtjs" data-func="closeLightBox" ></a>');
            },
            success: function( data ) {
                $('#lightBox-content').append(data);
                $('.evtjs').evt();
                $('html, body').animate({
                    scrollTop: 0
                }, 300);
            }
        });
    },
    spotLightToSpan : function(event) {
        var code = event.keyCode || event.which;
        if( code == 188 && ( $(event.target).val() != ',' && $(event.target).val() != '' ) ) {
            event.preventDefault();
            var tName = $(event.target).val();
            tName = tName.replace(',', '');
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'completePostTags',
                    tagName : tName
                },
                dataType: "html",
                beforeSend: function( xhr ) {
                    $(event.target).css('opacity', '0.2');
                },
                success: function( data ) {
                    $(event.target).css('opacity', '1');
                    $(event.target).val('');
                    $('#postTagsHolder').append(data);
                    $(event.target).removeAttr('required');
                    $('#postTagsHolder').find('.postTagBox').off( 'click' , js.removePostTag );
                    $('#postTagsHolder').find('.postTagBox').on( 'click' , js.removePostTag );
                },
                error : function() {
                    $(event.target).css('opacity', '1');
                }
            });
        }
    },
    spotLightToSpanBlur : function(event) {
        if( $(event.target).val() != ',' && $(event.target).val() != '' ) {
            event.preventDefault();
            var tName = $(event.target).val();
            tName = tName.replace(',', '');
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'completePostTags',
                    tagName : tName
                },
                dataType: "html",
                beforeSend: function( xhr ) {
                    $(event.target).css('opacity', '0.2');
                },
                success: function( data ) {
                    $(event.target).css('opacity', '1');
                    $(event.target).val('');
                    $('#postTagsHolder').append(data);
                    $(event.target).removeAttr('required');
                    $('#postTagsHolder').find('.postTagBox').off( 'click' , js.removePostTag );
                    $('#postTagsHolder').find('.postTagBox').on( 'click' , js.removePostTag );
                },
                error : function() {
                    $(event.target).css('opacity', '1');
                }
            });
        }
    },
    removePostTag : function(event){ 
        $(event.target).parent().find('[data-id="'+ $(event.target).attr('data-id') +'"]').remove();
    },
    showMoreTemas : function(item){
        $(item).prev().find('li').show();
        $(item).remove();
    },
            
    // funciones utilitarias para twitter feed
    parseDate : function( tdate ){
        var system_date = new Date(Date.parse(tdate)),
            user_date = new Date(),
            isIE = navigator.userAgent.match(/MSIE\s([^;]*)/),
            diff;

        if ( isIE ) { system_date = Date.parse(tdate.replace(/( \+)/, ' UTC$1')); }

        diff = Math.floor((user_date - system_date) / 1000);

        if (diff <= 1) {return "hace un instante";}
        if (diff < 20) {return "hace " + diff + " segundos";}
        if (diff < 40) {return "hace medio minuto";}
        if (diff < 60) {return "hace menos de un minuto";}
        if (diff <= 90) {return "hace un minuto y medio";}
        if (diff <= 3540) {return "hace " + Math.round(diff / 60) + " minutos";}
        if (diff <= 5400) {return "hace una hora";}
        if (diff <= 86400) {return "hace " + Math.round(diff / 3600) + " horas";}
        if (diff <= 129600) {return "hace 1 dia";}
        if (diff < 604800) {return "hace " + Math.round(diff / 86400) + " dias";}
        if (diff <= 777600) {return "hace mas de una semana";}

        return system_date;
    },
    parseLinks : function( string ){
        var urlRegexp = /[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g,
            twitterMentionRegexp = /\B@([\w-]+)/gm,
            hashTagRegexp = /[#]+[A-Za-z0-9-_]+/g,
            boxLocation = $('body').hasClass('home') ? 'Caja redes Home' : 'Caja redes acción',
            newString = '';

        newString = string.replace(urlRegexp, function( url ){ return '<a class="ganalytics" data-ga-category="SidebarLink" data-ga_action="LinksHome" data-ga_opt_label="LinksTwitterHome" href="'+ url +'" title="Ver link" rel="nofollow" target="_blank">'+ url +'</a>'; }); 
        newString = newString.replace(twitterMentionRegexp, ' <a class="ganalytics" data-ga-category="SidebarLink" data-ga_action="LinksHome" data-ga_opt_label="LinksTwitterHome" href="http://twitter.com/$1" target="_blank" rel="nofollow" title="Ir al perfil de @$1">@$1</a> ');
        newString = newString.replace(hashTagRegexp, function( hash ){
            var tag = hash.replace("#","%23");
            return '<a class="ganalytics" data-ga-category="SidebarLink" data-ga_action="LinksHome" data-ga_opt_label="LinksTwitterHome" href="http://search.twitter.com/search?q='+ tag +'" title="Ver mas tweets de '+ hash +'" rel="nofollow" target="_blank">'+ hash +'</a>';
        });
        return newString;
    },

    // funciones utilitarias para bloqueos de publicaciones y cosas
    getBloqueoMessage : function( type ){
        var $lightBoxWrapper = $('<div id="lightBox-wrapper" ></div>'),
            $lightBoxContent = $('<div id="lightBox-content" class="publishForm portadas"></div>').css({ 'padding' : '30px' }),
            message;

        message = '<h2>elquintopoder estará en pausa</h2>';
        message += '<p class="content-lightBox">';
        message += 'Este viernes 9 de agosto y hasta la madrugada del sábado 10, <strong>nuestro sitio estará abajo por mantenimiento del servidor.</strong> No te preocupes, no perderemos nada solo <strong>no podrás acceder a los contenidos, ni subir material nuevo.</strong> Te avisaremos por nuestros medios sociales en el minuto exacto en que volvamos arriba. A partir del Jueves 8 de Agosto, a las 16 hrs. <strong>no podrás subir contenidos ni comentar</strong>, porque debemos respaldar la base de datos.';
        message += '</p>';
        message += '<p class="content-lightBox"><strong>Gracias por la paciencia.</strong></p>';
        message += '<a href="#" id="cerrarLightbox" class="evtjs delegated" data-func="closeLightBox" title="Cerrar"></a>';

        $lightBoxWrapper.height( $(document).height() );

        $('body').append( $lightBoxWrapper );
        $lightBoxWrapper.fadeIn(1000);
        $('html, body').animate({ 'scrollTop' : 0 });

        $lightBoxContent.html( message );
        $lightBoxContent.find('.evtjs').evt();
        $lightBoxWrapper.html( $lightBoxContent );
    }
};

$(function(){
    var $currentBody = $('body');

    js.checkContingenciaMessageStatus();

    js.checkBloqueos( $('html').attr('data-bloqueos') );

    setTimeout(function(){ _gaq.push(['_trackEvent', 'No Rebote', 'Visita mayor 40 segundos']); }, 40000);
    
    $('.evt').evt();
    
    if( $('body').hasClass('single') ){
        _gaq.push(['_trackEvent', 'ImpulsarCV', 'temas', '-', 0, true]);
    }
    
    $currentBody.on("click", '.ganalytics', function(){
        if( ! $(this).attr('data-clicked') ){
            _gaq.push(['_trackEvent', $(this).attr('data-ga-category'), $(this).attr('data-ga_action'), $(this).attr('data-ga_opt_label'), ( $(this).attr('data-ga_value') ? parseInt( $(this).attr('data-ga_value'), 10 ) : undefined )]);
        }
    });

    $currentBody.on("click", '.socialganalytics', function(){
        _gaq.push(['_trackSocial', $(this).attr('data-ga_network'), $(this).attr('data-ga_socialaction'), $(this).attr('data-ga_opt_target'), $(this).attr('data-ga_opt_pagePath')]);
    });
    
    
    // resetea el tamaño de los videos destacados en la portada de videos
    if( $('#thumbContainer').length ) {
        $('#thumbContainer :only-child').width( $('#thumbContainer').width() );
        $('#thumbContainer :only-child').height( $('#thumbContainer').height() );
    }
    js.temasRank();
    js.hideTopSection();
    if ($('#comments').length){
        js.orderCommentsReplies();
    }
    if ( $('#showcased-comments ul.article-list > li').length ) {
        $('#showcased-comments ul.article-list > li').equalHeights();
    }
    if ( $('.article-list.vertical > li').length ) {
        $('.article-list.vertical > li').equalHeights();
    }
    $(".carousel-pict-holder iframe").each(function(){
        var ifr_source = $(this).attr('src');
        var wmode = "wmode=transparent";
        if(ifr_source.indexOf('?') != -1) $(this).attr('src',ifr_source+'&'+wmode);
        else $(this).attr('src',ifr_source+'?'+wmode);
        $(this).attr('height', 320);
        $(this).attr('width', 480);
    });
    
    $("#thumbContainer iframe").each(function(){
        var ifr_source = $(this).attr('src');
        var wmode = "wmode=transparent";
        if(ifr_source.indexOf('?') != -1) $(this).attr('src',ifr_source+'&'+wmode);
        else $(this).attr('src',ifr_source+'?'+wmode);
        $(this).attr('height', 320);
        $(this).attr('width', 480);
    });
    
    //funcion para publicar
    $('#publicationForm').ajaxForm({
        delegation : true,
        url : '/wp-admin/admin-ajax.php',
        target : '#lightBox-content',
        iframe : true,
        beforeSubmit : function(arr, form, options){
            var postType = $(form).find('input[name="postType"]').val();
            var offset = $('#lightBox-content').offset();
            var placeholderItems = $(form).find('[placeholder]');
            var cargando = "";
            
            $.each( placeholderItems ,function(index,elm){
                if( $(elm).val() == $(elm).attr('placeholder') ) {
                    $(elm).val("");
                }
            });
            
            window.scrollTo(offset.top,0);
            
            if( postType == 'post' ) {
                cargando += '<div id="ajaxCargando" class="clearfix inLightBox">';
                cargando += '<h2>En estos momentos tu entrada  está siendo subida a nuestros servidores.</h2>';
                cargando += '</div>';
            }
            else if( postType == 'post_fotos' ) {
                cargando += '<div id="ajaxCargando" class="clearfix inLightBox">';
                cargando += '<h2>En estos momentos tu foto está siendo subida a nuestros servidores. En unos instantes quedará publicada.</h2>';
                cargando += '</div>';
            }
            else if( postType == 'post_videos' ) {
                cargando += '<div id="ajaxCargando" class="clearfix inLightBox">';
                cargando += '<h2>En estos momentos el video foto está siendo subido. En unos instantes quedará publicado.</h2>';
                cargando += '</div>';
            }
            else if( postType == 'post_acciones' ) {
                cargando += '<div id="ajaxCargando" class="clearfix inLightBox">';
                cargando += '<h2>En estos momentos tu acción está siendo subida a nuestra comunidad. En unos instantes quedará publicada.</h2>';
                cargando += '</div>';
            }
            
            $('#lightBox-content').find('.inLightBox').fadeOut(500);
            $('#lightBox-content').append(cargando);
            $('#ajaxLoader').addClass('publicando');
        },
        success : function() {
            var offset = $('#lightBox-content').offset();
            window.scrollTo(offset.top,0);
        }
    });
    
    if( $('#carousel').length ){
        global.intervalo = setInterval(function(){
            js.autoCarousel()
        }, 10000);
        $('#carousel').on('mouseenter', function(){
            clearInterval(global.intervalo);
        });
        $('#carousel').on('mouseleave', function(){
            global.intervalo = setInterval(function(){
                js.autoCarousel()
            }, 10000);
        });
    }
    
    if( $('.wp-post-image').length ){
        $('.wp-post-image').removeAttr('width');
        $('.wp-post-image').removeAttr('height');
    }
    $('#lightBox-wrapper').height($(document).height());
    $('#lightBox-wrapper.data-socialPerfil').fadeIn('slow');
    
    $('#actionGoal').bind('keypress', function(e) { 
        return ( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57)) ? false : true ;
    });
    
    if( $('#bannersCont').length ) {
        setInterval(function(){
            js.autoBanners();
        }, 7000);
    }
    
    if( $('.article-list.vertical.candidatos-portada').length ) {
        var $ulCandidatosPortada = $('.article-list.vertical.candidatos-portada');
        var ulCandidatos_parentWidth = $ulCandidatosPortada.parent().innerWidth()
        $ulCandidatosPortada.css({
            'margin-left' : (ulCandidatos_parentWidth/2) - ( $ulCandidatosPortada.width()/2 ) - 20
        });
    }
    
    if( $('.usr-description').length ){
        // fuerza target = _blank en los links de la descripcion del usuario
        // se usa en usuarios.php y acciones-firmadas-php
        // lo mejor seria encontrar un hook, action o filter para la funcion make_clickable() que se usa para obtener estos links
        $('.usr-description').find('a').attr('target', '_blank');
    }
    
    // se inician los feedSociales
    var $feedBox = $('#feeds-box');
    if( $feedBox.length ){
        var keyWord = $feedBox.attr('data-filter') ? $feedBox.attr('data-filter') : 'elquintopoder';
        if( keyWord ){
            keyWord = keyWord.split('#');
            keyWord = keyWord.join("");
            keyWord = keyWord.split('@');
            keyWord = keyWord.join("");
            keyWord = keyWord.split(',');
            keyWord = keyWord.join(" OR ");
        }
        
        $.ajax({
            type: "POST",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'twitter_feed',
                funcion : 'getSearch',
                searchString : keyWord,
                resultsNumber : 30
            },
            dataType: "json",
            success: function( data ) {
                if( data.statuses && data.statuses.length ){
                    var normalizedData = [],
                        $innerList = $feedBox.find('ul').first(),
                        boxLocation = $('body').hasClass('home') ? 'Caja redes Home' : 'Caja redes acción',
                        contador = 0,
                        htmlString = '',
                        display;


                    $.each( data.statuses, function(index, item){
                        var tweetData = {
                            author_name : item.user.screen_name,
                            author_avatar : item.user.profile_image_url,
                            author_url : 'https://www.twitter.com/' + item.user.screen_name,
                            tweet_text : js.parseLinks( item.text ),
                            tweet_time_ago : js.parseDate( item.created_at )
                        };
                        normalizedData.push( tweetData );
                    });

                    $.each( normalizedData, function( index, item ){
                        if( contador >= (normalizedData.length - 9) ){ display = 'class="visible-feed"'; }
                        else { display = 'style="display:none" class="hidden-feed"'; }

                        htmlString += '<li '+ display +' data-item="'+ contador +'" data-type="tweeter" >';
                        htmlString += '<div class="usr-avatar-holder">';
                        htmlString += '<img width="48" height="48" src="'+ item.author_avatar +'" alt="Imagen de '+ item.author_name +'" >';
                        htmlString += '<p class="feed-src twitter">Twitter</p>';
                        htmlString += '</div>';
                        htmlString += '<p>';
                        htmlString += '<a class="gae" data-eventCat="'+ boxLocation +'" data-eventname="link" data-eventvalue="perfil de usuario" href="'+ item.author_url +'" title="Ver el perfil de '+ item.author_name +'" rel="external">'+ item.author_name +'</a>: ';
                        htmlString += item.tweet_text;
                        htmlString += '<span class="time-ago">'+ item.tweet_time_ago +'</span>';
                        htmlString += '</p>';
                        htmlString += '</li>';

                        contador++;
                    });

                    $innerList.html( htmlString );

                    setInterval(function(){
                        $feedBox.find('li.hidden-feed').last()
                            .slideDown()
                            .removeClass('hidden-feed')
                            .addClass('visible-feed');
                    }, 7000);
                }
                
            }
        });
    }
});

(function($) {
    $.fn.evt = function(){
        $.each(this,function(i,item) {
            var $item = $(item),
                func = $item.attr('data-func'),
                event = $item.attr('data-event') ? $item.attr('data-event') : 'click';
                
            if( func && $.isFunction( js[ func ] ) ){
                $item.on( event, function( e ){
                    if( $item.attr('data-noprevent') !== 'true' ) { e.preventDefault(); }
                    js[ func ].call( this, $item, e );
                });
                $item.addClass('delegated');
            }
            
        });
    }
    
    $.fn.iePlaceHolder = function(){
        return this.each(function(){
            if( !Modernizr.input.placeholder ) {
                var esto = $(this);
                var placeholder = $(esto).attr('placeholder');
                
                $(esto).val(placeholder);
                
                $(this).blur(function(){
                    if( $(esto).val() == '' || $(esto).val() == placeholder ) {
                        $(esto).val(placeholder);
                    }
                });
                
                $(this).click(function(){
                    if( $(esto).val() == '' || $(esto).val() == placeholder ) {
                        $(esto).val('');
                    }
                });
            }
        });
    }
    $.fn.equalHeights = function(minHeight, maxHeight) {
        var tallest = (minHeight) ? minHeight : 0;
        this.each(function() {
            if($(this).height() > tallest) {
                tallest = $(this).height();
            }
        });
        if((maxHeight) && tallest > maxHeight) tallest = maxHeight;
        return this.each(function() {
            $(this).height(tallest);
        });
    }
    $.fn.easywysiwyg = function(){
        var textarea;
        var editableBox;
        return this.each(function(){
            textarea = $(this);
            $(textarea).hide();
            
            addEditableBox(textarea);
            
            editableBox = $('#editableBox');
            
            $('.edBoxBtn').on('click', doAction);
            
            $(editableBox).on('blur keydown mouseup focus keyup', function(){
                $(textarea).val( $(this).html() );
            });
            
            $(editableBox).on('paste', function(e){
                //                var alert = "";
                //                alert += '<div class="message alert">';
                //                alert += '<p>Cada vez que copias y pegas contenido desde un procesador de texto (Microsoft Word), éste viene con código basura, para que el contenido de tu entrada se visualice correctamente debes hacer click en el botón "limpiar código" antes de comenzar a estilizar tu contenido</p>';
                //                alert += '</div>';
                //                
                //                $('#edBoxHolder').find('.message').remove();
                //                $('#edBoxButtons').before(alert);
                
                //                setTimeout(function(){
                //                    var edHtml = $('#editableBox').text().replace(/(\r\n|\n|\r)/gm,"");
                //                    $('#editableBox').html(edHtml);
                //                }, 150);
                
                });
            
        });
        
        function doAction(e) {
            e.preventDefault();
            var action = $(this).data('action');
            var txt = '';
            var txtString;
            var domEditableBox = document.getElementById('editableBox');
            
            if( action == 'createlink' ){
                if (window.getSelection) {
                    txt = window.getSelection();
                }
                else if (document.getSelection) {
                    txt = document.getSelection();
                }
                else if (document.selection) {
                    txt = document.selection.createRange().text;
                }
                
                if( txt != "" && txt != null && txt != false && txt != undefined ) {
                    var urlText = prompt("Ingresa la dirección:");
                    var linkOut;
                    txtString = txt.toString();
                    
                    if( urlText.substr( 0 , 7 ) == 'http://' || urlText.substr( 0 , 7 ) == 'https:/'  ) {
                        urlText = urlText;
                    }
                    else {
                        urlText = 'http://'+ urlText;
                    }
                    
                    linkOut = '<a href="'+ urlText +'" title="'+ txtString +'" rel="external">'+ txtString +'</a>';
                    document.execCommand("inserthtml", false, linkOut);
                    
                } else {
                    alert("Debes seleccionar texto para continuar");
                }
            }
            else if ( action == 'clean' ) {
                var edHtml = $('#editableBox').text().replace(/(\r\n|\n|\r)/gm,"");
                $('#editableBox').html(edHtml);
            }
            else { 
                document.execCommand(action, false, null); 
            }
            
            $(textarea).val( $('#editableBox').html() );
        }
        
        function addEditableBox(element) {
            var newEditableBox = "";
            newEditableBox += '<div id=edBoxHolder>';
            newEditableBox += '<ul id="edBoxButtons">';
            newEditableBox += '<li><a href="#" class="edBoxBtn bold" data-action="bold" title="Negrita">Bold</a></li>';
            newEditableBox += '<li><a href="#" class="edBoxBtn italic" data-action="italic" title="Italica">Italic</a></li>';
            newEditableBox += '<li><a href="#" class="edBoxBtn lista" data-action="insertunorderedlist" title="Lista">Lista</a></li>';
            newEditableBox += '<li><a href="#" class="edBoxBtn link" data-action="createlink" title="Hipervinculo">Link</a></li>';
            //            newEditableBox += '<li class="cleanBtn"><a href="#" class="edBoxBtn clean" data-action="clean" title="Limpiar Código">Limpiar Código</a></li>';
            newEditableBox += '</ul>';
            newEditableBox += '<div id="editableBox" contenteditable="true">';
            newEditableBox += '</div>';
            newEditableBox += '</div>';
            
            $(element).after(newEditableBox);
        }
        
    }
})($);



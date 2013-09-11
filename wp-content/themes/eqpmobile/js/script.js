(function(window, $, undefined) {
    // saca la altura mas alta del set de elementos.
    $.fn.heighestHeight = function(){ return Math.max.apply(null, ($(this).map(function(){ return $(this).height(); }).get())); };
    
    window.SiteHandler = function() {
        this.$body = $('body');

        this.postHeaderOffset = $('#mainHeader').next().offset();
        this.alterNav = $('#alterNav');
        
        this.mqSetups();
        this.formsHandler();
        $(window).on( 'orientationchange resize', {siteHandler : this}, this.onResize );
        $(window).on( 'scroll', {siteHandler : this}, this.onScroll );
        // manejadores de eventos
        this.autoHandle( $('.evt') ); 
        this.analyticsHandler();
        
        this.detectWidth( '.destaSlides', 'li' );
        this.detectWidth( '#slideEntradas ul', 'li' );
        
        if( $('#sobreeqp-nav').length ){ this.sobreEqpController(); }
        if( $('#user-content-type-buttons').length ){ this.perfilDeUsuarioController(); }
        
        this.sliderSetsHandler();
        
        this.svgFallback( $('[data-svgfallBack], [data-svgFallBack]') );

        this.checkBloqueos( $('html').attr('data-bloqueos') );
    };
    window.SiteHandler.prototype = {
        svgFallback : function( $elements ){
            if( ! Modernizr.svg ){
                $elements.each(function(index, elm){ $(elm).attr('src', $(elm).attr('data-svgfallBack') ); });
            }
        },
        autoHandle: function( $elements ) {
            if ( ! $elements.length ) { return; }
            var esto = this;
            $elements.each(function(i, elem) {
                var $item = $(elem),
                    func = $item.attr('data-func'),
                    event = $item.attr('data-event') || 'click.evt';
                if ( func && $.isFunction(esto[ func ]) && ! $item.hasClass('.handled-element') ) {
                    $item.on(event, $.proxy(esto[ func ], esto));
                    $item.addClass('handled-element');
                }
            });
        },
        reHandle : function( $elements ){
            $elements.each(function(i, elem){
                var $item = $(elem);
                $item.removeClass('handled-element');
                $item.off( ($item.data('event') || 'click.evt') );
            });
            this.autoHandle( $elements );
        },
        analyticsHandler : function(){
            var self = this;
            self.$body.on('click.eqp', '.track-action', function( event ){
                // se devuelve si es un click falso
                if( event.isTrigger ){ return true; } 

                // se comienza
                var $item = $(this);

                if( $item.hasClass('track-social') ){
                    _gaq.push(['_trackSocial', $item.attr('data-ga_network'), $item.attr('data-ga_socialaction'), $item.attr('data-ga_opt_target'), $item.attr('data-ga_opt_pagePath')]);
                }
                else {
                    _gaq.push(['_trackEvent', $item.attr('data-ga-category'), $item.attr('data-ga_action'), $item.attr('data-ga_opt_label'), ( $item.attr('data-ga_value') ? parseInt( $item.attr('data-ga_value'), 10 ) : undefined )]);
                }
            });
        },
        mqSetups : function(){
            var siteHandler = this;
            // para setear cosas con media queries
            //tablets horizontal y vertical y desktop
            if( Modernizr.mq('only screen and (min-width : 641px)') ){
                setTimeout(function(){
                    siteHandler.equalizeHeights( $('[data-equializeHeights="sliderEntradas"]') );
                    siteHandler.equalizeHeights( $('[data-equalize="featuredTitles"]') );
                }, 2000);
            }
            
            // tablets vertical
            if( Modernizr.mq('only screen and (max-width : 800px) and (min-width : 641px)') ){
                setTimeout(function(){
                    var $smallFotos = $('figure.smallFoto').find('img'),
                        $bigFotos = $('figure.bigFoto').find('img'),
                        bigFotoHighestHeight = $bigFotos.heighestHeight();
                        
                    $bigFotos.height( bigFotoHighestHeight );
                    $smallFotos.height( bigFotoHighestHeight / 2 );
                },2000);
            }
        },
        formsHandler : function(){
            if( $('#main-contact-form').length || $('#commentform').length ){
                Modernizr.load([{
                    load: $('html').attr('data-relUrl') + '/js/validizr.js',
                    complete: function () {
                        $('#main-contact-form').validizr({
                        validFormCallback : function( $formulario ){
                            $.ajax({
                                type: "POST",
                                url: '/wp-admin/admin-ajax.php',
                                data: 'action=mobile_ajax&func=sendContactForm&' + $formulario.serialize(),
                                dataType: "html",
                                beforeSend : function( xhr ){ $formulario.css('opacity', '0.2'); },
                                success: function( data ) { $formulario.parent().html( data ); }
                            });
                        }
                        }); 
                        $('#commentform').validizr({
                            validFormCallback : function( $formulario ){
                                var autor = $formulario.find('input[name="author"]').val(),
                                    postId = $('#comment_post_ID').val(),
                                    gacAction = $formulario.attr('data-gac_action');
                                    
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
                                    success: function( data ) { 
                                        _gaq.push(['_trackEvent', 'Comentarios', 'Comentar', gacAction]);
                                        $formulario.submit(); 
                                    }
                                });
                            }
                        });
                    }
                }]);
            }
        },
        detectWidth : function( parentSelector, itemSelector ){
            var count = $( parentSelector + ' ' + itemSelector ).length;
            $( parentSelector ).css("width", 100 * count + "%");
            $( parentSelector + ' ' + itemSelector ).css("width", 100.001 / count + "%");
        },
        equalizeHeights: function( $elements ) {
            if ( ! $elements.length ) { return; }
            var heightArray = [],
                maxValue;
            $elements.height('auto');
            $.each($elements, function(index, elm) { heightArray.push($(elm).height()); });
            maxValue = Math.max.apply(Math, heightArray);
            $elements.height( maxValue );
        },
        setupSliders : function( idString, customParams ){
            var $slider = $('#' + idString),
                $mainSlider_controls = $( '#' + $slider.data('control') ),
                params = customParams ? customParams : {
                    callback : function(index) {
                        $mainSlider_controls.find('a.active').removeClass('active');
                        $mainSlider_controls.find('li').eq( index ).find('a').addClass('active');
                    }
                };
                
            $slider.Swipe(params);
            $slider.addClass('isSlider').data('params', params);
            this[ idString ] = $slider.data('Swipe');
        },
        sliderSetsHandler : function(){
            var esto = this,
                $body = $('body'),
                isBigDevice = Modernizr.mq('only screen and (min-width : 641px)');
                
            if( $body.hasClass('home') ){
                var $slideHomeControls = $('#slideHomeControl');
                
                this.setupSliders( 'slideHome', {
                    auto : 4000,
                    continuous : true,
                    callback : function(index) {
                        $slideHomeControls.find('a.active').removeClass('active');
                        $slideHomeControls.find('li').eq( index ).find('a').addClass('active');
                    }
                });
                if( isBigDevice ){
                    this.setupSliders( 'slideEntradas' );
                }
            }
            else if( $body.hasClass('portada-entradas') ) {
                if( isBigDevice ){
                    esto.makeSwiperLoader('sliderPortadaEntradas_recientes', function( args ){
                        return {
                            type: "POST",
                            url: '/wp-admin/admin-ajax.php',
                            data: {
                                action : 'mobile_ajax',
                                func : 'load_more_slider',
                                pType : 'post',
                                orderType : 'recientes',
                                offset_start : args.$slider.attr('data-offset-start'),
                                offset_end : args.$slider.attr('data-offset-end'),
                                edgeType : args.edgeType,
                                noMorePost : args.morePost
                            },
                            dataType: "json",
                            beforeSend : function() {
                                args.$slider.find('li').css('opacity', '0.2');
                                args.$slider.removeClass('ready-to-rumble first last').addClass('loading-ajax');
                                args.$slider.data('lastCall', false);
                            },
                            success : function( data ) {
                                if( data.status === 'noBack' ){
                                    args.$slider.find('li').css('opacity', '1');
                                    args.$slider.removeClass('loading-ajax');
                                    return;
                                }

                                var swipeObj = args.$slider.data('Swipe');

                                // resetea las alturas
                                setTimeout(function(){ esto.equalizeHeights( args.$slider.find('article') ); }, 500);

                                args.$itemsHolder.html( data.html );
                                args.$slider.attr({
                                    'data-offset-start' : data.new_offset_start,
                                    'data-offset-end' : data.new_offset_end
                                });
                                args.$slider.data('edge', false);
                                args.$slider.data('lastCall', false);
                                if( data.status === 'noPostLeft' ){ args.$slider.data('noPostLeft', true); }

                                swipeObj.setup();
                                swipeObj.slide(0);

                                args.$slider.css('opacity', '1').removeClass('loading-ajax');
                            },
                            error : function() { args.$slider.css('opacity', '1'); }
                        };
                    });
                    esto.makeSwiperLoader('sliderPortadaEntradas_activas', function( args ){
                        return {
                            type: "POST",
                            url: '/wp-admin/admin-ajax.php',
                            data: {
                                action : 'mobile_ajax',
                                func : 'load_more_slider',
                                pType : 'post',
                                orderType : 'activos',
                                offset_start : args.$slider.attr('data-offset-start'),
                                offset_end : args.$slider.attr('data-offset-end'),
                                edgeType : args.edgeType,
                                noMorePost : args.morePost
                            },
                            dataType: "json",
                            beforeSend : function() {
                                args.$slider.find('li').css('opacity', '0.2');
                                args.$slider.removeClass('ready-to-rumble first last').addClass('loading-ajax');
                                args.$slider.data('lastCall', false);
                            },
                            success : function( data ) {
                                if( data.status === 'noBack' ){
                                    args.$slider.find('li').css('opacity', '1');
                                    args.$slider.removeClass('loading-ajax');
                                    return;
                                }

                                var swipeObj = args.$slider.data('Swipe');
                                
                                setTimeout(function(){ esto.equalizeHeights( args.$slider.find('article') ); }, 500);
                                if( data.html ){
                                    args.$itemsHolder.html( data.html );
                                    args.$slider.attr({
                                        'data-offset-start' : data.new_offset_start,
                                        'data-offset-end' : data.new_offset_end
                                    });
                                }
                                
                                args.$slider.data('edge', false);
                                args.$slider.data('lastCall', false);
                                if( data.status === 'noPostLeft' ){ args.$slider.data('noPostLeft', true); }

                                swipeObj.setup();
                                swipeObj.slide(0);

                                args.$slider.css('opacity', '1').removeClass('loading-ajax');
                                args.$slider.find('li').css('opacity', '1');
                            },
                            error : function() { args.$slider.css('opacity', '1'); }
                        };
                    });
                }
            }
            else if( $body.hasClass('portada-fotos') || $body.hasClass('portada-videos') ) {
                $('.slideImage iframe').attr('width', '100%');
                if( isBigDevice ){
                    this.setupSliders( 'sliderPortadaMedia_activos' );
                    this.setupSliders( 'sliderPortadaMedia_recientes' );
                }
            }
            else if( $body.hasClass('portada-acciones') ) {
                if( isBigDevice ){
                    this.setupSliders( 'slide_acciones_adherencia' );
                    this.setupSliders( 'slide_acciones_recientes' );
                }
            }
            else if( $body.hasClass('single-especiales') ){
                if( isBigDevice ){
                    this.setupSliders( 'especial_entradas_swipe' );
                    this.setupSliders( 'especial_fotos_swipe' );
                    this.setupSliders( 'especial_videos_swipe' );
                }
            }
        },
        makeSwiperLoader : function( idString, ajaxGetParams ){
            var esto = this,
                $slider = $('#' + idString),
                $mainSlider_controls = $( '#' + $slider.data('control') );

            esto.setupSliders( idString , {
                callback : function(index) { // se ejecuta al pasar un slide
                    $mainSlider_controls.find('a.active').removeClass('active');
                    $mainSlider_controls.find('li').eq( index ).find('a').addClass('active');

                    $slider.removeClass('ready-to-rumble first last');
                },
                onMove : function( index, delta, item ){ // se ejecuta en el movimiento
                    var itemCount = $(item).siblings().length,
                        currentRelativePos = index === 0 ? 'first' : index === itemCount ? 'last' : false;

                    if( ( delta > 150 && currentRelativePos === 'first' ) || ( delta < -150 && currentRelativePos === 'last' ) ){
                        $slider.data('lastCall', true);
                        $slider.data('edge', currentRelativePos);
                        $slider.addClass('ready-to-rumble ' + currentRelativePos);
                    }
                    else {
                        $slider.data('lastCall', false);
                        $slider.removeClass('ready-to-rumble first last');
                    }
                },
                edgeIndexCallback : function( index, item ){ // se ejecuta si se mueve la primera o la ultima slide
                    if( ! $slider.data('lastCall') ){ return; }

                    var edge = $slider.data('edge'),
                        $itemsHolder = $(item).parents('ul'),
                        morePost = $slider.data('noPostLeft') ? 1 : 0;

                    if( edge === 'first' ) {
                        $slider.data('noPostLeft', false);
                        morePost = 0;
                    }
                    

                    $.ajax((ajaxGetParams({
                        $slider : $slider,
                        $itemsHolder : $itemsHolder,
                        edgeType : edge,
                        noMorePost : morePost
                    })));
                }
            });
        },
        getLightBox : function( lbType, noScroll ){
            var $lightBox_bg = $('<div />').attr({ 'id' : 'lightbox', 'class' : 'lightbox-holder '+ ( lbType ? lbType : '' ) }).css({ 'opacity' : '0' }),
                $lightBox_content = $('<div />').attr({ 'id' : 'lightbox-content', 'class' : 'lightbox_content_box' }).css({ 'opacity' : '0' }),
                $closeBtn = $('<button />').attr({
                    'class' : 'lb-close-btn',
                    'data-func' : 'closeLightBox',
                    'title' : 'Cerrar'
                });
             
            $lightBox_bg.css({
                'width' : '100%',
                'height' : $(window.document).height()
            });
            
            if( noScroll ){ // si no hy scroll hay que centrar verticalmente la caja de contenido
                $lightBox_content.css({
                    'top' : $(window).scrollTop() + 60
                });
            }
            
            $('body').append( $lightBox_bg );
            $lightBox_bg.append( $lightBox_content );
            $lightBox_content.append( $closeBtn );   
            
            this.prevScrollTop = $(window).scrollTop();
            
            this.autoHandle( $closeBtn );
            
            // setTimeout con 1ms, es un hack para ejecutar el código de forma asincronica con el thread principal. sirve para gatillar animaciones de css mas facilmente
            setTimeout(function(){
                $lightBox_bg.css('opacity', '1');
                if( ! noScroll ) { $('html, body').animate({ 'scrollTop' : 0 }, 500); } // para ver si hace scroll o no
            }, 0);
            
            return $lightBox_content;
        },
        sobreEqpController : function(){
            if( Modernizr.mq('only screen and (min-width : 641px)') ){
                var $navi = $('#sobreeqp-nav'),
                    naviOffset = $('#sobreeqp-nav').offset(),
                    parentWidth = $navi.parent().width(),
                    $window = $(window);

                $window.on('scroll.sobreeqp', function(){
                    var windowScrollTop = $window.scrollTop();
                        if( windowScrollTop >= naviOffset.top ){
                            if( ! $navi.hasClass('fixed-nav') ){
                                $navi.width( parentWidth );
                                $navi.addClass('fixed-nav');
                            }
                        } else {
                            if( $navi.hasClass('fixed-nav') ){ $navi.removeClass('fixed-nav').removeAttr('style');  }
                        }
                });

                $("aside.aboutNav").css("height", $("#contAbout").height() + 'px' ); 

                Modernizr.load([{
                    load: $('html').attr('data-relUrl') + '/js/scrollNav.js',
                    complete: function () {  $('#sobreeqp-nav').find('a').scrollNav({ threshold : 60 }); }
                }]);
            } else {
                $('.long-list-title').on('click', function( event ){
                    event.preventDefault();
                    
                    var $item = $(this),
                        $contentBox = $(this).next();
                    
                    if( $contentBox.hasClass('uncollapsed') ){
                        $contentBox.removeClass('uncollapsed');
                        $item.removeClass('uncollapsed');
                    }
                    else {
                        $contentBox.addClass('uncollapsed');
                        $item.addClass('uncollapsed');
                    }
                });
            }
        },
        perfilDeUsuarioController : function(){
            var $scrollerBox = $('#user-content-type-buttons'),
                $window = $(window),
                $loadMoreBtn = $('#see-more-content'),
                windowHeight = $window.height(),
                parentWidth = $scrollerBox.parent().width(),
                scrollerOffset = $scrollerBox.offset();
            
            $window.on('scroll.perfil', function(){
                var windowScrollTop = $window.scrollTop();
                
                if( windowScrollTop >= scrollerOffset.top ){
                    if( ! $scrollerBox.hasClass('fixed') ){
                        $scrollerBox.parent().addClass('fixed');
                        $scrollerBox.width( parentWidth );
                        $scrollerBox.addClass('fixed');
                    }
                } else {
                    if( $scrollerBox.hasClass('fixed') ){
                        $scrollerBox.removeClass('fixed').removeAttr('style');
                        $scrollerBox.parent().removeClass('fixed');
                    }
                }
                
                if( windowScrollTop == ( $(window.document).height() - windowHeight ) ){ $loadMoreBtn.trigger('click'); }
            });
        },
        esRut : function( texto ){
            var tmpstr = "",
                i = 0,
                j = 0,
                cnt = 0,
                invertido = "",
                dtexto = "",
                largo;
        
            for ( i = 0; i < texto.length ; i++ ){ 
                if ( texto.charAt(i) != ' ' && texto.charAt(i) != '.' && texto.charAt(i) != '-' ){
                    tmpstr = tmpstr + texto.charAt(i);
                }
            }
            texto = tmpstr;   
            largo = texto.length;   

            if ( largo < 2 ){ return false; }   

            for ( i = 0; i < largo ; i++ ) {         
                if ( texto.charAt(i) !="0" && texto.charAt(i) != "1" && texto.charAt(i) != "2" && texto.charAt(i) != "3" && texto.charAt(i) != "4" && texto.charAt(i) != "5" && texto.charAt(i) != "6" && texto.charAt(i) != "7" && texto.charAt(i) != "8" && texto.charAt(i) != "9" && texto.charAt(i) != "k" && texto.charAt(i) != "K" ) {         
                    return false;      
                }   
            }   

            for ( i = (largo-1),j = 0; i >= 0; i--,j++ ) { invertido = invertido + texto.charAt(i); }   
            dtexto = dtexto + invertido.charAt(0);   
            dtexto = dtexto + '-';   
            cnt = 0;   

            for ( i = 1,j = 2; i < largo; i++,j++ ) {      
                if ( cnt == 3 ) {         
                    dtexto = dtexto + '.';         
                    j++;         
                    dtexto = dtexto + invertido.charAt(i);         
                    cnt = 1;      
                }      
                else {            
                    dtexto = dtexto + invertido.charAt(i);         
                    cnt++;      
                }   
            }   

            invertido = "";   
            for ( i = (dtexto.length-1) ,j = 0; i >= 0; i--,j++ ) { invertido = invertido + dtexto.charAt(i); }

            if ( this.revisarDigito(texto) ) { return true; }  

            return false;
        },
        revisarDigito : function( componente ){
            var crut =  componente,
                largo = crut.length,
                dvr = '0',
                rut,
                dv,
                suma,
                mul,
                res,
                dvi,
                i;   

            if ( largo < 2 ) { return false; }   
            if ( largo > 2 ) { rut = crut.substring(0, largo - 1); }
            else { rut = crut.charAt(0); }

            dv = crut.charAt(largo-1);   

            if ( dv != '0' && dv != '1' && dv != '2' && dv != '3' && dv != '4' && dv != '5' && dv != '6' && dv != '7' && dv != '8' && dv != '9' && dv != 'k'  && dv != 'K') {      
                return false;   
            }      

            if ( rut == null || dv == null ) { return 0; } 

            suma = 0;
            mul  = 2;  

            for ( i = rut.length -1 ; i >= 0; i-- ) {   
                suma = suma + rut.charAt(i) * mul;
                if (mul == 7) { mul = 2; }     
                else { mul++; }   
            }   
            res = suma % 11;

            if ( res == 1 ) { dvr = 'k'; }
            else if ( res == 0 ) { dvr = '0'; }
            else {      
                dvi = 11-res;
                dvr = dvi + "";
            }
            if ( dvr != dv ) { return false; }

            return true
        },
        checkBloqueos : function( blocksString ){
            if( !blocksString ){ return; }

            var userBlocks = blocksString.split(' ');
            this.block_publicaciones = userBlocks.indexOf('publicaciones') > -1 ? true : false;
            this.block_firmas = userBlocks.indexOf('firmas') > -1 ? true : false;
            this.block_comentarios = userBlocks.indexOf('comentarios') > -1 ? true : false;
            this.block_registros = userBlocks.indexOf('registros') > -1 ? true : false;
            this.block_usuarios = userBlocks.indexOf('usuarios') > -1 ? true : false;
        },
        getBloqueoMessage : function( type ){
            var $lightbox = this.getLightBox(),
                message;

            message = '<h2 class="lightbox-title">elquintopoder estará en pausa</h2>';
            message += '<p class="mensajes-text" >';
            message += 'Este viernes 9 de agosto y hasta la madrugada del sábado 10, <strong>nuestro sitio estará abajo por mantenimiento del servidor.</strong> No te preocupes, no perderemos nada solo <strong>no podrás acceder a los contenidos, ni subir material nuevo.</strong> Te avisaremos por nuestros medios sociales en el minuto exacto en que volvamos arriba. A partir del Jueves 8 de Agosto, a las 16 hrs. <strong>no podrás subir contenidos ni comentar</strong>, porque debemos respaldar la base de datos.';
            message += '</p>';
            message += '<p class="mensajes-text" ><strong>Gracias por la paciencia.</strong></p>';

            $lightbox.append( message );
            $lightbox.css('opacity', '1');
        },
        
        ///////////////////////////////////////////////////////// Delegaciones
        closeHoldingDiv :  function( event ){
            event.preventDefault();

            var contingenciaStatus = {
                    status : 'cerrado',
                    lastTime : $.now() / 1000
                };

            $(event.currentTarget).parent().remove();
            localStorage.setItem('contingencia_status', JSON.stringify( contingenciaStatus ));
        },
        mainSliderControls : function( event ){
            event.preventDefault();
           $('#slideHome').data('Swipe').slide( parseInt( $( event.currentTarget ).data('index') ) );
        },
        closeLightBox : function( event ){
            event.preventDefault();
            event.stopPropagation();
            $('#lightbox').on('webkitTransitionEnd mozTransitionEnd oTransitionEnd msTransitionEnd transitionend', function(e){ $(this).remove(); });
            $('#lightbox').css('opacity', '0');
            $('html, body').animate({ 'scrollTop' : this.prevScrollTop }, 300);
            $(window).off('orientationchange.lightbox resize.lightbox');
        },
        onResize : function( event ){ // corre de forma un poco distinta, this = window;
            var siteHandler = event.data.siteHandler,
                $smallFotos = $('figure.smallFoto').find('img'),
                $bigFotos = $('figure.bigFoto').find('img'),
                heighestHeight;
        
            $("aside.aboutNav").css("height", $("#contAbout").height() + 'px' );

            $bigFotos.removeAttr('style');
            $smallFotos.removeAttr('style');

            //tablets vertical
            if( Modernizr.mq('only screen and (max-width : 801px) and (min-width : 641px)') ){
                heighestHeight = $bigFotos.heighestHeight();
                $bigFotos.height( heighestHeight );
                $smallFotos.height( (heighestHeight / 2) );
            }
            if( Modernizr.mq('only screen and (min-width : 641px)') ){
                siteHandler.equalizeHeights( $('[data-equializeHeights="sliderEntradas"]') );
                siteHandler.equalizeHeights( $('[data-equalize="featuredTitles"]') );
            }
        },
        onScroll : function( event ){
            var siteHandler = event.data.siteHandler,
                scrollPos = $(window).scrollTop();
        
            if( scrollPos > siteHandler.postHeaderOffset.top ){ siteHandler.alterNav.removeClass('hidden-nav'); }
            else { siteHandler.alterNav.addClass('hidden-nav'); }
        },
        anadirShares : function( event ){
            var $item = $(event.currentTarget),
                provider = $item.data('provider'),
                pid = $item.data('pid');
                
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
                success : function(data) { console.log( provider + ' count: ' + data ); }
            });
        },
        notificarComentario : function( event ){
            event.preventDefault();

            if( this.block_comentarios ){ return this.getBloqueoMessage( 'comentarios' ); }
            
            var autor = $('#commentform').find('input[name="author"]').val(),
                postId = $('#comment_post_ID').val();

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
                beforeSend: function( xhr ) {},
                success: function( data ) {
                    $('#commentform').submit();
                },
                error : function() {}
            });
        },
        logOut : function( event ) {
            event.preventDefault();
            var $item = $( event.currentTarget );
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'ajax_zone',
                    func : 'logOut'
                },
                dataType: "html",
                success: function( data ) { window.location.reload(); }
            });
        },
        marcar_no_leido : function( event ){
            event.preventDefault();
            var siteHandler = this,
                $item = $( event.currentTarget ),
                pid = $item.data('pid'),
                userID = $item.data('user');
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'marcar_no_leido',
                    post_id : pid,
                    user_id : userID
                },
                dataType: "json",
                beforeSend : function(){ $item.css('opacity', '0.2'); },
                success: function( data ) {
                    $item
                    .css('opacity', '1')
                    .text('Marcar leído')
                    .attr({
                        'data-func' : 'marcar_leido',
                        'title' : 'Marcar esto como leído'
                    }); 
                    
                    siteHandler.reHandle( $item );
                }
            });
        },
        marcar_leido : function( event ){
            event.preventDefault();
            var siteHandler = this,
                $item = $( event.currentTarget ),
                pid = $item.data('pid'),
                userID = $item.data('user');
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'marcar_leido',
                    post_id : pid,
                    user_id : userID
                },
                dataType: "json",
                beforeSend : function(){ $item.css('opacity', '0.2'); },
                success: function( data ) {
                    $item
                    .css('opacity', '1')
                    .text('Leer después')
                    .attr({
                        'data-func' : 'marcar_no_leido',
                        'title' : 'Agregar a la lista de lectura'
                    }); 
                    
                    siteHandler.reHandle( $item );
                }
            });
        },
        verFotoCompleta : function( event ){
            event.preventDefault();
            
            var siteHandler = this,
                $item = $(event.currentTarget),
                imgID = $item.data('imgid'),
                imgTitle = $item.data('imagetitle'),
                $lightBox;
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'verFotoCompleta',
                    imageID : imgID,
                    imageTitle : imgTitle
                },
                dataType: "json",
                beforeSend : function(){ $lightBox = siteHandler.getLightBox(); },
                success: function( data ) {
                    console.log( data );
                    $lightBox.prepend( data.html );
                    $lightBox.css('opacity', '1');
                }
            });
        },
        contactarUsuario : function( event ) {
            event.preventDefault();
            
            var siteHandler = this,
                $item = $(event.currentTarget),
                usid = $item.data('usid'),
                $lightBox;
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'contactarUsuario',
                    user_id : usid
                },
                dataType: "html",
                beforeSend : function(){ $lightBox = siteHandler.getLightBox(); },
                success: function( data ) {
                    $lightBox.prepend( data );
                    Modernizr.load({
                        test : $.fn.validizr,
                        nope : $('html').attr('data-relUrl') + '/js/validizr.js',
                        complete : function(){
                            $lightBox.find('form').validizr({
                                validFormCallback : function( $formulario ){
                                    $.ajax({
                                        type: "POST",
                                        url: '/wp-admin/admin-ajax.php',
                                        data: 'action=mobile_ajax&func=enviarContactoaUsuario&' + $formulario.serialize(),
                                        dataType: "html",
                                        beforeSend : function(){
                                            $('html, body').animate({ 'scrollTop' : 0 });
                                            $formulario.css('opacity', '0.2');
                                        },
                                        success: function( data ) {
                                            $formulario.remove();
                                            $lightBox.prepend(data);
                                        }
                                    });
                                }
                            });
                        }
                    });

                    $lightBox.css('opacity', '1');
                }
            });
        },
        loadUserActivityType : function( event ){
            event.preventDefault();
            
            var $item = $(event.currentTarget),
                $contentHolder = $('#user-content-holder'),
                $loader = $('#see-more-content'),
                contentType = $item.data('contenttype'),
                usid = $item.data('usid');
                
            if( $item.hasClass('active') ){ return; }
            if( contentType !== 'publicado' ){
                $loader.data('inactiveBtn', true);
                $loader.addClass('hidden-btn');
            } else {
                $loader.data('inactiveBtn', false);
                $loader.removeClass('hidden-btn');
            }
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'loadUserActivityType',
                    user_id : usid,
                    content_type : contentType
                },
                dataType: "json",
                beforeSend : function(){ $contentHolder.css('opacity', '0.2'); },
                success: function( data ) {
                    if( data.status === 'ok' ){ // en este caso siempre es "ok" pero puede servir eventualmente
                        $item.addClass('active');
                        $item.siblings().removeClass('active');
                        $('#user-content-title').text( data.typeTitle );
                        $('#user-content-list').html( data.html );
                        $('html, body').animate({ 'scrollTop' : ($contentHolder.offset().top - 40) }, 500);
                    }
                    $contentHolder.css('opacity', '1');
                }
            });
        },
        loadMoreUserContent : function( event ){
            event.preventDefault();
            
            var $item = $(event.currentTarget),
                usid = $item.data('usid'),
                offset = $item.attr('data-offset') * 1;
                
             if( $item.data('inactiveBtn') ){ return; }
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'loadUserPublished',
                    user_id : usid,
                    offset : offset
                },
                dataType: "json",
                beforeSend : function(){ $item.addClass('loading'); },
                success: function( data ) {
                    if( data.html ){
                        $('#user-content-list').append( data.html );
                        $item.attr('data-offset', data.newOffset).removeClass('loading');
                    } else {
                        $item.remove();
                    }
                }
            });
        },
        loadMoreEntradas : function( event ){
            event.preventDefault();
            
            var $item = $(event.currentTarget),
                $daList = $item.parent().find('.ajax-swiper ul'),
                offset = $item.data('offset'),
                orderType = $item.data('ordertype');
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'loadMoreEntradas',
                    orderType : orderType,
                    offset : offset
                },
                dataType: "json",
                beforeSend : function(){ $item.addClass('loading'); },
                success: function( data ) {
                    console.log( data );
                    if( data.html ){
                        $daList.append( data.html );
                        $item.attr('data-offset', data.newOffset).removeClass('loading');
                    }
                    else { $item.remove(); }
                }
            });
        },
        loadMoreSearchResults : function( event ){
            event.preventDefault();
            
            var $item = $(event.currentTarget),
                $daList = $('#search-results-list'),
                offset = $item.attr('data-offset'),
                searchTerm = $item.data('searchterm');
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'loadMoreSearchResults',
                    offset : offset,
                    searchTerm : searchTerm
                },
                dataType: "html",
                beforeSend : function(){ $item.addClass('loading'); },
                success: function( data ) {
                    if( data ){
                        $daList.append( data );
                        $item.attr('data-offset',(parseInt(offset) + 10)).removeClass('loading');
                    } else {
                        $item.remove();
                    }
                }
            });
        },
        socialLoginAction : function( event ){
            event.preventDefault();
            
            var url = $(event.currentTarget).data('href'),
                windowName = 'loginSocial',
                width = 300,
                height = 150;
            window.open(url, windowName, "width="+ width +",height="+ height +",location=no,menubarno,top=300,left=400");
        },
        regularLoginAction : function( event ){
            event.preventDefault();
            
            var siteHandler = this,
                $item = $(event.currentTarget),
                $lightBox;
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'regularLoginAction'
                },
                dataType: "html",
                beforeSend : function(){ $lightBox = siteHandler.getLightBox(); },
                success: function( data ) {
                    $lightBox.prepend( data );
                    
                    Modernizr.load({
                        test : $.fn.validizr,
                        nope : $('html').attr('data-relUrl') + '/js/validizr.js',
                        complete : function(){
                            $lightBox.find('form').validizr({
                                validFormCallback : function( $formulario ){
                                    $.ajax({
                                        type: "POST",
                                        url: '/wp-admin/admin-ajax.php',
                                        data: 'action=mobile_ajax&func=loguearUser&' + $formulario.serialize(),
                                        dataType: "json",
                                        beforeSend : function(){ $formulario.css('opacity', '0.2'); },
                                        success: function( data ) {
                                            if( data.status === 'ok' ){ window.location.reload(); }
                                            else {
                                                $formulario.find('.form-helper.error-message').remove();
                                                $formulario.append( data.html );
                                                $formulario.css('opacity', '1');
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });
                    
                    $lightBox.css('opacity', '1');
                }
            });
            
        },
        firma_y_participa : function( event ){
            event.preventDefault();

            if( this.block_firmas ){ return this.getBloqueoMessage( 'firmas' ); }
            
            var siteHandler = this,
                $item = $(event.currentTarget),
                $lightBox;
            
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'firma_y_participa',
                    pid : $item.data('pid')
                },
                dataType: "html",
                beforeSend : function(){ $lightBox = siteHandler.getLightBox(); },
                success: function( data ) {
                    $lightBox.prepend( data );
                    siteHandler.autoHandle( $lightBox.find('.evt') );
                    Modernizr.load({
                        test : $.fn.validizr,
                        nope : $('html').attr('data-relUrl') + '/js/validizr.js',
                        complete : function(){
                            $lightBox.find('form').validizr({
                                customValidations : {
                                    validarRut : function( $input ){ return siteHandler.esRut( $input.val() ); }
                                },
                                validFormCallback : function( $formulario ){
                                    $.ajax({
                                        type: "POST",
                                        url: '/wp-admin/admin-ajax.php',
                                        data: 'action=mobile_ajax&func=firmarAccion&' + $formulario.serialize(),
                                        dataType: 'json',
                                        beforeSend : function(){ $formulario.css('opacity', '0.2'); },
                                        success: function( data ) {
                                            $formulario.find('.nice-paragraph.error').remove();
                                            
                                            if( data.status === 'error_contrasena' ){
                                                $formulario.css('opacity', '1');
                                                $formulario.find('.user-login-box').after('<p class="nice-paragraph error">Nombre de usuario y/o contraseña incorrectos</p>');
                                            }
                                            if( data.status === 'error_nouser' ){
                                                $formulario.css('opacity', '1');
                                                $formulario.find('.user-login-box').after('<p class="nice-paragraph error">Debes ingresar tu nombre y tu email!</p>');
                                            }
                                            else if( data.status === 'error_repetido' ){
                                                $formulario.data('validizr').$submitBtn.before('<p class="nice-paragraph error">¡Hey, no puedes firmar una acción más de una vez!</p>');
                                                $formulario.css('opacity', '1');
                                            }
                                            else if( data.status === 'ok' ){

                                                if( data.vote_type === 'no_login_vote' ){
                                                    _gaq.push(['_trackEvent', 'Participacion', 'Firmas', 'BtnMobFirma_SinRegFirmar']);
                                                }
                                                else if( data.vote_type === 'login_vote' ){
                                                    _gaq.push(['_trackEvent', 'Participacion', 'Firmas', 'BtnMobFirma_FirmaRegistrado']);
                                                }

                                                $('html, body').animate({ scrollTop : 0 });
                                                $formulario.remove();
                                                $lightBox.prepend( data.html );
                                                $('#numero_firmas').find('strong').text( data.votos );
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });
                    
                    $lightBox.css('opacity', '1');
                }
            });
        },
        accion_loginChoice : function( event ){
            event.preventDefault();
            
            var siteHandler = this,
                $item = $(event.currentTarget),
                daChoice = $item.data('choice'),
                $holdingBox = $item.parents('div.user-login-box'),
                $backBtn = $('<button />').attr({
                    'class' : 'lb-back-btn',
                    'data-func' : 'firmaPreviousStep',
                    'title' : 'Cancelar'
                }),
                html = '',
                sendBtnText;
            
            siteHandler.autoHandle( $backBtn );
            siteHandler.preFirmaHtml = $holdingBox.html();
                
            if( daChoice === 'login' ){
                html += '<label for="usrLogin" >Nombre de usuario</label>';
                html += '<input type="text" autocapitalize="off" autocorrect="off" name="usrLogin" id="usrLogin" placeholder="Ingresa tu nombre de usuario" required>';
                html += '<label for="usrPass" >Contraseña</label>';
                html += '<input type="password" autocapitalize="off" autocorrect="off" name="usrPass" id="usrPass" placeholder="*****" required>';
                sendBtnText = 'Ingresar y firmar';
            }
            else {
                html += '<label for="voter_name" >Nombre</label>';
                html += '<input type="text" name="voter_name" id="voter_name" placeholder="Ingresa tu nombre" required>';
                html += '<label for="voter_email" >Email</label>';
                html += '<input type="email" autocapitalize="off" autocorrect="off" name="voter_email" id="voter_email" placeholder="Ingresa tu email" required>';
                sendBtnText = 'Firmar Acción';
            }
                        
            $holdingBox.html( html );
            $holdingBox.append( $backBtn );
            $('#sendBtn').val( sendBtnText );
        },
        firmaPreviousStep : function( event ){
            event.preventDefault();
            var siteHandler = this,
                $holdingBox = $(event.currentTarget).parents('div.user-login-box'),
                $toHandle;
        
            $holdingBox.html( siteHandler.preFirmaHtml );
            
            $toHandle = $holdingBox.find('.evt').removeClass('handled-element');
            siteHandler.autoHandle( $toHandle );
        },
        searchBtnHandler : function( event ){
            if( Modernizr.mq('only screen and (max-width : 640px)') ){
                event.preventDefault();
                
                var $item = $(event.currentTarget),
                    $searchForm = $('#mainSearchForm'),
                    $logo = $('#siteLogo'),
                    temporalFormHandler = function( event ){ if( event.keyCode === 13 ){ $searchForm.submit(); } };
                
                if( ! $item.hasClass('closeBtn') ){
                    $item.addClass('closeBtn');
                    $logo.addClass('moved');
                    $searchForm.addClass('expanded').css({ 'marginTop' : ($logo.height() * -1) + 'px' });
                    $searchForm.on('keyup', temporalFormHandler);
                    $searchForm.find('.campoSearch').focus();
                }
                else {
                    $item.removeClass('closeBtn');
                    $searchForm.removeClass('expanded').css({ 'marginTop' : '0px' });
                    $searchForm.off('keyup', temporalFormHandler);
                    $logo.removeClass('moved');
                }
            }
        },
        media_verMasGrande : function( event ){
            event.preventDefault();
            
            var siteHandler = this,
                $item = $( event.currentTarget ),
                $lightBox,
                $iframes;
        
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: {
                    action : 'mobile_ajax',
                    func : 'media_verMasGrande',
                    pid : $item.data('attid'),
                    pType : $item.data('posttype')
                },
                dataType: "json",
                beforeSend : function(){ $lightBox = siteHandler.getLightBox( 'foto-preview-lb', 'noScroll' ); },
                success: function( data ) {
                    $lightBox.prepend( data.html );
                    $lightBox.addClass('fotoPreviewBox').css('opacity', '1');
                    
                    if( $lightBox.find('iframe, object, embed').length ){
                        $iframes = $lightBox.find('iframe, object, embed');
                        $iframes.width('100%');
                    }
                    
                    $lightBox.on('click', function(evento){
                        evento.preventDefault();
                        
                        if( $lightBox.data('visibleContent') ){
                            $lightBox.find('.hidden-item').css('opacity', '0');
                            $lightBox.data('visibleContent', false);
                        } else {
                            $lightBox.find('.hidden-item').css('opacity', '1');
                            $lightBox.data('visibleContent', true);
                        }
                    });
                }
            });
        }
    };
    
    // inicialización, se hace en el window.onload en vez de el DOM.ready pero puede ser en cualquiera
    $( window.document ).ready(function(){ window.siteHandler = new window.SiteHandler(); });
}(this, jQuery));
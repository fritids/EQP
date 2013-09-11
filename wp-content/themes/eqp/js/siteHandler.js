(function( window, $, undefined ){
    Modernizr.addTest('webkitbrowser', function(){ return 'webkitRequestAnimationFrame' in window; });
    Modernizr.addTest('iebrowser', function(){ return navigator.userAgent.indexOf("MSIE") > 0; });
    
    window.Validizr = function( formulario, callback ){
        var validizrObj = this;
        this.form = formulario;
        this.$form = $( formulario );
        this.$fields = this.$form.find('input:not([type="submit"]), select, textarea');
        this.$wysiwygs = this.$form.find('textarea.required');
        this.$submitBtn = this.$form.find('input[type="submit"]');
        this.callBack = typeof( callback ) === 'function' ? callback : false;
        this.$iframes = $('');
                
        // se empieza a delegar
        setTimeout( function(){ validizrObj.delegateIframes( validizrObj.$wysiwygs ); }, 3000 );
        this.$fields.on('change keyup', {validizr : this}, $.proxy( this.validateInput, this ) );
        this.$submitBtn.on('click', {validizr : this}, $.proxy( this.validateForm, this ) );
    };
    window.Validizr.prototype = {
        validateInput : function( event ){
            var $input = $(event.currentTarget),
                value = $input.val(),
                atPos = value.indexOf("@"),
                stopPos = value.lastIndexOf("."),
                customHandler = $input.attr('data-customValidation'),
                genericValidity = $input.is('[type="email"]') ? ( $input.is('[type="email"]') && atPos > 0 && stopPos > 0 ) : value,
                isValidInput = customHandler ? event.data.validizr[customHandler]( $input ) : genericValidity;
                
            if( $input.hasClass('invalid-input') ){ $input.removeClass('invalid-input'); }
            if( ( $input.is('[required]') || $input.hasClass('required') ) && ! isValidInput ){ this.youAreNotValid( $input ); }    
                        
//            if( this.isFormValid() ){ this.$submitBtn.removeClass('disabled').prop('disabled', false); }
//            else { this.$submitBtn.addClass('disabled').prop('disabled', true); }
        },
        validateForm : function( event ){
            event.preventDefault();
            
            this.$fields.trigger('keyup');
            this.forceValidateIframes( this.$wysiwygs );
            
            if( this.isFormValid() ){
                if( this.callBack ){ return this.callBack( this.$form, this.$submitBtn ); }
                return this.$form.submit();
            }
            else {
                $('html, body').animate({
                    'scrollTop' : this.$form.find('.invalid-input').first().offset().top - 50
                });
            }
        },
        isFormValid : function(){ return ! this.$form.find('.invalid-input').length; },
        youAreNotValid : function( $input ){ $input.addClass('invalid-input'); },
        getInputType : function( $input ){ return $input.attr('type') ? $input.attr('type') : $input.get(0).tagName.toLowerCase(); },
        delegateIframes : function( $textareas ){
            var validizr = this;
            $.each( $textareas, function(index, elm){
                var $textarea = $(elm),
                    iframeId = $textarea.attr('id'),
                    $iframeBody = $( window.document.getElementById( iframeId + '_ifr' ).contentDocument.body );
                
                $iframeBody.on('keyup', {
                    $realTextarea : $textarea,
                    $theIframe : $(window.document.getElementById( iframeId + '_ifr' )),
                    $submitBtn : validizr.$submitBtn
                }, $.proxy( validizr.checkIframe, validizr ));
                $textarea.addClass('delegated');
            });
        },
        checkIframe : function( event ){
            if( ! $( event.currentTarget ).text().length ){
                event.data.$theIframe.addClass('invalid-iframe');
                event.data.$realTextarea.addClass('invalid-input');
//                event.data.$submitBtn.addClass('disabled').prop('disabled', true);
            }
            else {
                event.data.$theIframe.removeClass('invalid-iframe');
                event.data.$realTextarea.removeClass('invalid-input');
//                event.data.$submitBtn.removeClass('disabled').prop('disabled', false);
            }
        },
        checkCheckBox : function( $input ){ return $input.is(':checked'); },
        checkMaxLength : function( $input ){
            var max = parseInt( $input.attr('data-max') ),
                value = $input.val(),
                $helperText = $input.next();
            
            $helperText.text('Máximo de 70 caracteres, quedan '+ (70 - value.length) +' caracteres');
            
            return value && value.length <= max;
        },
        forceValidateIframes : function( $textareas ){
            $.each( $textareas, function(index, elm){
                var $textarea = $(elm),
                    iframeId = $textarea.attr('id'),
                    $theIframe = $(window.document.getElementById( iframeId + '_ifr' )),
                    $iframeBody = $( window.document.getElementById( iframeId + '_ifr' ).contentDocument.body );;
                
                if( ! $iframeBody.text().length ){
                    $theIframe.addClass('invalid-iframe');
                    $textarea.addClass('invalid-input');
                }
                else {
                    $theIframe.removeClass('invalid-iframe');
                    $textarea.removeClass('invalid-input');
                }
            });
        }
    };
    
    $.fn.validizr = function( callback ) {
        return this.each(function(){
            var $element = $(this);
            if ( $element.data('validizr') ) { return $element.data('validizr'); }
            var validator = new window.Validizr( this, callback );
            $element.data('validizr', validator);
        });
    };
    
    window.SiteHandler = function(){
        var esto = this;
        
        // para manejar el formulario de publicacion nuevo
        if( ! Modernizr.iebrowser ){ this.beautifyWeirdInputs( $('.ugly-input') ); }
        $('[data-delegation="autovalidate"]').validizr(function( $form, $submitBtn ){
            _gaq.push(['_trackEvent', $submitBtn.attr('data-ga-category'), $submitBtn.attr('data-ga_action'), $submitBtn.attr('data-ga_opt_label'), ( $submitBtn.attr('data-ga_value') ? parseInt( $submitBtn.attr('data-ga_value'), 10 ) : undefined )]);
            $form.submit();
        });
        
        //para manejar el formulario de requermiento de email para los users de twitter y facebook
        $('#email-form').validizr(function( $form ){
            var $parentDiv = $form.parents('.thankyou-message'),
                formData = $form.serialize();
                
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_zone&func=updateSocialEmails&' + formData,
                dataType: "json",
                beforeSend: function( xhr ) { $parentDiv.css('opacity', '0.2'); },
                success: function( data ) {
                    $parentDiv.css('opacity', '1').removeClass('alert').addClass('success');
                    $parentDiv.html(data.html);
                    esto.delegateThem( $parentDiv.find('.delegate-me') );
                },
                error : function() { $parentDiv.css('opacity', '1'); }
            });
        });


        $('#contactForm').validizr(function( $form ){
            var formData = $form.serialize();
            $.ajax({
                type: "POST",
                url: '/wp-admin/admin-ajax.php',
                data: 'action=ajax_zone&func=sendContactForm&' + formData,
                dataType: "html",
                beforeSend: function( xhr ) { $form.css('opacity', '0.2'); },
                success: function( data ) {
                    $form.before( data );
                    $form.remove();
                },
                error : function() { $form.css('opacity', '1'); }
            });
        });
        
        
        // ecualiza alturas de los titulos con la clase "equalize-heights", usado en single especiales
        this.equalizeHeights( $('.equalize-me.especial-title') );
    };
    window.SiteHandler.prototype = {
        delegateThem : function( $elements ){
            var esto = this;
            if( $elements.length ){
                $elements.each(function(index, elem){
                    var $item = $(elem),
                        daFunc = $item.data('func'),
                        daEvent = $item.data('event') ? $item.data('event') : 'click';
                        
                    if( typeof( esto[ daFunc ] ) === 'function' && ! $item.hasClass('is-handled') ){
                        $item.on( daEvent, $.proxy( esto[ daFunc ], esto ) );
                        $item.addClass('is-handled');
                    }
                });
            }
        },
        beautifyWeirdInputs : function( $elements ){
            if( ! $elements.length ){ return; }
            
            var esto = this;
            $.each( $elements, function(index, elm){
                var $item = $(elm),
                    placeholder = $item.attr('placeholder') ? $item.attr('placeholder') : $item.attr('data-placeholder') ? $item.attr('data-placeholder') : '',
                    $faker = $( '<div class="beautiful-input '+ $item.attr('data-type') +'" data-name="'+ $item.attr('name') +'" >'+ placeholder +'</div>' );
                    
                $item.after( $faker );
                $faker.on('click', esto.handleFake_file_input);
            });
        },
        handleFake_file_input : function( event ){
            var $item = $( event.currentTarget ),
                $form = $item.parents('form'),
                $realInput = $form.find('input[type="file"][name="'+ $item.attr('data-name') +'"]');
                
            if( ! $realInput.hasClass('delegated') ){
                $realInput.on('change', function( event ){ $item.text( $realInput.val() || $realInput.attr('data-placeholder') ); });
                $realInput.addClass('delegated');
            }
            $realInput.trigger('click');
        },
        equalizeHeights : function( $elements ){
            if( $elements.length ){
                var heightArray = [],
                    maxValue;
                $elements.height('auto');
                $.each($elements, function(index, elm){ heightArray.push( $(elm).height() ); });
                maxValue = Math.max.apply( Math, heightArray );
                $elements.height( maxValue );
            }
        },
        
        /////////////////////////////////////////////////////////// Delegaciones
        closeTargetBox : function( e ){
            e.preventDefault();
            $( $(e.currentTarget).data('target') ).remove();
        }
    };
    
    $(window.document).ready(function(){ window.siteHandlerApp = new window.SiteHandler(); });
}( this, jQuery));
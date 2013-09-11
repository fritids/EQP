(function( window, $, undefined ){
    window.ScrollNav = function( element, options ){
        this.defaults = {
            activeClass : 'activo',
            threshold : 0,
            activateCallBack : undefined,
            deactivateCallBack : undefined
        };
        this.settings = $.extend(true, {}, this.defaults, options);
        this.$element = $(element);
        this.targetHeight = this.$element.attr('data-target') ? $( this.$element.attr('data-target') ).height() : $( this.$element.attr('href') ).height();
        this.targetOffset = this.$element.attr('data-target') ? $( this.$element.attr('data-target') ).offset() : $( this.$element.attr('href') ).offset();
        this.$window = $(window);
        
        this.$window.on('scroll.ScrollNav', $.proxy(this.onScroll, this));
        this.$window.on('orientationchange.ScrollNav resize.ScrollNav', $.proxy(this.onResize, this));
        this.$element.on('click.ScrollNav', $.proxy(this.onClick, this));
        
        this.$window.trigger('scroll');
    };
    window.ScrollNav.prototype = {
        activateElement : function(){
            if( ! this.$element.hasClass( this.settings.activeClass ) ) { this.$element.addClass( this.settings.activeClass ); }
            if( typeof( this.settings.activateCallBack ) === 'function' ){ this.settings.activateCallBack( this.$element ); }
        },
        deactivateElement : function(){
            if( this.$element.hasClass( this.settings.activeClass ) ) { this.$element.removeClass( this.settings.activeClass ); }
            if( typeof( this.settings.deactivateCallBack ) === 'function' ){ this.settings.deactivateCallBack( this.$element ); }
        },
        onScroll : function( event ){
            var windowScrollTop = this.$window.scrollTop();
            if( (windowScrollTop >= ( this.targetOffset.top - this.settings.threshold )) && ( windowScrollTop < (this.targetOffset.top + this.targetHeight + this.settings.threshold) ) ){ this.activateElement(); }
            else { this.deactivateElement(); }
        },
        onResize : function( event ){
            this.targetHeight = this.$element.attr('data-target') ? $( this.$element.attr('data-target') ).height() : $( this.$element.attr('href') ).height();
            this.targetOffset = this.$element.attr('data-target') ? $( this.$element.attr('data-target') ).offset() : $( this.$element.attr('href') ).offset();
        },
        onClick : function( event ){
            event.preventDefault();
            $('html, body').animate({ 'scrollTop' : this.targetOffset.top  }, 500);
        }
    };
    
    $.fn.scrollNav = function( options ) {
        return this.each(function(){
            var $element = $(this);
            if ( $element.data('scrollNav') ) { return $element.data('scrollNav'); }
            var scrollNav = new window.ScrollNav( this, options );
            $element.data('scrollNav', scrollNav);
        });
    };
}( this, jQuery ));
(function( window, $, undefined ){
    //console log para todos 
    if( typeof( console.log ) === 'undefined' ){ console.log = function( stuff ){}; }
    
    (function(d, debug){
        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
        if (d.getElementById(id)) { return; }
        js = d.createElement('script');
        js.id = id;
        js.async = true;
        js.src = "//connect.facebook.net/en_US/all" + (debug ? "/debug" : "") + ".js";
        ref.parentNode.insertBefore(js, ref);
    }(window.document, /*debug*/ false));
    
    
    window.SocialFeedizr = function( element, options ){
        var esto = this;
        this.facebookFeed = false;
        this.twitterFeed = false;
        this.startTime = new Date().getTime();
        this.element = element;
        this.defaults = {
            facebook : {
                display : true,
                initSettings : {
                    appiD : '264596836884988',
                    channelUrl : '//www.elquintopoder.cl/channel.html',
                    status : false,
                    cookie : false,
                    xfbml : false
                },
                apiUrl : '/elquintopoder/posts',
                apiSettings : {
                    limit:30, 
                    access_token: '264596836884988|pVFs-M1DEyLtQtGpck6xkA148Uk'
                }
            },
            twitter : {
                display : true,
                url : "https://api.twitter.com/1.1/search/tweets.json?q=elquintopoder&amp;rpp=31&amp;callback=?"
            },
            actions : {
                responseHandler : undefined,
                responseCallback : undefined
            }
        };
        this.settings = $.extend({}, this.defaults, options ? options : {});
        
        if( this.settings.facebook.display ) {
            window.fbAsyncInit = function(){
                FB.init( esto.settings.facebook.initSettings );
                FB.api( esto.settings.facebook.apiUrl, esto.settings.facebook.apiSettings, function( response ){ esto.facebookFeed = response;  } );
                if( esto.settings.twitter.display ) { $.getJSON( esto.settings.twitter.url, function( response ){ esto.twitterFeed = response; }); }
            };
        }
        else if( this.settings.twitter.display ) { $.getJSON( esto.settings.twitter.url, function( response ){ esto.twitterFeed = response; }); }
        if( this.settings.facebook.display || this.settings.twitter.display ) { this.syncronizeCalls(); }
    };
    
    window.SocialFeedizr.prototype = {
        syncronizeCalls : function(){
            var esto = this,
                callTime =  new Date().getTime(),
                timePassed = callTime - esto.startTime,
                bothSocial = !this.twitterFeed || !this.facebookFeed;
            if( bothSocial && timePassed <= 3000 ){ setTimeout( function(){ esto.syncronizeCalls(); }, 600 ); }
            else { return this.takeAction( this.parseFeedResponses( this.twitterFeed, this.facebookFeed ), this.settings.actions.responseHandler, this.settings.actions.responseCallback ); }
//            console.log('esperando'); 
        },
        takeAction : function( data, handler, callback ){
//            console.log('accion!');
            if( typeof( handler ) === 'function' ){ handler( data, this.element ); }
            else { this.makeHTML( data, this.element ); }
            if( typeof( callback ) === 'function' ){ callback( data, this.element ); }
            return data || false;
        },
        parseFeedResponses : function( twitterFeed, facebookFeed ){
            var esto = this,
                twitterResponses = twitterFeed ? twitterFeed.results : [],
                facebookResponses = facebookFeed ? facebookFeed.data : [],
                twitterStandarResponse = [],
                facebookStandardResponse = [];
            
            console.log( facebookResponses );
            
            $.each( twitterResponses, function(index, value){
                var item = {
                        type : 'twitter',
                        text : esto.parseLinks( value.text ),
                        time : esto.parseDate( value.created_at ),
                        user : value.from_user,
                        avatar : value.profile_image_url,
                        user_url : 'http://twitter.com/' + value.from_user
                    };
                twitterStandarResponse.push( item );
            });
            $.each( facebookResponses, function(index, value){
                var item = {
                        type : 'facebook',
                        text : esto.parseLinks( value.message ? value.message : '' ) + (value.link ? '<a href="'+ value.link +'" rel="nofollow" title="Ir al link" target="_blank" >'+ ( value.name ? value.name : 'Ver enlace' ) +'</a>' : ''),
                        time : esto.parseDate( value.created_time ),
                        user : value.from.name,
                        avatar : 'http://graph.facebook.com/'+ value.from.id +'/picture',
                        user_url : 'http://facebook.com/' + value.from.id
                    };
                facebookStandardResponse.push( item );
            });
            
            if( twitterStandarResponse.length > facebookStandardResponse.length ) {
                return $.map(twitterStandarResponse, function(value, index) {
                    if( facebookStandardResponse[index] ){ return [value, facebookStandardResponse[index]]; }
                    else { return [value]; }
                });
            }
            else {
                return $.map(facebookStandardResponse, function(value, index) {
                    if( twitterStandarResponse[index] ){ return [value, twitterStandarResponse[index]]; }
                    else { return [value]; }
                });
            }
        },
        makeHTML : function( response ){
            var htmlString = '';
            
            $.each( response, function(index, item){
                if( item && item.text ){
                    htmlString += '<li class="social-feedizr-item" >';
                    htmlString += '<div class="social-feedizr-avatar-holder">';
                    htmlString += '<img class="social-feedizr-avatar-image" src="'+ item.avatar +'" alt="Imagen de '+ item.user +'" width="48" height="48" >';
                    htmlString += '<p class="social-feedizr-feed-src '+ item.type +'">'+ item.type +'</p>';
                    htmlString += '</div>';
                    htmlString += '<p class="social-feedizr-text" ><a href="'+ item.user_url +'" title="Ver el perfil de '+ item.user +'" rel="nofollow" target="_blank">'+ item.user +'</a> : ' + item.text;
                    htmlString += '<span class="social-feedizr-time-ago">'+ item.time +'</span></p>';
                    htmlString += '</li>';
                }
            });
            
            $( this.element ).append('<ul class="social-feedizr-list">'+ htmlString +'</ul>');
        },
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
                
            newString = string.replace(urlRegexp, function( url ){ return '<a class="gae" data-eventCat="'+ boxLocation +'" data-eventname="Link" data-eventvalue="Ver link asociado"  href="'+ url +'" title="Ver link" rel="nofollow" target="_blank">'+ url +'</a>'; }); 
            newString = newString.replace(twitterMentionRegexp, ' <a class="gae" data-eventCat="'+ boxLocation +'" data-eventname="link" data-eventvalue="mencion de usuario"  href="http://twitter.com/$1" target="_blank" rel="nofollow" title="Ir al perfil de @$1">@$1</a> ');
            newString = newString.replace(hashTagRegexp, function( hash ){
                var tag = hash.replace("#","%23");
                return '<a class="gae" data-eventCat="'+ boxLocation +'" data-eventname="link" data-eventvalue="Ver hashtag"  href="http://search.twitter.com/search?q='+ tag +'" title="Ver mas tweets de '+ hash +'" rel="nofollow" target="_blank">'+ hash +'</a>';
            });
            return newString;
        }
    };
    
    $.fn.socialFeedizr = function( options ) {
        return this.each(function(){
            var $element = $(this);
            if ( $element.data('socialFeedizr') ) { return $element.data('socialFeedizr'); }
            
            var socialFeedizr = new window.SocialFeedizr(this, options);
            $element.data('socialFeedizr', socialFeedizr);
        });
    };
    
}( this, jQuery ));
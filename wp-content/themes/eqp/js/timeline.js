var tl = {
    contadortw : 0,
    contadorfb : 0,
    mensajes : new Array(),
    mensajesfb : new Array(),
    twitter : function(){
        var word = $('#feeds-box').attr('data-filter');
        if(word){
            word = word.split(',');
            word = word.join(" OR ");
        }
        if(!word){    
            word = 'elquintopoder';
        }
        $.ajax({
            url:'http://search.twitter.com/search.json',
            data:{
                q:word,
                result_type : 'recent',
                rpp:40,
                lang: 'es'
            },
            dataType:"jsonp",
            jsonp:"callback",
            jsonpCallback:"twc"
        })       
     
    },
    timeline : function(){
        
        msgsort=new Array();
        for(k in tl.mensajes){
            msgsort.push(k);
        }
        msgsort.sort(function(a,b){
            return(a*1 > b*1) - (a*1 < b*1);
        });
            
            
        lastary=new Array();
        for(var i=0;i < msgsort.length; i++){
            lastary[i]=tl.mensajes[msgsort[i]]
        }
                    
        
        var lista="";
        $.each(lastary,function(i,item){
            lista+=item;
        })
        var maxItem = 12;
        var word = $('#feeds-box').attr('data-filter');

        if(word){
            maxItem = 5;
        }
        $("#feeds-box ul").html(lista);
        $("#feeds-box ul").find('a').attr('title','link externo de twitter').attr('rel','external');
        var total = $("#feeds-box ul li").length - maxItem; 
        $("#feeds-box ul li:gt("+total+")").show();
        
        
        tl.inter = setInterval(function(){
        
            if($( "#feeds-box ul li").length < 10 ) {
                clearInterval(tl.inter);
                $("#feeds-box ul li:first-child").show()
            }
            $("#feeds-box ul li:last-child").slideUp();
            $("#feeds-box ul li:last-child").queue(function () {
                $(this).remove();
                $(this).dequeue();
            });
            
            var maxItem = 9;
            var word = $('#feeds-box').attr('data-filter');
            
            if(word){
                maxItem = 5;
            }
            
            var total = $("#feeds-box ul li").length - maxItem;
            $("#feeds-box ul li:eq("+total+")").slideDown();
        }, 10000);        

        
    }
}
function prettyDatetw(time){
    var date=new Date(time),diff=(((new Date()).getTime()-date.getTime())/1000),day_diff=Math.floor(diff/86400);
    if(isNaN(day_diff)||day_diff<0||day_diff>=31)
        return;
    return day_diff==0 && ( diff<60 && "ahora" || diff<120 && "hace 1 minuto" || diff<3600 && "hace "+Math.floor(diff/60)+" minutos" || diff<7200 && "hace 1 hora" || diff<86400 && "hace "+Math.floor(diff/3600)+" horas") || day_diff==1 && "Ayer" || day_diff<7 && "hace "+day_diff+" dias" || day_diff<31 && "hace "+Math.ceil(day_diff/7)+" semanas";
}

function twc(tw){
    var contadortw  = 1;
    var content;
    var newContent;

    $.each(tw.results,function(ii,itemtw){
//        console.log(itemtw);
        content = itemtw.text.substring(0, 150);
        newContent = content.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g, function(url) {
            return url.link(url);
        });
        newContent = newContent.replace(/[@]+[A-Za-z0-9-_]+/g, function(u) {
            var username = u.replace("@","")
            return u.link("http://twitter.com/"+username);
        });
        newContent = newContent.replace(/[#]+[A-Za-z0-9-_]+/g, function(t) {
            var tag = t.replace("#","%23")
            return t.link("http://search.twitter.com/search?q="+tag);
        });
        
        
        tl.mensajes[contadortw ] = '<li  style="display:none" data-item="'+ contadortw  +'" data-type="tw"><div class="usr-avatar-holder"><img src="'+itemtw.profile_image_url+'" width="48" height="48" alt="avatar del usuario: '+itemtw.from_user+'" /><p class="feed-src twitter">Twitter</p></div><p><a rel="external" href="http://twitter.com/#!/'+itemtw.from_user+'" title="twitter user @'+itemtw.from_user+'">'+itemtw.from_user+'</a> : '+ newContent +'<span class="time-ago">'+ prettyDatetw(Date.parse(itemtw.created_at)) +'</span></p></li>';                 
        contadortw  = contadortw  + 2;
    });
}

window.fbAsyncInit = function() {
    FB.init({
        appId      : '264596836884988', // App ID
        channelUrl : '//www.elquintopoder.cl/channel.html', // Channel File
        status     : false, // check login status
        cookie     : false, // enable cookies to allow the server to access the session
        xfbml      : false  // parse XFBML
    });
    tl.contadorfb = 0;

    FB.api('/elquintopoder/posts', {
        limit:40, 
        access_token: '264596836884988|pVFs-M1DEyLtQtGpck6xkA148Uk'
    } , function(response){
        if($('body').hasClass('home')){                
            var  contadorfb = 0;               
                $.each(response.data,function(i,item){
                    
                    var ta=item.created_time.split("T")
                    var ta2=ta[1].split("+");
                    var ta3=ta[0].split("-");
                    var timeago=ta3[1]+", "+ta3[2]+" "+ta3[0]+" "+ta2[0]+" +"+ta2[1];
                    var srcimg='http://graph.facebook.com/'+item.from.id+'/picture';

                    if ( (item.type=="link" || item.type=="photo") && item.message!=undefined ){
                        tl.mensajes[contadorfb] = '<li style="display:none" data-item="'+contadorfb +'" data-type="fc"><div class="usr-avatar-holder"><img src="'+srcimg+'" width="50"  height="50"  alt="foto de  usuario:'+item.from.name+' " /><p class="feed-src facebook">Facebook</p></div><p><a href="#" title="Deja un comentario en facebook"> '+item.from.name+'</a> : '+item.message.substring(0, 90)+'...<span class="time-ago">'+ prettyDatetw(Date.parse(timeago)) +'</span></p></li>';
                        contadorfb  = contadorfb  + 2;
                    }
                    if (item.comments.count!=0 && item.comments.data != undefined){
                        $.each(item.comments.data , function(iterator, comenta){

                            var srcimgc='http://graph.facebook.com/'+comenta.from.id+'/picture';
                            tl.mensajes[contadorfb] = '<li  style="display:none" data-item="'+contadorfb +'" data-type="fc_c"><div class="usr-avatar-holder"><img src="'+srcimgc+'" width="50"  height="50"  alt="foto de  usuario:'+comenta.from.name+' " /><p class="feed-src facebook">Facebook</p></div><p><a href="#" title="Deja un comentario en facebook"> '+comenta.from.name+'</a> : '+comenta.message.substring(0, 90) + '...<span class="time-ago">'+ prettyDatetw(Date.parse(timeago)) +'</span></p></li>';
                            contadorfb  = contadorfb  + 2;
                        })
                    }
                }) 
        }
    });

}

$(function(){
    tl.twitter();
    setTimeout(function(){
        tl.timeline()
    },5500);
    
});



(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement('script');
    js.id = id;
    js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

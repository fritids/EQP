
gl = {
    it : 0,
    fbmpage: true,
    twmpage : true,
    fl : false
}

function sf(a,b){
    return(b-a)
}

function twc(tw){
    gl.twmpage= new Object();
    gl.it = 1;
    var content;
    var newContent;
    
    
    
    $.each(tw.results,function(ii,itemtw){
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
            
            gl.twmpage[gl.it] ='<li  style="display:none" data-item="'+gl.it+'"><div class="usr-avatar-holder"><img src="'+itemtw.profile_image_url+'" width="48" height="48" alt="avatar del usuario: '+itemtw.from_user+'" /><p class="feed-src twitter">Twitter</p></div><p><a rel="external" href="http://twitter.com/#!/'+itemtw.from_user+'" title="twitter user @'+itemtw.from_user+'">'+itemtw.from_user+'</a> dijo '+ newContent +'</p></li>';                 
            gl.it = gl.it + 2;
        })
    }
            
window.fbAsyncInit = function() {
    gl.fl=false;
    FB.init({
        appId      : '264596836884988', // App ID
        channelUrl : '//www.elquintopoder.cl/channel.html', // Channel File
        status     : false, // check login status
        cookie     : false, // enable cookies to allow the server to access the session
        xfbml      : false  // parse XFBML
    });

    
if ( document.getElementById("feeds-box") ){
        
        var word = $('#feeds-box').attr('data-filter');
        
        if(word){
            word = word.split(',');
            word = word.join(" OR ");
        }
        
        if(!word){    
            word = 'elquintopoder';
        }
        
        
        setTimeout(function(){
            $.ajax({
                url:'http://search.twitter.com/search.json',
                data:{
                    q:word,
                    result_type : 'recent',
                    rpp:50
                },
                dataType:"jsonp",
                jsonp:"callback",
                jsonpCallback:"twc"
            });    
        }, 200);
        
        
        <?php 
            $context = stream_context_create(array('https'=>array('ignore_errors'=>true))); 
            $at = file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=264596836884988&client_secret=30001e9faa67e4bbc9445ebf8e3495fc&grant_type=client_credentials", false, $context); 
            $access_tocken = split("=", $at);
        ?>
        FB.api('/elquintopoder/posts', {limit:35 , access_token:'<?php echo $access_tocken[1]?>'} ,function(response){
            gl.fbmpage= new Object();
            gl.it=0;
            
            $.each(response.data,function(i,item){
                var ta=item.created_time.split("T");
                var ta2=ta[1].split("+");
                var ta3=ta[0].split("-");
                timeago=ta3[1]+", "+ta3[2]+" "+ta3[0]+" "+ta2[0]+" +"+ta2[1];
                var srcimg='http://graph.facebook.com/'+item.from.id+'/picture';

                if ( (item.type=="link" || item.type=="photo") && item.message!=undefined ){
                    gl.fbmpage[gl.it] = '<li style="display:none" data-item="'+gl.it+'"><div class="usr-avatar-holder"><img src="'+srcimg+'" width="50"  height="50"  alt="foto de  usuario:'+item.from.name+' " /><p class="feed-src facebook">Facebook</p></div><p><a href="#" title="Deja un comentario en facebook"> '+item.from.name+'</a>, dijo : '+item.message.substring(0, 90)+'...</p></li>';
                    gl.it = gl.it+2;
                }
                if (item.comments.count!=0 ){
                    $.each(item.comments.data , function(iterator, comenta){
                        var tac=comenta.created_time.split("T");
                        var ta2c=tac[1].split("+");
                        var ta3c=tac[0].split("-");
                        timeagoc=ta3c[1]+", "+ta3c[2]+" "+ta3c[0]+" "+ta2c[0]+" +"+ta2c[1];
                        var srcimgc='http://graph.facebook.com/'+comenta.from.id+'/picture';
                            gl.fbmpage[gl.it] = '<li  style="display:none" data-item="'+gl.it+'"><div class="usr-avatar-holder"><img src="'+srcimgc+'" width="50"  height="50"  alt="foto de  usuario:'+comenta.from.name+' " /><p class="feed-src facebook">Facebook</p></div><p><a href="#" title="Deja un comentario en facebook"> '+comenta.from.name+'</a>, dijo : '+comenta.message.substring(0, 90) + '...</p></li>';
                            gl.it = gl.it + 2;
                    })

                }
                
            })
            
            
            
            var msg=new Array();
            var tws=gl.twmpage;
            $.each(tws,function(i,item){
                msg[i]=item;
            })
            if($('body').hasClass('home')){
                var fbp=gl.fbmpage;
               $.each(fbp,function(i,item){
                    msg[i]=item;
                })
            }
            msgsort=new Array();
            for(k in msg){
                msgsort.push(k);
            }
            msgsort.sort(function(a,b){
                return(a*1 > b*1) - (a*1 < b*1);
            });
            
            
            lastary=new Array();
            for(var i=0;i < msgsort.length; i++){
                lastary[i]=msg[msgsort[i]]
            }
            
            var lista="";
            var cont = 0;
            $.each(lastary,function(i,item){
                    lista+=item;
                    cont++
            })
            
            var maxItem = 9;
            var word = $('#feeds-box').attr('data-filter');
            
            if(word){
                maxItem = 3;
            }
            
            $("#feeds-box ul").html(lista);
            $("#feeds-box ul").find('a').attr('title','link externo de twitter').attr('rel','external');
            var total = $("#feeds-box ul li").length - maxItem; 
            $("#feeds-box ul li:gt("+total+")").show();

        })
        
        console.log(gl);
        
        gl.inter = setInterval(function(){
        
        if($( "#feeds-box ul li").length < 10 ) {clearInterval(gl.inter); $("#feeds-box ul li:first-child").show()}
            $("#feeds-box ul li:last-child").slideUp();
            $("#feeds-box ul li:last-child").queue(function () {
                $(this).remove();
                $(this).dequeue();
            });
            
            var maxItem = 9;
            var word = $('#feeds-box').attr('data-filter');
            
            if(word){
                maxItem = 3;
            }
            
            var total = $("#feeds-box ul li").length - maxItem;
            $("#feeds-box ul li:eq("+total+")").slideDown();
        }, 10000);
    }
};

// Load the SDK Asynchronously
(function(d){
    var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    ref.parentNode.insertBefore(js, ref);
}(document));

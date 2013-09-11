
$(function(){
    if( $('#newsletter-subscription').length ){
        $('#newsletter-subscription').find('#suscribir').on("click", function(event){
            event.preventDefault();
            
            var email = $('#newsletter-subscription').find('input[name="susbcribeEmail"]').val();
            var AtPos = email.indexOf("@");
            var StopPos = email.lastIndexOf(".");
            
            if ( email && (AtPos != -1 && StopPos != -1) ) {
                $('#newsletter-subscription').find('input[name="susbcribeEmail"]').removeClass('invalidInput');
                
                $.ajax({
                    type: "POST",
                    url: '/wp-admin/admin-ajax.php',
                    data: {
                        action : 'newsAjax',
                        email : email
                    },
                    dataType: "html",
                    beforeSend: function( xhr ) {
                        $('#newsletter-subscription').css({ 'opacity' : '0.2' });
                    },
                    success: function( data ) {
                        $('#newsletter-subscription').css({ 'opacity' : '1' });
                        $('#newsletter-subscription').children().fadeOut();
                        $('#newsletter-subscription').prepend('<p id="newsSuccess" class="message success">Tu email ha sido registrado y te agregaremos en nuestro próximo newsletter</p>');
                        setTimeout(function(){
                            $('#newsSuccess').fadeOut();
                            $('#newsSuccess').remove();
                            $('#newsletter-subscription').children().fadeIn();
                        }, 3000);
                    },
                    error : function() { $('#newsletter-subscription').css({ 'opacity' : '1' }); }
                });
            }
            else {
                $('#newsletter-subscription').find('input[name="susbcribeEmail"]').addClass('invalidInput');
            }
            
        });
    }
});
jQuery(function(){
    jQuery("#el_mes_en_curso").on("change", "#selectmes", function(){
        jQuery.ajax({
            type: "GET",
            url: '/wp-admin/admin-ajax.php',
            data: {
                action : 'ajax_stat',
                mes : jQuery("#el_mes_en_curso #selectmes").val()
            },
            dataType: "html",
            beforeSend: function( xhr ) {
                jQuery("#el_mes_en_curso .inside").css('opacity','0.5');
            },
            success: function( data ) {
                jQuery("#el_mes_en_curso .inside").css('opacity','1.0');
                jQuery("#el_mes_en_curso .inside").html(data);
            },
            error : function() {}
        });
         
    });
})
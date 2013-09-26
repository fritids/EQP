jQuery(function() {

    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=ajax_statIDA",
        cache: false,
        dataType: "json",
        data: {action: "ajax_statIDA"}

    }).done(function(msg) {
        meses = new Array();

        jQuery.each(msg, function(i, item) {
            categories = new Array();
            datos = new Array();
            jQuery.each(item, function(ii, itemes) {
                categories.push(ii);
                datos.push(itemes);
            });
            meses.push({name: i, data: datos});
        });

        jQuery('.graph').highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Datos 4 últimos meses'
            },
            xAxis: {
                categories: categories
            },
            yAxis: {
                title: {
                    text: 'esta'
                }
            },
            series: meses
        });

    });


});
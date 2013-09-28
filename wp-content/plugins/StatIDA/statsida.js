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
            	ii = ii.replace(/_/g," ")
                categories.push(ii);
                datos.push(itemes);
            });
            meses.push({name: i, data: datos});
        });

        jQuery('.graph').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Comentarios 12 últimos meses'
            },
            xAxis: {
                categories: categories,
                labels: {
                    rotation: -45,
                    align: 'right',
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif',
                        marginTop: '20px'
                    }
                }                 
            },
            yAxis: {
                title: {
                    text: 'Miles'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.5,
                    borderWidth: 0,
					pointWidth:10
                }
            },           
            series: meses
        });

    });


});
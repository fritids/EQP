jQuery(function() {

    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=ajax_statIDA&obt=publicaciones",
        cache: false,
        dataType: "json",
        data: {action: "ajax_statIDA"}
    }).done(function(msg) {
        meses = new Array();
        datos_entradas = new Array();
        datos_acciones = new Array();
        datos_fotos = new Array();
        datos_videos = new Array();
        categories = new Array();
        datos_total = new Array();

        jQuery.each(msg, function(i, item) {
            total_pub = 0;
            jQuery.each(item, function(ii, itemes) {
                ii = ii.replace(/_/g, " ")
                if (ii == "entradas") {
                    datos_entradas.push(itemes);
                }
                else if (ii == "acciones") {
                    datos_acciones.push(itemes);
                }
                else if (ii == "fotos") {
                    datos_fotos.push(itemes);
                }
                else if (ii == "videos") {
                    datos_videos.push(itemes);
                }
                total_pub = total_pub + itemes;
            });
            datos_total.push(total_pub);
            categories.push(i);
        });

        meses.push({name: 'Publicaciones', data: datos_total, type: "column"});
        meses.push({name: 'Acciones', data: datos_acciones, tﬁype: "spline"});
        meses.push({name: 'Entradas', data: datos_entradas, type: "spline"});
        meses.push({name: 'Fotos', data: datos_fotos, type: "spline"});
        meses.push({name: 'Videos', data: datos_videos, type: "spline"});

        jQuery('.graph_publicaciones').highcharts({
            chart: {
            },
            title: {
                text: 'Entradas, Videos, Imágenes y Acciones'
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
                    text: 'Cifras totales por mes'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0,
                    borderWidth: 0,
                    pointWidth: 25,
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#fff',
                        align: 'left',
                        x: 3,
                        y: 50,
                        style: {
                            fontSize: '12px',
                            fontFamily: 'Verdana, sans-serif',
                            textShadow: '0 0 3px black'
                        }
                    }
                }
            },
            series: meses
        });
        jQuery('.graph_publicaciones').after('<a style="display:block; margin: 10px auto; width:20%;" href="http://www.elquintopoder.cl/wp-admin/admin.php?page=stats-ida">Ver más estadísticas</a>');
    });
//                                                                votos
    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=ajax_statIDA&obt=votos",
        cache: false,
        dataType: "json",
        data: {action: "ajax_statIDA"}
    }).done(function(msg) {
        meses = new Array();
        datos = new Array();
        datos_spline_n = new Array();
        datos_spline_p = new Array();
        categories = new Array();

        jQuery.each(msg, function(i, item) {
            jQuery.each(item, function(ii, itemes) {
                ii = ii.replace(/_/g, " ")
                if (ii == "total votos acciones") {
                    datos.push(itemes);
                }
                else if (ii == "votos acciones login") {
                    datos_spline_p.push(itemes);
                }
                else {
                    datos_spline_n.push(itemes);
                }
            });
            categories.push(i);
        });
        meses.push({name: 'Firma y Participa', data: datos, type: "column"});
        meses.push({name: 'Con login', data: datos_spline_p, type: "spline"});
        meses.push({name: 'Sin login', data: datos_spline_n, type: "spline"});
        jQuery('.graph_votos').highcharts({
            chart: {
//                type: 'column'
            },
            title: {
                text: 'Firma y participa con/sin login'
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
                    text: 'Cifras totales por mes'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.5,
                    borderWidth: 0,
                    pointWidth: 30,
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#FFFFFF',
                        align: 'right',
                        x: 4,
                        y: 10,
                        style: {
                            fontSize: '11px',
                            fontFamily: 'Verdana, sans-serif',
                            textShadow: '0 0 3px black'
                        }
                    }
                }
            },
            series: meses
        });
        jQuery('.graph_votos').after('<a style="display:block; margin: 10px auto; width:20%;" href="http://www.elquintopoder.cl/wp-admin/admin.php?page=stats-ida">Ver más estadísticas</a>');
    });

});
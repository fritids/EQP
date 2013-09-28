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
                text: 'Publicaciones 12 últimos meses'
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
    });


    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=ajax_statIDA&obt=usuarios",
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
                if (ii == "usuarios activos") {
                    datos.push(itemes);
                }
                else if (ii == "usuarios comentado") {
                    datos_spline_p.push(itemes);
                }
                else {
                    datos_spline_n.push(itemes);
                }
            });
            categories.push(i);
        });
        meses.push({name: 'Usuarios que publicaron ', data: datos, type: "column"});
        meses.push({name: 'Usuarios que comentaron', data: datos_spline_p, type: "column"});
        meses.push({name: 'Usuarios registrados', data: datos_spline_n, type: "spline"});
        jQuery('.graph_usuarios').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Usuarios 12 últimos meses'
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
                    pointWidth: 18,
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
    });

    //                                                                          comentarios    
    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=ajax_statIDA&obt=comentarios",
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
                if (ii == "comentarios") {
                    datos.push(itemes);
                }
                else if (ii == "comentarios positivos") {
                    datos_spline_p.push(itemes);
                }
                else {
                    datos_spline_n.push(itemes);
                }
            });
            categories.push(i);
        });
        meses.push({name: 'Comentarios', data: datos, type: "column"});
        meses.push({name: 'Votos negativos', data: datos_spline_n, type: "spline"});
        meses.push({name: 'Votos positivos', data: datos_spline_p, type: "spline"});
        jQuery('.graph_comentarios').highcharts({
            chart: {
//                type: 'column'
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
    });
//                                                                              votos
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
                text: 'Firma y Participa 12 últimos meses'
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
    });
    
    
//                                                                              GA
    jQuery.ajax({
        type: "POST",
        url: "/wp-admin/admin-ajax.php?action=gastatida",
        cache: false,
        dataType: "json"
    }).done(function(msg) {
        redes= new Array();
        devices= new Array();
        total_social= new Array();
        facebook= new Array();
        google= new Array();
        twitter= new Array();
        total_visitor= new Array();
        desktop= new Array();
        mobile= new Array();
        tablet= new Array();
        categories = new Array();
        jQuery.each(msg, function(i, item) {

            jQuery.each(item, function(ii, itemes) {
                if (ii == "total_social") {
                    total_social.push(itemes);
                }
                else if (ii == "Google") {
                    google.push(itemes);
                }
                else if (ii == "Facebook") {
                    facebook.push(itemes);
                }
                else if (ii == "Twitter") {
                    twitter.push(itemes);
                }
                if (ii == "total_visitor") {
                    total_visitor.push(itemes);
                }
                else if (ii == "desktop") {
                    desktop.push(itemes);
                }
                else if (ii == "mobile") {
                    mobile.push(itemes);
                }
                else if (ii == "tablet") {
                    tablet.push(itemes);
                }                
                
                
            });
            categories.push(i);
        });
        redes.push({name: 'Redes Sociales', data: total_social, type: "column"});
        redes.push({name: 'Google', data: google, type: "spline"});
        redes.push({name: 'Twitter', data: twitter, type: "spline"});
        redes.push({name: 'Facebook', data: facebook, type: "spline"});        

        devices.push({name: 'Visitas', data: total_visitor, type: "column"});
        devices.push({name: 'Desktop', data: desktop, type: "spline"});
        devices.push({name: 'Mobile', data: mobile, type: "spline"});
        devices.push({name: 'Tablets', data: tablet, type: "spline"});        
       
        jQuery('.graph_redes').highcharts({
            chart: {
	            //s
            },
            title: {
                text: 'Actividad Redes Sociales'
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
            series: redes
        });
        jQuery('.graph_visitas').highcharts({
            chart: {
	            //s
            },
            title: {
                text: 'Vistas x dispositivos'
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
            series: devices
        });
    });    


});
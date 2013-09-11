
$(function(){
    $('.actions-board li:last-child a').css({
        'display' : 'block',
        'height' : '35px',
        'text-indent' : '-99999px',
        'width' : '220px'
    });
    $('.articles-holder .col:last-child, #inside-showcased-items .vertical li:last-child').css({
        'margin-right' : 0
    });
    $('.actions-board.resumed li:last-child a').css({
        'font-size' : '0.9em',
        'line-height' : '140%',
        'margin-top' : '2px',
        'text-indent' : '0',
        'width' : '90px'
    });
    $('#thisWeek ul li:last-child').css({
        'border-right' : 'medium none',
        'padding-right' : '0'
    });
    $('#newsletter-subscription input[type="email"]').css({
        'display' : 'inline'
    });
});
function checkAnimation() {
    console.log('Animation check');
   $('.fl-animation').each(function() {
       var top = $(this).offset().top;
       if($(window).scrollTop() + $(window).height() > top) {
           $(this).addClass('fl-animated');
        }
   });
};
$(document).ready(function() {
    console.log('test');
    checkAnimation();

    $('.nojs').removeClass('nojs');
    
    $('#nav-toggler').click(function() {
        if($('#site-header').hasClass('navshow'))
            $('#site-header').removeClass('navshow');
        else
            $('#site-header').addClass('navshow');
    });

    $(document).on('click', 'a.share-btn:not([popup="false"])', function(e) {
        e.preventDefault();
        window.open($(this).attr('href'), '', 'toolbar=0,status=0,width=626,height=436');
    });
});

$(window).scroll(function() {
    checkAnimation();
});

function numberWithSpaces(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}
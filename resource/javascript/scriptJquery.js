$(document).ready(function(){
    $('.button-collapse').sideNav({'edge': 'left'});
    
    var $navbar = $('#tinyNavbar'),
        $win    = $(window);
    $win.scroll(function() {
        if ($win.scrollTop()>100){
            $navbar.addClass("header-fixed-shrink");
        }
        else {
            $navbar.removeClass("header-fixed-shrink");
        }
    });
})
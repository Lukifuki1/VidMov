;(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports !== 'undefined') {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function ($) {
    'use strict';
    var $d = $(document);
    var $w = $(window);
    var _d = document;
    var _w = window;
    var $h = $('html');
    var $b = $('body');

    var $player_parent = $('.custom-player-float-e');
    var $player_wrapper = $player_parent.find('.beeteam368-player-wrapper-control');

    $w.on('scroll', function(){
        
        var videoOffset = $player_parent.offset().top + $player_parent.outerHeight(true);
        
        if($w.scrollTop() > videoOffset + 50){
            if($b.hasClass('beeteam368-floating-video')){
                return;
            }

            $b.addClass('beeteam368-floating-video').removeClass('beeteam368-disable-floating-video');
            $player_wrapper.find('.player-video-icon, .mejs__overlay-button.custom-style').addClass('small-icon');
        }else{
            if(!$b.hasClass('beeteam368-floating-video')){
                return;
            }
            
            $b.removeClass('beeteam368-floating-video').addClass('beeteam368-disable-floating-video');
            $player_wrapper.find('.player-video-icon, .mejs__overlay-button.custom-style').removeClass('small-icon');
        }
    });
    
    $player_wrapper.find('.scroll-up-floating-video-control').on('click', function(){
        
        $('html, body').stop().animate({scrollTop:$player_wrapper.parents('.custom-player-float-e').offset().top-90}, {duration:500}, function(){});
        
        return false;
    });
    
    $player_wrapper.find('.close-floating-video-control').on('click', function(){
        
        $b.removeClass('beeteam368-floating-video').addClass('beeteam368-disable-floating-video beeteam368-remove-floating-video');
        $player_wrapper.find('.player-video-icon, .mejs__overlay-button.custom-style').removeClass('small-icon');
        
        return false;
    });

    $('.turn-off-light-dynamic').on('click', function(e){
        $(this).parents('.beeteam368-player-control').toggleClass('active-dynamic-light-player');
    });
}));
/*
Review Feature
Author: BeeTeam368
Author URI: http://themeforest.net/user/beeteam368
License: Themeforest Licence
License URI: http://themeforest.net/licenses
*/

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
	
	$d.ready(function () {
		var instances = OverlayScrollbars(document.querySelectorAll('.main-daffodil-items-control'), {
			scrollbars: {
				autoHide: 'leave',
			},
			overflowBehavior: {
				x: 'hidden',
				y: 'scroll',
			}
		});		
    });
}));
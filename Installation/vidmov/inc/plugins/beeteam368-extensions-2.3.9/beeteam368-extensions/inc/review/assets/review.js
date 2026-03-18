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
	
	$.getStylesheet = function (href) {
        var $d = $.Deferred();
        var $link = $('<link/>', {
            rel: 'stylesheet',
            type: 'text/css',
            href: href
        }).prependTo('head');
        $d.resolve($link);
        return $d.promise();
    }

    $.getUrlExtension = function(url) {
        return url.split(/[#?]/)[0].split('.').pop().trim();
    }
	
	$.updateQueryStringParameterCT = function(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        }else {
            return uri + separator + key + "=" + value;
        }
    }
	
	$.cachedScriptCT = function( url, options ) {
		options = $.extend( options || {}, {
			dataType: 'script',
			cache: true,
			url: $.updateQueryStringParameterCT(url, 'cache_version', vidmov_jav_js_object.cache_version)
		});
		return $.ajax( options );		
	}

    $.getMultiScripts = function(arr, path) {
        var _arr = $.map(arr, function(scr) {
            if($.getUrlExtension(scr) === 'css'){
                return $.getStylesheet( (path||'') + scr );
            }else if($.getUrlExtension(scr) === 'js'){
                return $.cachedScriptCT( (path||'') + scr );
            }

        });

        _arr.push($.Deferred(function( deferred ){
            $( deferred.resolve );
        }));

        return $.when.apply($, _arr);
    }
	
	$.isNumber = function(n){
		var _ = this;
		return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	$.fn.beeteam368_review = function($params){
		
		if(typeof($params) !== 'object'){
            return false;
        }

        var $t = $(this);
        var review_id = $t.attr('id');
		var post_id = $t.attr('data-id');
		
		if(typeof(post_id)==='undefined' || !$.isNumber(post_id)){
			return false;
		}
		
		$t.on('beeteam368ReviewActionControl'+(review_id), function(e, post_id, value, review_id, rN_params){
			var $tca = $(this);
			
			$tca.addClass('loading-review');
			
			var query = {
                'action': 'review_action_request',
                'post_id': post_id,
				'value': value,
                'security': vidmov_jav_js_object.security,
            }
			
			$.ajax({
                type: 'POST',
                url: vidmov_jav_js_object.admin_ajax,
                cache: true,
                data: query,
                dataType: 'json',
                success: function(data, textStatus, jqXHR){
				
					if(typeof(data) === 'object' && typeof(data.review_count)!=='undefined' && typeof(data.review_percent)!=='undefined'){
						
						var review_unit = (typeof(vidmov_jav_js_object.review_unit)!=='undefined' && vidmov_jav_js_object.review_unit === 'percent')?'percent':'decimal';
						
						if(review_unit === 'percent'){
							$('.review-score-percent-control[data-id="'+(post_id)+'"]').html((data.review_percent)+'<span class="review-percent font-main font-size-10">%</span>');
						}else{
							$('.review-score-percent-control[data-id="'+(post_id)+'"]').html((data.review_percent/10).toFixed(1));
						}
						
						$('.review-score-count-control[data-id="'+(post_id)+'"]').text(data.review_count_text);
						$('.review-score-wrapper-control[data-id="'+(post_id)+'"]').attr('style', data.review_percent_css);
						
						if(data.review_percent <= 39){
							$('.review-score-wrapper-control[data-id="'+(post_id)+'"]').removeClass('red-percent yellow-percent').addClass('red-percent');
						}else if(data.review_percent > 39 && data.review_percent < 70){
							$('.review-score-wrapper-control[data-id="'+(post_id)+'"]').removeClass('red-percent yellow-percent').addClass('yellow-percent');
						}else{
							$('.review-score-wrapper-control[data-id="'+(post_id)+'"]').removeClass('red-percent yellow-percent')
						}
						
						var $post_review_title_control = $('.post-review-title-control[data-id="'+(post_id)+'"]');
						$post_review_title_control.find('.has-rated-control').remove();
						
						if(value > 0){
							$post_review_title_control.append('<span class="font-main font-size-12 has-rated has-rated-control">'+(vidmov_jav_js_object.thanks_for_rating_text)+': '+(value)+'&nbsp;<i class="far fa-star"></i></span>');
						}
                        
                        if(typeof(data.new_action_btn_html)!=='undefined' && $.trim(data.new_action_btn_html)!='' && $('#action-btn-for-'+(post_id)).length > 0){
                            $('#action-btn-for-'+(post_id)).replaceWith(data.new_action_btn_html);
                        }
					}        

                    $tca.removeClass('loading-review');
                },
                error: function( jqXHR, textStatus, errorThrown ){
                }
            });
		});
		
		$t.on('beeteam368ReviewLibraryControlInstalled'+(review_id), function(e, rN_params){
			
			var $tci = $(this);
			var $input_control = $tci.find('.review-input-control');
			
			var star_otps = {
				min: 0, max: 10, step: 0.5, stars: 10,
				starCaptions: {
					0.5: vidmov_jav_js_object.very_poor_text,
					1: vidmov_jav_js_object.very_poor_text, 
					1.5: vidmov_jav_js_object.very_poor_text, 
					2: vidmov_jav_js_object.very_poor_text, 
					2.5: vidmov_jav_js_object.very_poor_text, 
					3: vidmov_jav_js_object.poor_text, 
					3.5: vidmov_jav_js_object.poor_text, 
					4: vidmov_jav_js_object.poor_text, 
					4.5: vidmov_jav_js_object.poor_text, 
					5: vidmov_jav_js_object.ok_text, 
					5.5: vidmov_jav_js_object.ok_text, 
					6: vidmov_jav_js_object.ok_text, 
					6.5: vidmov_jav_js_object.ok_text, 
					7: vidmov_jav_js_object.good_text, 
					7.5: vidmov_jav_js_object.good_text, 
					8: vidmov_jav_js_object.good_text, 
					8.5: vidmov_jav_js_object.good_text, 
					9: vidmov_jav_js_object.very_good_text, 
					9.5: vidmov_jav_js_object.very_good_text, 
					10: vidmov_jav_js_object.very_good_text
				},
				starCaptionClasses: {
					0.5: 'caption-badge caption-danger',
					1: 'caption-badge caption-danger', 
					1.5: 'caption-badge caption-danger', 
					2: 'caption-badge caption-danger', 
					2.5: 'caption-badge caption-danger', 
					3: 'caption-badge caption-warning', 
					3.5: 'caption-badge caption-warning', 
					4: 'caption-badge caption-warning', 
					4.5: 'caption-badge caption-warning', 
					5: 'caption-badge caption-info', 
					5.5: 'caption-badge caption-info', 
					6: 'caption-badge caption-info', 
					6.5: 'caption-badge caption-info', 
					7: 'caption-badge caption-primary', 
					7.5: 'caption-badge caption-primary', 
					8: 'caption-badge caption-primary', 
					8.5: 'caption-badge caption-primary', 
					9: 'caption-badge caption-success', 
					9.5: 'caption-badge caption-success', 
					10: 'caption-badge caption-success'
				},
				starTitles:{
					0.5: vidmov_jav_js_object.Half_Star_text,
					1: vidmov_jav_js_object.One_Star_text, 
					1.5: vidmov_jav_js_object.One_Half_Star_text, 
					2: vidmov_jav_js_object.Two_Stars_text, 
					2.5: vidmov_jav_js_object.Two_Half_Stars_text, 
					3: vidmov_jav_js_object.Three_Stars_text, 
					3.5: vidmov_jav_js_object.Three_Half_Stars_text, 
					4: vidmov_jav_js_object.Four_Stars_text, 
					4.5: vidmov_jav_js_object.Four_Half_Stars_text, 
					5: vidmov_jav_js_object.Five_Stars_text, 
					5.5: vidmov_jav_js_object.Five_Half_Stars_text, 
					6: vidmov_jav_js_object.Six_Stars_text, 
					6.5: vidmov_jav_js_object.Six_Half_Stars_text, 
					7: vidmov_jav_js_object.Seven_Stars_text, 
					7.5: vidmov_jav_js_object.Seven_Half_Stars_text, 
					8: vidmov_jav_js_object.Eight_Stars_text, 
					8.5: vidmov_jav_js_object.Eight_Half_Stars_text, 
					9: vidmov_jav_js_object.Nine_Stars_text, 
					9.5: vidmov_jav_js_object.Nine_Half_Stars_text, 
					10: vidmov_jav_js_object.Ten_Stars_text
				},
				clearButtonTitle: vidmov_jav_js_object.clear_text,
				clearCaption: vidmov_jav_js_object.not_rated_text,
				
			}
			
			if($b.css('direction') === 'rtl'){
				star_otps.rtl = true;
			}
			
			$input_control.rating(star_otps)
			.on('rating:change', function(event, value, caption) {
				$t.trigger('beeteam368ReviewActionControl'+(review_id), [post_id, value, review_id, rN_params]);
			})
			.on('rating:clear', function() {
				$t.trigger('beeteam368ReviewActionControl'+(review_id), [post_id, 0, review_id, rN_params]);
			})
			.on('rating:reset', function() {				
				$t.trigger('beeteam368ReviewActionControl'+(review_id), [post_id, 0, review_id, rN_params]);
			});
		});

        var rN_params = [];		
		var script_arr = [
			(vidmov_jav_js_object.review_library_url)+'star-rating.min.css',
			(vidmov_jav_js_object.review_library_url)+'themes/krajee-svg/theme.min.css',
			(vidmov_jav_js_object.review_library_url)+'star-rating.min.js',
			/*(vidmov_jav_js_object.review_library_url)+'themes/krajee-svg/theme.min.js',*/ /*include to library*/
		];
		
		$.getMultiScripts(script_arr).done(function() {
			$t.trigger('beeteam368ReviewLibraryControlInstalled'+(review_id), [rN_params]);
			console.log('[Review Library] Loaded.');
		}).fail(function(error) {
			console.log('[Review Library] One or more scripts failed to load.');
		});
	}
    
    $d.on('click', '.action-btn-scroll-to-review', function(){
        $('html, body').stop().animate({scrollTop:$('div[id*="beeteam368_review_"]').offset().top-150}, {duration:500}, function(){});
    });
	
	$d.trigger( 'beeteam368ReviewLibraryInstalled', []);
}));	
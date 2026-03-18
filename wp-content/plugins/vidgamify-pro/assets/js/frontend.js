/**
 * VidGamify PRO Frontend Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Animate progress bars on scroll
        $('.progress-fill').each(function() {
            var $bar = $(this);
            var width = $bar.css('width');
            
            $bar.css('width', 0);
            
            setTimeout(function() {
                $bar.animate({ width: width }, 1000, 'easeOutCubic');
            }, 300);
        });

        // Streak fire animation on hover
        $('.fire-icon').hover(
            function() {
                $(this).addClass('pulse-hover');
            },
            function() {
                $(this).removeClass('pulse-hover');
            }
        );

        // Achievement unlock notification (if using notifications)
        $(document).on('click', '.achievement-item', function() {
            var achievementName = $(this).find('.achievement-name').text();
            
            // Show tooltip or modal with achievement details
            console.log('Achievement clicked: ' + achievementName);
        });

        // Leaderboard ranking highlight
        $('.rank-1, .rank-2, .rank-3').addClass('highlight-rank');

        // Smooth scroll for leaderboard links
        $('.leaderboard-item a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            if (target && $(target).length) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 500);
            }
        });

    });

})(jQuery);

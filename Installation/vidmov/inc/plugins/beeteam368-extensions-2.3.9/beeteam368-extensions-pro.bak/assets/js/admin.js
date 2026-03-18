/**
 * VidGamify PRO Admin Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Initialize tooltips
        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Animate XP bars on page load
        $('.vidgamify-xp-bar-fill').each(function() {
            var width = $(this).data('width');
            $(this).css('width', 0);
            
            setTimeout(function() {
                $(this).animate({ width: width + '%' }, 1000);
            }, 300);
        });

        // Streak fire animation
        $('.streak-fire').hover(
            function() {
                $(this).addClass('pulse');
            },
            function() {
                $(this).removeClass('pulse');
            }
        );

        // Achievement unlock notification
        $(document).on('click', '.vidgamify-achievement-item', function() {
            var achievementName = $(this).find('strong').text();
            
            // Show notification (if using a notification system)
            console.log('Achievement: ' + achievementName);
        });

        // Leaderboard ranking highlight
        $('.rank').each(function(index) {
            if ($(this).find('.medal').length > 0) {
                $(this).addClass('highlight-rank');
            }
        });

    });

})(jQuery);

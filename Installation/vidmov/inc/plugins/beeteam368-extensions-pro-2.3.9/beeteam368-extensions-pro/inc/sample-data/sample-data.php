<?php
/* 
 * LICENSE VERIFICATION DISABLED
 * All features are now available without license verification
 * Modified for custom verification system
 */

if(!function_exists('beeteam368_add_monthly')){
    function beeteam368_add_monthly( $schedules ) {
        $schedules['vdrmonthly'] = array(
            'interval' => 2592000,
            'display' => esc_html__('Monthly', 'beeteam368-extensions-pro')
        );
        return $schedules;
    }
}
add_filter( 'cron_schedules', 'beeteam368_add_monthly' );

// Set default verified status
if(!get_option('beeteam368_verify_purchase_code', false)){
    update_option('beeteam368_verify_purchase_code', 'disabled-license-check');
    update_option('beeteam368_verify_buyer', 'beeteam368');
    update_option('beeteam368_verify_domain', '');
    update_option('beeteam368_verify_md5_code', md5('license-disabled'));
}

if(!function_exists('beeteam368_verify_purchase_code')){
    function beeteam368_verify_purchase_code($code = '', $buyer = ''){
        return 'success';
    }
}

if(!function_exists('beeteam368_vidmov_extensions_vrf_cron')){
    function beeteam368_vidmov_extensions_vrf_cron(){
        // License check disabled - do nothing
    }
}

if(!function_exists('beeteam368_vidmov_extensions_vrf_cron_activation')){
    function beeteam368_vidmov_extensions_vrf_cron_activation(){
        // License check disabled
    }
}

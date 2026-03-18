<?php
if (!class_exists('beeteam368_video_advertising')) {
    class beeteam368_video_advertising
    {
        public function __construct()
        {
			add_action('init', array($this, 'register_post_type'), 5);
			add_action('cmb2_admin_init', array($this, 'add_meta_box_for_tax'), 10);
			add_action('cmb2_admin_init', array($this, 'add_meta_box_for_post'), 10);
			add_action('cmb2_admin_init', array($this, 'register_post_meta'), 5);
			add_action('beeteam368_video_player_settings_options', array($this, 'add_opt_for_settings_area'));
			add_filter('beeteam368_video_player_settings_tab', array($this, 'add_tab_for_settings_area'));
			add_filter('beeteam368_video_single_params_hook', array($this, 'set_ad_for_video'));
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
		}
		
		function set_ad_for_video($params){
			
			if(class_exists('ARM_global_settings') || class_exists('ARM_global_settings_Lite')){
			
				$user_id = get_current_user_id();			
				if (!empty($user_id) && $user_id != 0) {
					
					$user_plans = get_user_meta($user_id, 'arm_user_plan_ids', true);
					
					if(!empty($user_plans) && is_array($user_plans) && count($user_plans) > 0){
						$suspended_plan_ids = get_user_meta($user_id, 'arm_user_suspended_plan_ids', true);
						
						if(!empty($suspended_plan_ids) && is_array($suspended_plan_ids) && count($suspended_plan_ids) > 0) {
							foreach ($suspended_plan_ids as $suspended_plan_id) {
								if(in_array($suspended_plan_id, $user_plans)) {
									unset($user_plans[array_search($suspended_plan_id, $user_plans)]);
								}
							}
						}
						
						$member_plans = beeteam368_get_option('_membership_plans', '_video_settings', array());				
						if(is_array($member_plans) && count($member_plans) > 0 && is_array($user_plans) && count($user_plans) > 0){
							foreach($member_plans as $member_plan){
								if(($found_key = array_search((string)$member_plan, $user_plans)) !== FALSE){
									return $params;
									break;
								}
							}
						}
					
					}
					
				}
				
			}

			if (defined('PMPRO_VERSION')) {
				$current_user = wp_get_current_user();
				$_pmp_membership_plans = beeteam368_get_option('_pmp_membership_plans', '_video_settings', array());

				if(is_array($_pmp_membership_plans) && count($_pmp_membership_plans) > 0 && isset($current_user->membership_level) && isset($current_user->membership_level->ID) && is_numeric($current_user->membership_level->ID)){
					foreach($_pmp_membership_plans as $pmp_member_plan){
						if($current_user->membership_level->ID === $pmp_member_plan){
							return $params;
							break;
						}
						
					}
				}
			}

			$params = apply_filters('beeteam368_handle_ads_params_before', $params);
			
			if(is_array($params) && isset($params['post_id']) && is_numeric($params['post_id']) && $params['post_id'] > 0){
				$post_id = $params['post_id'];
				$fn_ad_id = 0;
				$ad_id = get_post_meta($post_id, BEETEAM368_PREFIX . '_video_ads', true);
				
				if($ad_id === 'off'){
					return $params;
				}
				
				if($ad_id!='' && $ad_id!='off' && is_numeric($ad_id) && $ad_id > 0){
					$fn_ad_id = $ad_id;
				}
				
				if($fn_ad_id === 0 && is_single() && (is_singular(BEETEAM368_POST_TYPE_PREFIX . '_playlist') || is_singular(BEETEAM368_POST_TYPE_PREFIX . '_series'))){
					$ad_id = get_post_meta(get_the_ID(), BEETEAM368_PREFIX . '_video_ads', true);
					
					if($ad_id === 'off'){
						return $params;
					}
					
					if($ad_id!='' && $ad_id!='off' && is_numeric($ad_id) && $ad_id > 0){
						$fn_ad_id = $ad_id;
					}
				}
				
				if($fn_ad_id === 0){
					$terms = get_the_terms($post_id, BEETEAM368_POST_TYPE_PREFIX . '_video_category');
					if($terms && !is_wp_error($terms)){
						foreach($terms as $term){							
							$ad_id = get_term_meta($term->term_id, BEETEAM368_PREFIX . '_video_ads', true);	
							
							if($ad_id === 'off' && count($terms) === 1){
								return $params;
								break;
							}
								
							if($ad_id!='' && $ad_id!='off' && is_numeric($ad_id) && $ad_id > 0){
								$fn_ad_id = $ad_id;
								break;
							}
						}
					}
				}
				
				if($fn_ad_id === 0){
					$post_tags = get_the_tags($post_id);
					
					if($post_tags){
						foreach($post_tags as $tag){
							$ad_id = get_term_meta($tag->term_id, BEETEAM368_PREFIX . '_video_ads', true);	
							
							if($ad_id === 'off' && count($post_tags) === 1){
								return $params;
								break;
							}
								
							if($ad_id!='' && $ad_id!='off' && is_numeric($ad_id) && $ad_id > 0){
								$fn_ad_id = $ad_id;
								break;
							}							
						}	
					}
				}
				
				if($fn_ad_id === 0){
					$ad_id = beeteam368_get_option('_video_ads', '_video_settings', 'off');
					if($ad_id!='' && $ad_id!='off' && is_numeric($ad_id) && $ad_id > 0){	
						$fn_ad_id = $ad_id;					
					}
				}
				
				if($fn_ad_id > 0){
					$_ads_type = trim(get_post_meta($fn_ad_id, BEETEAM368_PREFIX . '_ads_type', true));
					if($_ads_type==''){
						$_ads_type = 'mixed';
					}
					
					switch($_ads_type){
						case 'mixed':
							$params['video_ads_type'] = 'mixed';
							$params['video_ads_params'] = get_post_meta($fn_ad_id, BEETEAM368_PREFIX . '_ads_mixed', true);
							break;
                        
                        /*Googe IMA Develop  
                        case 'google_ima':
							$params['video_ads_type'] = 'google_ima';
							$params['video_ads_params'] = array(get_post_meta($fn_ad_id, BEETEAM368_PREFIX . '_google_ima_tag_url', true));
							break;
                        End - Googe IMA Develop*/   
					}
				}
				
			}

			$params = apply_filters('beeteam368_handle_ads_params_after', $params);
			
			return $params;
		}
		
		function register_post_type()
        {
			$custom_permalink = 'video_advertising';
			register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_video_ads',
				apply_filters('beeteam368_register_post_type_video_advertising',
					array(
						'labels' => array(
								'name'                  => esc_html__('Video Ads', 'beeteam368-extensions-pro'),
								'singular_name'         => esc_html__('Video Ad', 'beeteam368-extensions-pro'),
								'menu_name'             => esc_html__('Video Advertising', 'beeteam368-extensions-pro'),
								'add_new'               => esc_html__('Add Video Ad', 'beeteam368-extensions-pro'),
								'add_new_item'          => esc_html__('Add New Video Ad', 'beeteam368-extensions-pro'),
								'edit'                  => esc_html__('Edit', 'beeteam368-extensions-pro'),
								'edit_item'             => esc_html__('Edit Video Ad', 'beeteam368-extensions-pro'),
								'new_item'              => esc_html__('New Video Ad', 'beeteam368-extensions-pro'),
								'view'                  => esc_html__('View Video Ad', 'beeteam368-extensions-pro'),
								'view_item'             => esc_html__('View Video Ad', 'beeteam368-extensions-pro'),
								'search_items'          => esc_html__('Search Video Ads', 'beeteam368-extensions-pro'),
								'not_found'             => esc_html__('No Video Ads found', 'beeteam368-extensions-pro'),
								'not_found_in_trash'    => esc_html__('No Video Ads found in trash', 'beeteam368-extensions-pro'),
								'parent'                => esc_html__('Parent Video Ad', 'beeteam368-extensions-pro'),
								'featured_image'        => esc_html__('Video Ad Image', 'beeteam368-extensions-pro'),
								'set_featured_image'    => esc_html__('Set Video Ad image', 'beeteam368-extensions-pro'),
								'remove_featured_image' => esc_html__('Remove Video Ad image', 'beeteam368-extensions-pro'),
								'use_featured_image'    => esc_html__('Use as Video Ad image', 'beeteam368-extensions-pro'),
								'insert_into_item'      => esc_html__('Insert into Video Ad', 'beeteam368-extensions-pro'),
								'uploaded_to_this_item' => esc_html__('Uploaded to this Video Ad', 'beeteam368-extensions-pro'),
								'filter_items_list'     => esc_html__('Filter Video Ads', 'beeteam368-extensions-pro'),
								'items_list_navigation' => esc_html__('Video Ads navigation', 'beeteam368-extensions-pro'),
								'items_list'            => esc_html__('Video Ads list', 'beeteam368-extensions-pro'),
							),
						'description'         => esc_html__('This is where you can add new Video Ads to your site.', 'beeteam368-extensions-pro'),
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => BEETEAM368_PREFIX . '_video_ads',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => $custom_permalink ? array('slug' => untrailingslashit($custom_permalink), 'with_front' => false, 'feeds' => true) : false,
						'query_var'           => true,
						'supports'            => array('title'),
						'has_archive'         => true,
						'show_in_nav_menus'   => true,
						'menu_icon'			  => 'dashicons-welcome-view-site',
						'show_in_menu'	  	  => 'edit.php?post_type=' . BEETEAM368_POST_TYPE_PREFIX . '_video',
						'menu_position'		  => 5,
						'capabilities'		  => array(							
						),
					)
				)
			);
		}
		
		function register_post_meta(){
            $object_types = apply_filters('beeteam368_video_ads_config_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video_ads'));

            $ads_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_video_ads_config',
                'title' => esc_html__('Video Ads Settings', 'beeteam368-extensions-pro'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));
            
            $ads_settings->add_field( array(
                'id'        	=> BEETEAM368_PREFIX . '_ads_type',
                'name'      	=> esc_html__('Video Ad Type And Format', 'beeteam368-extensions-pro'),
                'type'      	=> 'radio_inline',
                'options' 		=> apply_filters('beeteam368_ad_type_format_config', array(
					'mixed' => esc_html__('Mixed Mode', 'beeteam368-extensions-pro'),
                    //'vpaid_2' => esc_html__('VPAID 2', 'beeteam368-extensions-pro'),
                    
                    /*Googe IMA Develop
                    'google_ima' => esc_html__('Google IMA', 'beeteam368-extensions-pro'),
                    End - Googe IMA Develop*/
                )),
                'default' => apply_filters('beeteam368_ad_type_format_default_config', 'mixed'),
                'desc' => wp_kses(__(
                    'Choose a specific ad type for this group.', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array())
                ),
            ));
            
			/*Mixed Ads*/
			$dynamic_ads = $ads_settings->add_field(array(
				'id'          => BEETEAM368_PREFIX . '_ads_mixed',
				'type'        => 'group',	
				'description' => esc_html__('You can mix different types of ads and show them while the user is watching the video.', 'beeteam368-extensions-pro'),		
				'options'     => array(
					'group_title'   => esc_html__('Advertisement {#}', 'beeteam368-extensions-pro'),
					'add_button'	=> esc_html__('Add Advertisement', 'beeteam368-extensions-pro'),
					'remove_button' => esc_html__('Remove Advertisement', 'beeteam368-extensions-pro'),
					'sortable'		=> true,				
					'closed'		=> false,
				),
				'repeatable'  => true,				
			));			
			
				$ads_settings->add_group_field($dynamic_ads, array(
					'id'        	=> BEETEAM368_PREFIX . '_mixed_type',
					'name'      	=> esc_html__('Type', 'beeteam368-extensions-pro'),
					'type'      	=> 'select',
					'options' 		=> array(
						'image' => esc_html__('Image', 'beeteam368-extensions-pro'),
						'video' => esc_html__('Video', 'beeteam368-extensions-pro'),
						'html' => esc_html__('HTML', 'beeteam368-extensions-pro'),
						'vast' => esc_html__('VAST', 'beeteam368-extensions-pro'),
					),
					'default' => 'image',				
				));
				
				/*IMAGE*/					
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('[Image] Source for Desktop', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_image_source',
					'type' => 'file',
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'image',
					),
				));	
				
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('[Image] Source for Mobile', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_image_source_mobile',
					'type' => 'file',
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'image',
					),
				));	
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__( 'Link Target', 'beeteam368-extensions-pro'),
					'id'   => BEETEAM368_PREFIX . '_image_link',
					'type' => 'text',
					'description' => esc_html__( 'Enter URL if you want this image to have a link.', 'beeteam368-extensions-pro'),
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'image',
					),
				));/*IMAGE*/
				
				/*HTML5 Video*/					
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('Video Source', 'beeteam368-extensions-pro'),
					'desc' => wp_kses(__('Recommended Format Solution: *.mp4, *.webm, *.ogg or link to hls format (*m3u8)', 'beeteam368-extensions-pro'), 
								array('br'=>array(), 'code'=>array(), 'strong'=>array())		
							  ),
					'id'   => BEETEAM368_PREFIX . '_video_source',
					'type' => 'file',
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'video',
					),
				));	
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('Link Target', 'beeteam368-extensions-pro'),
					'id'   => BEETEAM368_PREFIX . '_video_link',
					'type' => 'text',
					'description' => esc_html__( 'Enter URL if you want this video to have a link.', 'beeteam368-extensions-pro'),
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'video',
					),
				));/*HTML5 Video*/	
				
				/*HTML*/				
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('[HTML] Source for Desktop', 'beeteam368-extensions-pro'),
					'id'   => BEETEAM368_PREFIX . '_html_source',
					'description' => esc_html__( 'You can also use Google Adsense code here. Or HTML blocks developed by you.', 'beeteam368-extensions-pro'),
					'type' => 'textarea_code',
					'options' => array( 'disable_codemirror' => true ),
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'html',
					),
				));
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__('[HTML] Source for Mobile', 'beeteam368-extensions-pro'),
					'id'   => BEETEAM368_PREFIX . '_html_source_mobile',
					'description' => esc_html__( 'You can also use Google Adsense code here. Or HTML blocks developed by you.', 'beeteam368-extensions-pro'),
					'type' => 'textarea_code',
					'options' => array( 'disable_codemirror' => true ),
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'html',
					),
				));/*HTML*/
				
				/*VAST*/
				$ads_settings->add_group_field($dynamic_ads, array(
					'name' => esc_html__( 'The url of the VAST XML', 'beeteam368-extensions-pro'),
					'id'   => BEETEAM368_PREFIX . '_vast_tag_url',
					'type' => 'textarea_code',
                    'options' => array( 'disable_codemirror' => true ),
					'description' => esc_html__( 'Please note the VAST tag XML response Content-Type must be either application/xml or text/xml.', 'beeteam368-extensions-pro'),
					'repeatable' => false,
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'vast',
					),
				));
                $ads_settings->add_group_field($dynamic_ads, array(
					'name'    => esc_html__( 'Link behavior', 'beeteam368-extensions-pro'),
					'id'      => BEETEAM368_PREFIX . '_link_behavior',
					'type'    => 'select',
					'default' => 'default',
					'options' => array(
						'default' => esc_html__('Default', 'beeteam368-extensions-pro'),
						'entire_player' => esc_html__('Entire player', 'beeteam368-extensions-pro'),
					),
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => 'vast',
					),
				));/*VAST*/
				
				/*ad size & position*/
				$ads_settings->add_group_field($dynamic_ads, array(
					'name'    => esc_html__( 'Ad Size for Desktop', 'beeteam368-extensions-pro'),
					'id'      => BEETEAM368_PREFIX . '_size_desktop',
					'type'    => 'select',
					'default' => '336x280',
					'options' => array(
						'336x280' => esc_html__('336x280 (px)', 'beeteam368-extensions-pro'),
						'300x250' => esc_html__('300x250 (px)', 'beeteam368-extensions-pro'),
						'728x90' => esc_html__('728x90 (px)', 'beeteam368-extensions-pro'),
						'468x60' => esc_html__('468x60 (px)', 'beeteam368-extensions-pro'),
						'250x250' => esc_html__('250x250 (px)', 'beeteam368-extensions-pro'),
						'200x200' => esc_html__('200x200 (px)', 'beeteam368-extensions-pro'),
					),
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => wp_json_encode( array( 'image', 'html' ) ),
					),
				));	
				$ads_settings->add_group_field($dynamic_ads, array(
					'name'    => esc_html__( 'Ad Size for Mobile', 'beeteam368-extensions-pro'),
					'id'      => BEETEAM368_PREFIX . '_size_mobile',
					'type'    => 'select',
					'default' => '300x250',
					'options' => array(
						'300x250' => esc_html__('300x250 (px)', 'beeteam368-extensions-pro'),
						'320x50' => esc_html__('320x50 (px)', 'beeteam368-extensions-pro'),
						'320x100' => esc_html__('320x100 (px)', 'beeteam368-extensions-pro'),
						'300x50' => esc_html__('300x50 (px)', 'beeteam368-extensions-pro'),
						'250x250' => esc_html__('250x250 (px)', 'beeteam368-extensions-pro'),
						'200x200' => esc_html__('200x200 (px)', 'beeteam368-extensions-pro'),
					),
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => wp_json_encode( array( 'image', 'html' ) ),
					),
				));					
				$ads_settings->add_group_field($dynamic_ads, array(
					'name'    => esc_html__( 'Vertial Align', 'beeteam368-extensions-pro'),
					'id'      => BEETEAM368_PREFIX . '_vertial_align',
					'type'    => 'select',
					'default' => 'bottom',
					'options' => array(
						'bottom' => esc_html__('Bottom', 'beeteam368-extensions-pro'),
						'middle' => esc_html__('Middle', 'beeteam368-extensions-pro'),
						'top' => esc_html__('Top', 'beeteam368-extensions-pro'),
					),
					'attributes' => array(
						'data-conditional-id' => wp_json_encode( array( $dynamic_ads, BEETEAM368_PREFIX . '_mixed_type' ) ),
						'data-conditional-value' => wp_json_encode( array( 'image', 'html' ) ),
					),
				));	/*ad size & position*/
				
				$ads_settings->add_group_field($dynamic_ads, array(
					'id'        	=> BEETEAM368_PREFIX . '_mixed_time_show_ad',
					'name'      	=> esc_html__( 'Time to show ads', 'beeteam368-extensions-pro'),
					'type'      	=> 'select',
					'options' 		=> array(
						'pre_roll' => esc_html__('Pre-Roll', 'beeteam368-extensions-pro'),
						'post_roll' => esc_html__('Post-Roll', 'beeteam368-extensions-pro'),
						'mid_roll' => esc_html__('Mid-Roll', 'beeteam368-extensions-pro'),
						'custom' => esc_html__('Custom', 'beeteam368-extensions-pro'),
					),
					'default' => 'pre_roll',
					'desc' => wp_kses(__(
						'If you want to set a specific time, select "Custom" and enter the value in the field below ( <strong>[Custom] Ad shows up after</strong> )', 'beeteam368-extensions-pro'),
						array('br'=>array(), 'code'=>array(), 'strong'=>array())
					),
				));
					$ads_settings->add_group_field($dynamic_ads, array(
						'id'        	=> BEETEAM368_PREFIX . '_mixed_time_show_ad_custom',
						'name'      	=> esc_html__( '[Custom] Ad shows up after (seconds)', 'beeteam368-extensions-pro'),
						'type'      	=> 'text',
						'attributes' => array(
							'type' => 'number',
						),	
						'default' => 5,			
						'desc' => wp_kses(__(
							'If blank, defaults to: 5', 'beeteam368-extensions-pro'),
							array('br'=>array(), 'code'=>array(), 'strong'=>array())
						),						
					));
									
				$ads_settings->add_group_field($dynamic_ads, array(
					'id'        	=> BEETEAM368_PREFIX . '_mixed_time_skip_ad',
					'name'      	=> esc_html__( 'Skip Ad - Clickable After (seconds)', 'beeteam368-extensions-pro'),
					'type'      	=> 'text',
					'attributes' => array(
						'type' => 'number',
						'min' => '3',
					),	
					'default' => 5,			
					'desc' => wp_kses(__(
						'If blank, defaults to: 5', 'beeteam368-extensions-pro'),
						array('br'=>array(), 'code'=>array(), 'strong'=>array())
					),
				));
				
				$ads_settings->add_group_field($dynamic_ads, array(
					'id'        	=> BEETEAM368_PREFIX . '_mixed_time_hide_ad',
					'name'      	=> esc_html__( 'Hide Ad After (seconds)', 'beeteam368-extensions-pro'),
					'type'      	=> 'text',
					'attributes' => array(
						'type' => 'number',
						'min' => '0',
					),	
					'default' => 20,			
					'desc' => wp_kses(__(
						'If blank, defaults to: 20. Enter 0 if you want users to manually skip this ad.', 'beeteam368-extensions-pro'),
						array('br'=>array(), 'code'=>array(), 'strong'=>array())
					),
				));            
            /*Mixed Ads*/
            
            /*Google IMA
            $ads_settings->add_field(array(
                'name' => esc_html__( '[Google IMA] Ad tag URLs', 'beeteam368-extensions-pro'),
                'id'   => BEETEAM368_PREFIX . '_google_ima_tag_url',
                'type' => 'textarea_code',
                'options' => array( 'disable_codemirror' => true ),
                'description' => esc_html__( 'Note: Google IMA ads only work with self-hosted videos, Youtube, Vimeo or Dailymotion when working on VidMov\'s player.', 'beeteam368-extensions-pro'),
                'repeatable' => false,
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_ads_type',
                    'data-conditional-value' => 'google_ima',
                ),
            ));
            Google IMA*/
        }
		
		public static function get_videos_ads_settings($params = array('type' => 'all')){
			
			$args_query = array(
				'post_type'				=> array(BEETEAM368_POST_TYPE_PREFIX . '_video_ads'),
				'posts_per_page' 		=> -1,
				'post_status' 			=> 'publish',
				'ignore_sticky_posts' 	=> 1,
				'fields'				=> 'ids',				
			);
			
			switch($params['type']){
				case 'all':
					$ads = get_posts($args_query);
					
					if($ads){
						return $ads;
					}else{
						return [];
					}
					break;
					
				case BEETEAM368_POST_TYPE_PREFIX . '_video_category':
					break;
					
				case 'post_tag':
					break;
				
				case 'single':
					break;
			
			}
			
		}
		
		public static function get_videos_ads_otps($level){
			$list_ads = self::get_videos_ads_settings(array('type' => 'all'));
			if($level === 1){
				$ads_otp = array(					
					'off' => esc_html__('Disable', 'beeteam368-extensions-pro'),
				);
			}else{
				$ads_otp = array(
					'' => esc_html__('Default', 'beeteam368-extensions-pro'),
					'off' => esc_html__('Disable', 'beeteam368-extensions-pro'),
				);
			}
			foreach($list_ads as $ad){
				$ads_otp[$ad] = get_the_title($ad);
			}
			
			return $ads_otp;
		}
		
		function add_tab_for_settings_area($tabs){
			if(is_array($tabs)){
				$tabs[] = BEETEAM368_PREFIX . '_video_ads';
				$tabs[] = BEETEAM368_PREFIX . '_video_vast_time_out';
				$tabs[] = BEETEAM368_PREFIX . '_membership_plans';
				$tabs[] = BEETEAM368_PREFIX . '_pmp_membership_plans';
				$tabs[] = BEETEAM368_PREFIX . '_prime_video';
			}
			
			return $tabs;
		}
		
		function add_opt_for_settings_area($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Video Advertising', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose display ads for videos.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_video_ads',
                'type' => 'select',
                'default' => 'off',
                'options' => self::get_videos_ads_otps(1),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('VAST TimeOut', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('This parameter lets you set the time, in milliseconds, to wait for the VAST to load. (Default: 888).', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_video_vast_time_out',
				'type' => 'text',
				'attributes' => array(
					'type' => 'number',
					'min' => '368',
				),	
				'default' => 888,
            ));
			
			if(class_exists('ARM_global_settings') || class_exists('ARM_global_settings_Lite')){
			
				global $wpdb, $ARMember, $arm_subscription_plans;
				
				$all_plans = [];
				
				$form_result = $arm_subscription_plans->arm_get_all_subscription_plans();
				if(!empty($form_result)){
					foreach($form_result as $planData){
                        
                        if(class_exists('ARM_Plan')){
                            $planObj = new ARM_Plan();
                        }elseif(class_exists('ARM_Plan_Lite')){
                            $planObj = new ARM_Plan_Lite(0);
                        }
                        
						if(isset($planObj)){
						  $planObj->init((object) $planData);						
						  $all_plans[$planData['arm_subscription_plan_id']] = esc_html(stripslashes($planObj->name));
                        }
					}
				}
				
				if(count($all_plans) > 0){
					$settings_options->add_field(array(
						'name' => esc_html__('[ArMember] Hide Ads for Specific Plans', 'beeteam368-extensions-pro'),
						'desc' => esc_html__('Choose the right plans for this item.', 'beeteam368-extensions-pro'),
						'id' => BEETEAM368_PREFIX . '_membership_plans',
						'type' => 'multicheck',
						'options' => $all_plans,
					));
				}
				
			}

			if (defined('PMPRO_VERSION')) {
				global $wpdb;
				$sqlQuery 	= "SELECT * FROM $wpdb->pmpro_membership_levels WHERE 1 = 1 ORDER BY id ASC";
				$levels 	= $wpdb->get_results($sqlQuery, OBJECT);

				$all_plans_pmp = [];

				if(is_array($levels) && count($levels) > 0){
					foreach($levels as $level){
						$all_plans_pmp[$level->id] = $level->name;	
					}
				}
				
				if(count($all_plans_pmp) > 0){
					$settings_options->add_field(array(
						'name' => esc_html__('[Paid Membership Pro] Hide Ads for Specific Plans', 'beeteam368-extensions-pro'),
						'desc' => esc_html__('Choose the right plans for this item.', 'beeteam368-extensions-pro'),
						'id' => BEETEAM368_PREFIX . '_pmp_membership_plans',
						'type' => 'multicheck',
						'options' => $all_plans_pmp,
					));
				}
				
			}
			
			/*
			if (class_exists('WooCommerce', false)){
				$settings_options->add_field(array(
					'name' => esc_html__('[WooCommerce] Hide Ads for Prime Videos', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_prime_video',
					'type' => 'select',
					'default' => 'off',
					'options' => array(
						'off' => esc_html__('Disable', 'beeteam368-extensions-pro'),
						'on' => esc_html__('Enable', 'beeteam368-extensions-pro'),						
					),
				));
			}
			*/
			
		}
		
		function add_meta_box_for_post(){
			$object_types = apply_filters('beeteam368_post_video_ads_settings_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_playlist', BEETEAM368_POST_TYPE_PREFIX . '_series'));

            $video_ads_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_post_video_ads_settings',
                'title' => esc_html__('Video Ads Settings', 'beeteam368-extensions-pro'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));
			
			$video_ads_settings->add_field(array(
                'name' => esc_html__('Video Advertising', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose display ads for videos. Select "Default" to use settings in Theme Settings > Video Settings > Player Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_video_ads',
                'type' => 'select',
                'default' => '',
                'options' => self::get_videos_ads_otps(2),
            ));
		}
		
		function add_meta_box_for_tax(){
			$taxonomies = array(
                BEETEAM368_POST_TYPE_PREFIX . '_video_category',
                'post_tag',
            );
			
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_video_ads_settings',
                'title' => esc_html__('Video Ads Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('term'),
                'taxonomies' => $taxonomies,
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Video Advertising', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Select "Default" to use settings in Theme Settings > Video Settings > Player Settings.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_video_ads',
                'type' => 'select',
                'default' => '',
                'options' => self::get_videos_ads_otps(2),
            ));
		}
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){              
				$define_js_object['video_vast_time_out'] = beeteam368_get_option('_video_vast_time_out', '_video_settings', 888);
            }

            return $define_js_object;
        }
	}
}

global $beeteam368_video_advertising;
$beeteam368_video_advertising = new beeteam368_video_advertising();
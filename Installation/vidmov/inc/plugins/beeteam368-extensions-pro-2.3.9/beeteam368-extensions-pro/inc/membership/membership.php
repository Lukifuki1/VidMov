<?php
if(!class_exists('beeteam368_membership')){
	
	class beeteam368_membership{
        public function __construct()
        {
            add_action('cmb2_admin_init', array($this, 'membership_custom_post_tax_settings'));
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			
			add_filter('beeteam368_media_protect_html', array($this, 'protect'), 30, 4);
			add_filter('beeteam368_media_protect_html', array($this, 'pmp_protect'), 30, 4);
			
			add_action('beeteam368_show_membership_on_featured_img', array($this, 'membership_icon'), 10, 2);
			
			add_action('beeteam368_membership_nav_icon', array($this, 'membership_icon_in_nav'), 10, 2);
			
			add_action('beeteam368_membership_transactions_dropdown_login', array($this, 'membership_menu'));
			
			add_filter('beeteam368_default_sidebar_control', array($this, 'element_sidebar_control'), 10, 1);

			add_filter('body_class', array($this, 'body_classes'));
        }
		
		function body_classes($classes){
			if (defined('MEMBERSHIP_VERSION')) {
				$classes[] = 'beeteam368-armember-pro';
			}
            
            if(class_exists('ARM_global_settings_Lite')){
                $classes[] = 'beeteam368-armember-lite-4';
            }
			
			return $classes;
		}
		
		function membership_menu($user_id){
			$_membership_transactions_page = beeteam368_get_option('_membership_transactions_page', '_theme_settings', '');	
			
			if(!is_numeric($_membership_transactions_page) || $_membership_transactions_page < 1){
				return;
			}
		?>
            <a href="<?php echo get_permalink($_membership_transactions_page);?>" class="membership-menu flex-row-control flex-vertical-middle icon-drop-down-url">
                <span class="beeteam368-icon-item">
                   <i class="fas fa-money-check-alt"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Membership Transactions', 'beeteam368-extensions-pro')?></span>
                
            </a>
            
        <?php	
		}
		
		function membership_icon_in_nav($position, $beeteam368_header_style){
			$_membership_plans_page = beeteam368_get_option('_membership_plans_page', '_theme_settings', '');
			
			if(!is_numeric($_membership_plans_page) || $_membership_plans_page < 1){
				return;
			}
		?>
        	<a href="<?php echo get_permalink($_membership_plans_page);?>" class="beeteam368-icon-item beeteam368-top-menu-membership tooltip-style bottom-center">
            	<i class="fas fa-user-shield"></i>
                <span class="tooltip-text"><?php echo esc_html__('Membership Plans', 'beeteam368-extensions-pro');?></span>
            </a>
        <?php	
		}
		
		function membership_icon($post_id, $params){
			$member_plans = self::get_post_protect_plans($post_id);
			
			if(is_array($member_plans) && count($member_plans) > 0){
				$_i = 1;
				foreach($member_plans as $plan){
                    
                    if(class_exists('ARM_Plan')){
                        $planObj = new ARM_Plan();
                    }elseif(class_exists('ARM_Plan_Lite')){
                        $planObj = new ARM_Plan_Lite(0);
                    }
					
                    if(isset($planObj)){
                        $planObj -> init((object) $planObj->arm_get_plan_detail($plan));
					   echo '<span class="membership-icon font-size-12 flex-vertical-middle" data-plan-id="'.esc_attr($plan).'"><i class="fas fa-crown"></i>&nbsp;&nbsp;<span>'.esc_html(stripslashes($planObj->name)).'</span></span>';
                    }
										
					$_i++;
				}
			}
		}
		
		function membership_custom_post_tax_settings(){

			$taxonomies = array(
				'post_tag',
				BEETEAM368_POST_TYPE_PREFIX . '_video_category',
				BEETEAM368_POST_TYPE_PREFIX . '_audio_category',
				BEETEAM368_POST_TYPE_PREFIX . '_playlist_category',
				BEETEAM368_POST_TYPE_PREFIX . '_series_category'
			);
			
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
					
					$settings_options = new_cmb2_box(array(
						'id' => BEETEAM368_PREFIX . '_membership_settings',
						'title' => esc_html__('[ArMember] Membership Settings', 'beeteam368-extensions-pro'),
						'object_types' => apply_filters('beeteam368_membership_settings_object_types', array('term', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio')),
						'taxonomies' => $taxonomies,
					));
					
					$settings_options->add_field(array(
						'name' => esc_html__('[ArMember] Membership Plans', 'beeteam368-extensions-pro'),
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
					$settings_options = new_cmb2_box(array(
						'id' => BEETEAM368_PREFIX . '_pmp_membership_settings',
						'title' => esc_html__('[Paid Membership Pro] Membership Settings', 'beeteam368-extensions-pro'),
						'object_types' => apply_filters('beeteam368_pmp_membership_settings_object_types', array('term', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio')),
						'taxonomies' => $taxonomies,
					));
					
					$settings_options->add_field(array(
						'name' => esc_html__('[Paid Membership Pro] Membership Plans', 'beeteam368-extensions-pro'),
						'desc' => esc_html__('Choose the right plans for this item.', 'beeteam368-extensions-pro'),
						'id' => BEETEAM368_PREFIX . '_pmp_membership_plans',
						'type' => 'multicheck',
						'options' => $all_plans_pmp,
					));
				}
				
			}
			
		}
		
		public static function get_post_protect_plans($post_id){
			
			if(class_exists('ARM_global_settings')  || class_exists('ARM_global_settings_Lite')){
				$member_plans = get_post_meta($post_id, BEETEAM368_PREFIX . '_membership_plans', true);
				
				if(!is_array($member_plans) || count($member_plans) < 1){
					
					$post_type = get_post_type($post_id);
					
					$fn_terms_membership = array();
					$terms = get_the_terms($post_id, $post_type.'_category');
					if($terms && !is_wp_error($terms)){
						foreach($terms as $term){							
							$terms_membership = get_term_meta($term->term_id, BEETEAM368_PREFIX . '_membership_plans', true);
							if(is_array($terms_membership)){
								$fn_terms_membership = array_merge($fn_terms_membership, $terms_membership);
							}
						}
					}
					
					$fn_post_tags_membership = array();
					$post_tags = get_the_tags($post_id);
					if($post_tags){
						foreach($post_tags as $tag){							
							$post_tags_membership = get_term_meta($tag->term_id, BEETEAM368_PREFIX . '_membership_plans', true);
							if(is_array($post_tags_membership)){
								$fn_post_tags_membership = array_merge($fn_post_tags_membership, $post_tags_membership);	
							}
						}
					}
					
					$member_plans = array_unique(array_merge($fn_terms_membership, $fn_post_tags_membership));
				}
				
				if(is_array($member_plans) && count($member_plans) > 0){
					return $member_plans;
				}
				
				return [];
			}
			
			return [];
		}

		public static function pmp_get_post_protect_plans($post_id){
			
			if (defined('PMPRO_VERSION')) {
				$member_plans = get_post_meta($post_id, BEETEAM368_PREFIX . '_pmp_membership_plans', true);
				
				if(!is_array($member_plans) || count($member_plans) < 1){
					
					$post_type = get_post_type($post_id);
					
					$fn_terms_membership = array();
					$terms = get_the_terms($post_id, $post_type.'_category');
					if($terms && !is_wp_error($terms)){
						foreach($terms as $term){							
							$terms_membership = get_term_meta($term->term_id, BEETEAM368_PREFIX . '_pmp_membership_plans', true);
							if(is_array($terms_membership)){
								$fn_terms_membership = array_merge($fn_terms_membership, $terms_membership);
							}
						}
					}
					
					$fn_post_tags_membership = array();
					$post_tags = get_the_tags($post_id);
					if($post_tags){
						foreach($post_tags as $tag){							
							$post_tags_membership = get_term_meta($tag->term_id, BEETEAM368_PREFIX . '_pmp_membership_plans', true);
							if(is_array($post_tags_membership)){
								$fn_post_tags_membership = array_merge($fn_post_tags_membership, $post_tags_membership);	
							}
						}
					}
					
					$member_plans = array_unique(array_merge($fn_terms_membership, $fn_post_tags_membership));
				}
				
				if(is_array($member_plans) && count($member_plans) > 0){
					return $member_plans;
				}
				
				return [];
			}
			
			return [];
		}
		
		function protect($content, $post_id, $trailer_url, $type){
			if(class_exists('ARM_global_settings') || class_exists('ARM_global_settings_Lite')){
				
				$member_plans = self::get_post_protect_plans($post_id);				
								
				if(is_array($member_plans) && count($member_plans) > 0){
					
					global $mycred_partial_content_sale;
					$mycred_partial_content_sale = true;
					
					$img_background_cover = '';
					if(has_post_thumbnail($post_id) && $imgsource = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')){
						$img_background_cover = 'style="background-image:url('.esc_url($imgsource[0]).');"';
					}	
					
					$btn_trailer = '';	
					if($trailer_url!=''){
						$btn_trailer = '<a href="'.esc_url(add_query_arg(array('trailer' => 1), beeteam368_get_post_url($post_id)) ).'" class="btnn-default btnn-primary"><i class="fas fa-photo-video icon"></i><span>'.esc_html__('Trailer', 'beeteam368-extensions-pro').'</span></a>';
					}
					
					$_i = 1;
					$all_plans_name = '';
					foreach($member_plans as $plan){
                        
						if(class_exists('ARM_Plan')){
                            $planObj = new ARM_Plan();
                        }elseif(class_exists('ARM_Plan_Lite')){
                            $planObj = new ARM_Plan_Lite(0);
                        }
                        
                        if(isset($planObj)){
						  $planObj -> init((object) $planObj->arm_get_plan_detail($plan));
						  $all_plans_name.= '<span class="plan-special font-size-12">' . esc_html(stripslashes($planObj->name)) . '</span>';	
                        }
                        
						$_i++;
					}
					
					$plans_url = '#';
					$_membership_plans_page = beeteam368_get_option('_membership_plans_page', '_theme_settings', '');
					if($_membership_plans_page != '' && is_numeric($_membership_plans_page) && $_membership_plans_page > 0){
						$plans_url = get_permalink($_membership_plans_page);
					}
					
					$content = apply_filters('beeteam368_membership_restrict_content_html', '
					[arm_restrict_content plan="'.implode(',', $member_plans).'," type="show"]
						'.$content.'
						[armelse]
						<div class="beeteam368-player beeteam368-player-protect dark-mode">
							<div class="beeteam368-player-wrapper temporaty-ratio">
								<div class="player-banner flex-vertical-middle" '.$img_background_cover.'>
									<div class="membership-info-wrapper">
										<h2 class="h1 h4-mobile membership-heading">'.esc_html__('Premium Content', 'beeteam368-extensions-pro').'</h2>
										<div class="membership-descriptions">'.str_replace( '$$plans$$', $all_plans_name, esc_html__('This content is for $$plans$$ members only.', 'beeteam368-extensions-pro') ).'</div>
										<a href="'.$plans_url.'" class="btnn-default btnn-primary"><i class="fas fa-users-cog icon"></i><span>'.esc_html__('Choose a plan', 'beeteam368-extensions-pro').'</span></a>
										'.$btn_trailer.'
									</div>
								</div>
							</div>	
						</div>
					[/arm_restrict_content]
					');
					
					return do_shortcode($content);
				}
			}
			
			return $content;
		}

		function pmp_protect($content, $post_id, $trailer_url, $type){

			if(current_user_can( 'administrator' )){
				return $content;
			}

			if (defined('PMPRO_VERSION')) {
				
				$member_plans = self::pmp_get_post_protect_plans($post_id);				
								
				if(is_array($member_plans) && count($member_plans) > 0){
					
					global $mycred_partial_content_sale;
					$mycred_partial_content_sale = true;
					
					$img_background_cover = '';
					if(has_post_thumbnail($post_id) && $imgsource = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')){
						$img_background_cover = 'style="background-image:url('.esc_url($imgsource[0]).');"';
					}	
					
					$btn_trailer = '';	
					if($trailer_url!=''){
						$btn_trailer = '<a href="'.esc_url(add_query_arg(array('trailer' => 1), beeteam368_get_post_url($post_id)) ).'" class="btnn-default btnn-primary"><i class="fas fa-photo-video icon"></i><span>'.esc_html__('Trailer', 'beeteam368-extensions-pro').'</span></a>';
					}
					
					global $wpdb;
					$current_user = wp_get_current_user();

					$_i = 1;
					$all_plans_name = '';
					foreach($member_plans as $plan){

						if ( isset($current_user->membership_level) && isset($current_user->membership_level->ID) && is_numeric($current_user->membership_level->ID) && $current_user->membership_level->ID === $plan) {

							return $content;

							break;
						}
						
						$sqlQuery = "SELECT * FROM $wpdb->pmpro_membership_levels WHERE id = '".$plan."'";
						$level = $wpdb->get_results($sqlQuery, OBJECT);
						
                        if($level && !empty($level) && $level != null){						  
						  $all_plans_name.= '<span class="plan-special font-size-12">' . esc_html(stripslashes($level[0]->name)) . '</span>';	
                        }
                        
						$_i++;
					}
					
					$plans_url = '#';

					if(function_exists('pmpro_getOption')){
						$levels_page_id = pmpro_getOption("levels_page_id");
						if(is_numeric($levels_page_id)){
							$plans_url = get_permalink($levels_page_id);
						}
					}
					
					$plans_url = apply_filters('beeteam368_replace_pmp_levels_page_url', $plans_url, $levels_page_id);
					
					$content = apply_filters('beeteam368_pmp_membership_restrict_content_html', '
						<div class="beeteam368-player beeteam368-player-protect dark-mode">
							<div class="beeteam368-player-wrapper temporaty-ratio">
								<div class="player-banner flex-vertical-middle" '.$img_background_cover.'>
									<div class="membership-info-wrapper">
										<h2 class="h1 h4-mobile membership-heading">'.esc_html__('Premium Content', 'beeteam368-extensions-pro').'</h2>
										<div class="membership-descriptions">'.str_replace( '$$plans$$', $all_plans_name, esc_html__('This content is for $$plans$$ members only.', 'beeteam368-extensions-pro') ).'</div>
										<a href="'.$plans_url.'" class="btnn-default btnn-primary"><i class="fas fa-users-cog icon"></i><span>'.esc_html__('Choose a plan', 'beeteam368-extensions-pro').'</span></a>
										'.$btn_trailer.'
									</div>
								</div>
							</div>	
						</div>					
					');
					
					return $content;
				}
			}
			
			return $content;
		}

		function element_sidebar_control($option){
			global $pmpro_page_id;

			if(!empty($pmpro_page_id) && is_page($pmpro_page_id)){
				return 'hidden';
			}
			
			return $option;
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
		{
			if(class_exists('ARM_global_settings') || class_exists('ARM_global_settings_Lite') || defined('PMPRO_VERSION')){
				if (is_array($values)) {
					$values[] = array('beeteam368-membership', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/membership/assets/membership.css', []);
				}
			}
			return $values;
		}
	}
}

global $beeteam368_membership;
$beeteam368_membership = new beeteam368_membership();
<?php
if (!class_exists('beeteam368_views_counter_front_end')) {
    class beeteam368_views_counter_front_end
    {
        public function __construct()
        {
            add_action('beeteam368_post_listing_views_counter', array($this, 'views_counter_post'), 10, 2);
            add_action('wp_head', array($this, 'views_counter_set'));
			
			add_action('beeteam368_before_video_player_in_single_playlist', array($this, 'views_counter_set'));
			add_action('beeteam368_before_audio_player_in_single_playlist', array($this, 'views_counter_set'));
			
			add_action('beeteam368_before_video_player_in_single_series', array($this, 'views_counter_set'));
			add_action('beeteam368_before_audio_player_in_single_series', array($this, 'views_counter_set'));
			
			add_filter('beeteam368_all_sort_query', array($this, 'all_sort_query'), 10, 2);
			add_filter('beeteam368_most_viewed_query', array($this, 'most_viewed_query'), 10, 1);
			add_filter('beeteam368_most_viewed_query_blog', array($this, 'most_viewed_query_blog'), 10, 1);
			
			add_filter('beeteam368_ordering_options', array($this, 'ordering_options'), 10, 1);
			
			add_filter('beeteam368_order_by_custom_query', array($this, 'order_by_custom_query'), 10, 1);
			
			add_filter('beeteam368_view_settings_tab', array($this, 'view_settings_tabs'), 10, 1);
			add_action('beeteam368_theme_settings_after_view_settings_options', array($this, 'view_settings_after'), 10, 1);
			
			add_action('beeteam368_trigger_real_times_media', array($this, 'view_calculator_wat'), 10, 2);
			
			add_action('wp_ajax_beeteam368_update_views_count_real_times', array($this, 'ajax_update_views_count_real_times'));
            add_action('wp_ajax_nopriv_beeteam368_update_views_count_real_times', array($this, 'ajax_update_views_count_real_times'));
        }
		
		function ajax_update_views_count_real_times(){
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			$params = isset($_POST['params'])?sanitize_text_field($_POST['params']):array();
			
			if (!beeteam368_ajax_verify_nonce($security, false)){
				wp_send_json(array('error'));
				return;
				die();			
			}
			
			if(!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){
				wp_send_json(array('error_post_id'));
				return;
				die();
			}
			
			global $beeteam368_views_counter_skip_lock;
			$beeteam368_views_counter_skip_lock = 'on';
				$this->views_counter_set($_POST['post_id']);
			$beeteam368_views_counter_skip_lock = NULL;
			
			wp_send_json(array('Done-'.$_POST['post_id']));
			return;
			die();
		}
		
		function view_calculator_wat($rnd_id, $params){
			$_views_counter_cal_method = beeteam368_get_option('_views_counter_cal_method', '_theme_settings', 'default');
			if(is_numeric($_views_counter_cal_method) && (int)$_views_counter_cal_method > 0 && (int)$_views_counter_cal_method <= 100){
			?>
            	var real_percent_<?php echo $rnd_id;?> = 'on';
                
                jQuery(document).on('beeteam368PlayerRealTimess<?php echo $rnd_id;?>', function(e, player_id, fN_params, video_current_time, video_duration, real_percent){
                    
                    if(real_percent >= <?php echo esc_html((int)$_views_counter_cal_method * 0.9);?> && real_percent_<?php echo $rnd_id;?> === 'on'){
                    	
                        real_percent_<?php echo $rnd_id;?> = 'off';
                                                
                        var data = {
                            'action': 'beeteam368_update_views_count_real_times',
                            'post_id': <?php echo $params['post_id'];?>,
                            'security':	vidmov_jav_js_object.security,
                        }
                                                
                        jQuery.ajax({
                            type: 'POST',
                            url: vidmov_jav_js_object.admin_ajax,
                            cache: false,
                            data: data,
                            dataType: 'json',
                            success: function(data, textStatus, jqXHR){                            
                            },
                            error: function( jqXHR, textStatus, errorThrown ){                          
                            }
                        });
                    
                    }
                    
                });
            <?php
			}
		}
		
		function view_settings_tabs($tabs){
			
			if(is_array($tabs)){
				$tabs[] = BEETEAM368_PREFIX . '_views_counter_cal_method';
			}
			
			return $tabs;
		}
		
		function view_settings_after($settings){
			$settings->add_field(array(
                'name' => esc_html__('Calculation Method', 'beeteam368-extensions'),
                'desc' => esc_html__('Choose a method of counting views for video and audio posts.', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_views_counter_cal_method',
                'type' => 'select',
                'default' => 'default',
                'options' => array(
                    'default' => esc_html__('Default', 'beeteam368-extensions'),
                    '5' => esc_html__('When the viewer has watched 5% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'10' => esc_html__('When the viewer has watched 10% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'15' => esc_html__('When the viewer has watched 15% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'20' => esc_html__('When the viewer has watched 20% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'25' => esc_html__('When the viewer has watched 25% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'30' => esc_html__('When the viewer has watched 30% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'35' => esc_html__('When the viewer has watched 35% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'40' => esc_html__('When the viewer has watched 40% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'45' => esc_html__('When the viewer has watched 45% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'50' => esc_html__('When the viewer has watched 50% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'55' => esc_html__('When the viewer has watched 55% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'60' => esc_html__('When the viewer has watched 60% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'65' => esc_html__('When the viewer has watched 65% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'70' => esc_html__('When the viewer has watched 70% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'75' => esc_html__('When the viewer has watched 75% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'80' => esc_html__('When the viewer has watched 80% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'85' => esc_html__('When the viewer has watched 85% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'90' => esc_html__('When the viewer has watched 90% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'95' => esc_html__('When the viewer has watched 95% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
					'100' => esc_html__('When the viewer has watched 100% of the video\'s length (or audio\'s length).', 'beeteam368-extensions'),
                ),
                'attributes' => array(
                    'data-conditional-id' => BEETEAM368_PREFIX . '_views_counter',
                    'data-conditional-value' => 'on',
                ),
            ));
		}
		
		function order_by_custom_query($options){
			if(is_array($options)){
				$options['most_viewed_week'] = esc_html__('Most Viewed (by this week)', 'beeteam368-extensions');
				$options['most_viewed_month'] = esc_html__('Most Viewed (by this month)', 'beeteam368-extensions');
				$options['most_viewed_year'] = esc_html__('Most Viewed (by this year)', 'beeteam368-extensions');
				$options['most_viewed'] = esc_html__('Most Viewed', 'beeteam368-extensions');				
			}
			return $options;
		}
		
		function most_viewed_query_blog($query){
			$query->set('meta_key', BEETEAM368_PREFIX . '_views_counter_totals');
			$query->set('orderby', 'meta_value_num');
			$query->set('order', 'DESC');
		}
		
		function most_viewed_query($args_query){
			if(is_array($args_query)){
				$args_query['meta_key'] = BEETEAM368_PREFIX . '_views_counter_totals';
				$args_query['orderby'] = 'meta_value_num';
				$args_query['order'] = 'DESC';
			}
			return $args_query;
		}
		
		function all_sort_query($sort, $position = ''){
			if(is_array($sort)){
				$sort['most_viewed'] = esc_html__('Most Viewed', 'beeteam368-extensions');
			}
			return $sort;
		}
		
		function ordering_options($options){
			if(is_array($options)){
				$options['most_viewed'] = esc_html__('Most Viewed', 'beeteam368-extensions');
			}
			return $options;
		}

        function views_counter_post($post_id, $hook_params){
            $views_number   = get_post_meta($post_id, BEETEAM368_PREFIX . '_views_counter_totals', true);
            $views_text     = esc_html__('views', 'beeteam368-extensions');

            if($views_number === 1){
                $views_text = esc_html__('view', 'beeteam368-extensions');
            }
            ?>
            <span class="post-footer-item post-lt-views post-lt-views-control">
                <span class="beeteam368-icon-item small-item"><i class="fas fa-eye"></i></span><span class="item-number"><?php echo apply_filters('beeteam368_number_format', $views_number);?></span>
                <span class="item-text"><?php echo esc_html($views_text);?></span>
            </span>
            <?php
        }

        function views_counter_set($post_id = 0){
			
			global $beeteam368_views_counter_skip_lock;	
			
			if(!is_single() && $beeteam368_views_counter_skip_lock !== 'on'){
				return;
			}
			
			if($post_id == NULL || $post_id == 0 || $post_id == ''){							
				$post_id = get_the_ID();
			}
			
			if($post_id == 0 || $post_id === FALSE){
				return;
			}
			
			$post_type = get_post_type($post_id);			
					
			$_views_counter_cal_method = beeteam368_get_option('_views_counter_cal_method', '_theme_settings', 'default');			
			if(is_numeric($_views_counter_cal_method) && (int)$_views_counter_cal_method > 0 && (int)$_views_counter_cal_method <= 100 && $beeteam368_views_counter_skip_lock !== 'on' && ($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video' || $post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio')){
				return;
			}

            $all_metas = array();

            $current_day        = current_time('Y_m_d');
            $current_week       = current_time('W');
            $current_month      = current_time('m');
            $current_year       = current_time('Y');

            $meta_current_day   = BEETEAM368_PREFIX . '_views_counter_day_'.$current_day;
            $meta_current_week  = BEETEAM368_PREFIX . '_views_counter_week_'.$current_week.'_'.$current_year;
            $meta_current_month = BEETEAM368_PREFIX . '_views_counter_month_'.$current_month.'_'.$current_year;
            $meta_current_year  = BEETEAM368_PREFIX . '_views_counter_year_'.$current_year;
            $meta_totals        = BEETEAM368_PREFIX . '_views_counter_totals';

            $all_metas[]        = $meta_current_day;
            $all_metas[]        = $meta_current_week;
            $all_metas[]        = $meta_current_month;
            $all_metas[]        = $meta_current_year;
            $all_metas[]        = $meta_totals;

            foreach($all_metas as $meta){
                $current_value  = get_post_meta($post_id, $meta, true);
                if(!is_numeric($current_value)){
                    $current_value = 0;
                }
                $new_value      = $current_value + 1;
                update_post_meta($post_id, $meta, $new_value);
				
				if(strpos($meta, '_views_counter_day_') !== false){
					$day_reactions_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_reaction_counter_day_'.$current_day, true);
					if(!is_numeric($day_reactions_value)){
						$day_reactions_value = 0;
					}
					
					update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_day_'.$current_day, ($new_value + $day_reactions_value));
					
				}elseif(strpos($meta, '_views_counter_week_') !== false){
					$week_reactions_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_reaction_counter_week_'.$current_week.'_'.$current_year, true);
					if(!is_numeric($week_reactions_value)){
						$week_reactions_value = 0;
					}
					
					update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_week_'.$current_week.'_'.$current_year, ($new_value + $week_reactions_value));
					
				}elseif(strpos($meta, '_views_counter_month_') !== false){
					$month_reactions_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_reaction_counter_month_'.$current_month.'_'.$current_year, true);
					if(!is_numeric($month_reactions_value)){
						$month_reactions_value = 0;
					}
					
					update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_month_'.$current_month.'_'.$current_year, ($new_value + $month_reactions_value));
					
				}elseif(strpos($meta, '_views_counter_year_') !== false){
					$year_reactions_value  = get_post_meta($post_id, BEETEAM368_PREFIX . '_reaction_counter_year_'.$current_year, true);
					if(!is_numeric($year_reactions_value)){
						$year_reactions_value = 0;
					}
					
					update_post_meta($post_id, BEETEAM368_PREFIX . '_trending_counter_year_'.$current_year, ($new_value + $year_reactions_value));
					
				}
            }
        }
    }
}

global $beeteam368_views_counter_front_end;
$beeteam368_views_counter_front_end = new beeteam368_views_counter_front_end();
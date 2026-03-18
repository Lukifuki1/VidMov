<?php
if (!class_exists('beeteam368_report_settings')) {
    class beeteam368_report_settings
    {
		
        public function __construct()
        {
            add_action('init', array($this, 'register_post_type'), 5);
			
			add_filter('beeteam368_video_main_toolbar_settings_tab', array($this, 'video_main_toolbar_settings_tabs'));
			add_action('beeteam368_video_main_toolbar_settings_options', array($this, 'video_main_toolbar_settings_options'));
			
			add_filter('beeteam368_audio_main_toolbar_settings_tab', array($this, 'audio_main_toolbar_settings_tabs'));
			add_action('beeteam368_audio_main_toolbar_settings_options', array($this, 'audio_main_toolbar_settings_options'));
			
			add_action('beeteam368_report_in_single', array($this, 'report_in_single'), 10, 3);
			
			add_filter('beeteam368_css_footer_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('wp_ajax_beeteam368_add_new_report', array($this, 'add_new_report'));
            add_action('wp_ajax_nopriv_beeteam368_add_new_report', array($this, 'add_new_report'));
			
			add_filter('manage_edit-'.BEETEAM368_POST_TYPE_PREFIX . '_report_columns', array($this, 'report_column_ID'));
			add_filter('manage_'.BEETEAM368_POST_TYPE_PREFIX . '_report_posts_custom_column', array($this, 'report_column_ID_value'), 10, 2);
			add_action('admin_menu', array($this, 'notification_bubble_in_report_menu'));
        }
		
		function report_column_ID( $columns ) {
			$date = $columns['date'];
			unset($columns['date']);
			$columns['reasons'] = esc_html__('Reasons', 'beeteam368-extensions');
			$columns['edit_view_data'] = esc_html__('Actions', 'beeteam368-extensions');
			$columns['user_report'] = esc_html__('User Report', 'beeteam368-extensions');
			$columns['date'] = $date;
			return $columns;
		}
		
		function report_column_ID_value( $colname, $cptid ) {
		
			$explode_id = explode('|', get_post_meta($cptid, 'post_report_id', true));
			
			if ( $colname == 'reasons' ){
				echo '<code class="reasons-texxt">'.get_post_meta($cptid, 'post_report_reasons', true).'</code>';
			}elseif($colname == 'edit_view_data'){
				if(is_array($explode_id) && count($explode_id) == 2){
					echo 	wp_kses(
								__('<a href="'.esc_url(get_edit_post_link($explode_id[0])).'" target="_blank">Edit Post</a> | <a href="'.esc_url(get_permalink($explode_id[0])).'" target="_blank">View Post</a>', 'beeteam368-extensions'
								),
								array(
									'a'=>array('href' => array(), 'target' => array()),
								)
							);
				}
			}elseif($colname == 'user_report'){
				if(is_array($explode_id) && count($explode_id) == 2){
					if($explode_id[1] == 0){						
						echo esc_html__('Unknown', 'beeteam368-extensions');									
					}else{
						$user_obj = get_user_by('id', $explode_id[1]);
						echo 	wp_kses(
									__('User Name: <strong>'.$user_obj->user_login.'</strong> | <a href="'.esc_url(get_edit_user_link($user_obj->ID)).'" target="_blank">View User</a>', 'beeteam368-extensions'
									),
									array(
										'a'=>array('href' => array(), 'target' => array()),
										'strong'=>array(),
									)
								);
					}
				}
			}
		}
		
		function notification_bubble_in_report_menu() {
			global $menu;
			
			global $wpdb;
			$pending_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type IN( '%s' ) AND post_status = 'publish'", BEETEAM368_POST_TYPE_PREFIX . '_report') );
			
			foreach ( $menu as $key => $value ){
				if ( $menu[$key][2] == 'edit.php?post_type='.BEETEAM368_POST_TYPE_PREFIX . '_report' ){
					$menu[$key][0] .= $pending_count?" <span class='update-plugins count-1' title='title'><span class='update-count'>$pending_count</span></span>":'';
					return;
				}
			}
		}
		
		function add_new_report(){
			$result = array();
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
            if ( !beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id'])) {
				$result['messages'] = '<span>'.esc_html__('You do not have permission to submit reports.', 'beeteam368-extensions').'</span>';
                wp_send_json($result);
                return;
                die();
            }
			
			$report_value = isset($_POST['report_value'])?trim($_POST['report_value']):'';
			$report_content = isset($_POST['report_content'])?trim($_POST['report_content']):'';
			
			if($report_value=='' && $report_content==''){
				$result['messages'] = '<span>'.esc_html__('Choose one from the suggested issues below.', 'beeteam368-extensions').'</span>';
                wp_send_json($result);
                return;
                die();
			}
			
			$report_messages = $report_value.($report_content!=''?': '.$report_content:'');
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
				$user_id = (int)$current_user->ID;		
			}else{
				$user_id = 0;
			}
			$meta_id = $_POST['post_id'].'|'.$user_id;
			
			$exists = new WP_Query(array(
				'post_type'   		=> BEETEAM368_POST_TYPE_PREFIX . '_report',
				'post_status' 		=> 'any',
				'posts_per_page'	=> 1,			
				'meta_query'  		=> array(
					array(
						'key'     => 'post_report_id',
						'value'   => $meta_id,
						'compare' => '=',
					),
				),
			));
	
			if($exists->have_posts()){			
				wp_reset_postdata();			
				$result['messages'] = '<span>'.esc_html__('This content is under review and processing. Please wait patiently!', 'beeteam368-extensions').'</span>';
				wp_send_json($result);			
				return;
				die();
			}			
			wp_reset_postdata();
			
			$postData = array();											
			$postData['post_title'] 	= esc_html__('Report', 'beeteam368-extensions').': '.get_the_title($_POST['post_id']);	
			$postData['post_status'] 	= 'publish';
			$postData['post_type'] 		= BEETEAM368_POST_TYPE_PREFIX . '_report';
			
			$newPostID = wp_insert_post($postData);
			
			if(!is_wp_error($newPostID) && $newPostID){			
				update_post_meta($newPostID, 'post_report_id', $meta_id);
				update_post_meta($newPostID, 'post_report_reasons', trim($report_messages));	
			}else{
				$result['messages'] = '<span>'.esc_html__('An error has occurred. Please reload the page and try again!', 'beeteam368-extensions').'</span>';
				wp_send_json($result);			
				return;
				die();
			}
			
			$result['messages'] = '<span class="success">'.esc_html__('Thanks, you have successfully submitted the report! We will review and resolve.', 'beeteam368-extensions').'</span>';
            wp_send_json($result);
			
			return;
            die();
		}
		
		function report_in_single($post_id, $pos_style, $wrap){
			if($wrap){
				echo '<div class="sub-block-wrapper">';
			}
			?>
				<div class="beeteam368-icon-item is-square tooltip-style beeteam368-global-open-popup-control" data-post-id="<?php echo esc_attr($post_id)?>" data-popup-id="report_popup">
                    <i class="icon far fa-flag"></i>
                    <span class="tooltip-text"><?php echo esc_html__('Report', 'beeteam368-extensions')?></span>
                </div>
			<?php
			if($wrap){
				echo '</div>';
			}
			?>
            <div class="beeteam368-global-popup beeteam368-report-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="report_popup">
                <div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                	<h2 class="h3-mobile"><?php echo esc_html__('Report', 'beeteam368-extensions')?></h2>
                    
                    <hr>
                    <div class="report-alerts report-alerts-control"></div>
                	<form name="add-report" class="form-report-control" method="post" enctype="multipart/form-data">
                    	<input type="hidden" name="post_id" value="<?php echo esc_attr($post_id)?>">
                    	<div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-1" name="report_value" value="<?php echo esc_attr__('Inappropriate content', 'beeteam368-extensions')?>" required><label for="report-1" class="h5"><?php echo esc_html__('Inappropriate content', 'beeteam368-extensions')?></label>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-2" name="report_value" value="<?php echo esc_attr__('Media is broken, can\'t watch...', 'beeteam368-extensions')?>"><label for="report-2" class="h5"><?php echo esc_html__('Media is broken, can\'t watch...', 'beeteam368-extensions')?></label>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-3" name="report_value" value="<?php echo esc_attr__('Captions issue', 'beeteam368-extensions')?>"><label for="report-3" class="h5"><?php echo esc_html__('Captions issue', 'beeteam368-extensions')?></label>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-4" name="report_value" value="<?php echo esc_attr__('Spam or misleading', 'beeteam368-extensions')?>"><label for="report-4" class="h5"><?php echo esc_html__('Spam or misleading', 'beeteam368-extensions')?></label>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-5" name="report_value" value="<?php echo esc_attr__('Infringes my rights', 'beeteam368-extensions')?>"><label for="report-5" class="h5"><?php echo esc_html__('Infringes my rights', 'beeteam368-extensions')?></label>
                        </div>
                        
                         <div class="report-item flex-vertical-middle">
                        	<input type="radio" id="report-6" name="report_value" value="<?php echo esc_attr__('Others', 'beeteam368-extensions')?>"><label for="report-6" class="h5"><?php echo esc_html__('Others', 'beeteam368-extensions')?></label>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                        	<textarea name="report_content" placeholder="<?php echo esc_attr__('More reports...', 'beeteam368-extensions')?>"></textarea>
                        </div>
                        
                        <div class="report-item flex-vertical-middle">
                            <button name="submit" type="button" class="loadmore-btn report-submit-control">
                                <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Report', 'beeteam368-extensions');?></span>
                                <span class="loadmore-loading">
                                    <span class="loadmore-indicator">
                                        <svg><polyline class="lm-back" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline> <polyline class="lm-front" points="1 6 4 6 6 11 10 1 12 6 15 6"></polyline></svg>
                                    </span>
                                </span>								
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php
		}
		
		function video_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_report';			
			return $fields;
		}	
		
		function video_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Report" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_report',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}
		
		function audio_main_toolbar_settings_tabs($fields){
			$fields[] = BEETEAM368_PREFIX . '_mtb_report';			
			return $fields;
		}	
		
		function audio_main_toolbar_settings_options($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Report" Button', 'beeteam368-extensions'),
                'id' => BEETEAM368_PREFIX . '_mtb_report',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions'),
                    'off' => esc_html__('NO', 'beeteam368-extensions'),
                ),
            ));
		}

        function register_post_type()
        {
			$permalink 	= esc_html_x('report', 'slug', 'beeteam368-extensions');
			register_post_type(BEETEAM368_POST_TYPE_PREFIX . '_report',
				apply_filters('beeteam368_register_post_type_report',
					array(
						'labels' => array(
								'name'                  => esc_html__('Reports', 'beeteam368-extensions'),
								'singular_name'         => esc_html__('Report', 'beeteam368-extensions'),
								'menu_name'             => esc_html__('Reports', 'beeteam368-extensions'),
								'add_new'               => esc_html__('Add Report', 'beeteam368-extensions'),
								'add_new_item'          => esc_html__('Add New Report', 'beeteam368-extensions'),
								'edit'                  => esc_html__('Edit', 'beeteam368-extensions'),
								'edit_item'             => esc_html__('Edit Report', 'beeteam368-extensions'),
								'new_item'              => esc_html__('New Report', 'beeteam368-extensions'),
								'view'                  => esc_html__('View Report', 'beeteam368-extensions'),
								'view_item'             => esc_html__('View Report', 'beeteam368-extensions'),
								'search_items'          => esc_html__('Search Reports', 'beeteam368-extensions'),
								'not_found'             => esc_html__('No Reports found', 'beeteam368-extensions'),
								'not_found_in_trash'    => esc_html__('No Reports found in trash', 'beeteam368-extensions'),
								'parent'                => esc_html__('Parent Report', 'beeteam368-extensions'),
								'featured_image'        => esc_html__('Report Image', 'beeteam368-extensions'),
								'set_featured_image'    => esc_html__('Set report image', 'beeteam368-extensions'),
								'remove_featured_image' => esc_html__('Remove report image', 'beeteam368-extensions'),
								'use_featured_image'    => esc_html__('Use as report image', 'beeteam368-extensions'),
								'insert_into_item'      => esc_html__('Insert into report', 'beeteam368-extensions'),
								'uploaded_to_this_item' => esc_html__('Uploaded to this report', 'beeteam368-extensions'),
								'filter_items_list'     => esc_html__('Filter reports', 'beeteam368-extensions'),
								'items_list_navigation' => esc_html__('Reports navigation', 'beeteam368-extensions'),
								'items_list'            => esc_html__('Reports list', 'beeteam368-extensions'),
							),
						'description'         => esc_html__('This is where you can add new reports to your site.', 'beeteam368-extensions'),
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => BEETEAM368_PREFIX . '_report',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'hierarchical'        => false,
						'rewrite'             => $permalink?array('slug' => untrailingslashit($permalink), 'with_front' => false, 'feeds' => true):false,
						'query_var'           => true,
						'supports'            => array('title'),
						'has_archive'         => true,
						'show_in_nav_menus'   => true,
						'menu_icon'			  => 'dashicons-info',
						'menu_position'		  => 6,
						'capabilities'		  => array(
							'create_posts' => 'do_not_allow',
						),
					)
				)
			);
        }
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-report', BEETEAM368_EXTENSIONS_URL . 'inc/report/assets/report.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-report', BEETEAM368_EXTENSIONS_URL . 'inc/report/assets/report.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){                
				$define_js_object['report_error_choose_one_field'] = esc_html__( 'Choose one from the suggested issues below.', 'beeteam368-extensions');
            }

            return $define_js_object;
        }
    }
}

global $beeteam368_report_settings;
$beeteam368_report_settings = new beeteam368_report_settings();
<?php
if (!class_exists('beeteam368_virtual_gifts_front_end')) {
    class beeteam368_virtual_gifts_front_end
    {
		public $module_action = 'transfer_history';
		
        public function __construct()
        {
			global $beetam368_show_post_meta_action;
			$beetam368_show_post_meta_action = 'on';
			
			global $beetam368_show_author_description;
			$beetam368_show_author_description = 'off';
			
			add_action('init', function(){
				remove_action( 'beeteam368_author_sub_meta_for_post', 'beeteam368_author_sub_meta_for_post', 10, 2 );
			});
			
            add_action('beeteam368_virtual_gifts_button', array($this, 'virtual_gifts_front_end'), 10, 2);
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);
			
			add_action('mycred_transfer_completed', array($this, 'log_in_single'), 10, 4);
			
			add_filter('mycred_new_transfer_request', array($this, 'add_params_transfer_request'), 10, 2);
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_action('beeteam368_channel_privacy_'.$this->module_action, array($this, 'profile_privacy'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));
			
			add_action('beeteam368_side_menu_'.$this->module_action, array($this, 'in_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_'.$this->module_action, array($this, 'show_in_tab'), 10, 2);
			
			add_action('beeteam368_channel_fe_tab_content_'.$this->module_action, array($this, 'channel_tab_content'), 10, 2);
			
			add_action('beeteam368_transfer_history_item_dropdown_login', array($this, 'transfer_history_menu'));
			
			add_filter('mycred_log_front_nav_url', array($this, 'fix_myCred_pagination_logs'), 10, 2);
        }
		
		/*profile page privacy*/
		function profile_privacy($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_'.$this->module_action, true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="<?php echo esc_attr($this->module_action);?>"><?php echo esc_html__('Transfer History Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="<?php echo esc_attr($this->module_action);?>" id="<?php echo esc_attr($this->module_action);?>" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}/*profile page privacy*/
		
		/*order settings*/	
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_'.$this->module_action.'_item', '_channel_settings', 'on') === 'on'){
				$tabs[$this->module_action] = esc_html__('Transfer History', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_'.$this->module_action.'_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs[$this->module_action] = esc_html__('Transfer History', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}/*order settings*/
		
		/*side menu settings*/
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_'.$this->module_action.'_item';
			return $tabs;
		}		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Transfer History" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Transfer History" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_'.$this->module_action.'_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
		}/*side menu settings*/
		
		/*tab settings*/
		function add_tab_tab_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_'.$this->module_action.'_tab_item';
			return $tabs;
		}
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Transfer History" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Transfer History" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_'.$this->module_action.'_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
		}/*tab settings*/
		
		/*front show in side menu*/
		function in_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_'.$this->module_action.'_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == $this->module_action){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_'.$this->module_action.'_tab_name', $this->module_action))));?>" class="ctrl-show-hidden-elm notification-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-funnel-dollar"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Transfer History', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', $this->module_action.'_page'));?>" data-redirect="<?php echo esc_attr($this->module_action.'_page')?>" data-note="<?php echo esc_attr__('Sign in to see your transfer history.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm subscription-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-funnel-dollar"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Transfer History', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php	
			}
        }/*front show in side menu*/
		
		/*front show in tab menu*/
		function show_in_tab($author_id, $tab){
			if(beeteam368_get_option('_channel_'.$this->module_action.'_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_'.$this->module_action.'_tab_name', $this->module_action))));?>" class="swiper-slide tab-item<?php if($tab == $this->module_action){echo ' active-item';}?>" title="<?php echo esc_attr__('Transfer History', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-funnel-dollar"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Transfer History', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', $this->module_action, $author_id);?>
                </a>
        <?php
			}
		}/*front show in tab menu*/
		
		/*front show in tab content*/
		function channel_tab_content($author_id, $tab){
			if($tab!=$this->module_action){
				return;
			}
			
			$user_id = 0;
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
			}
			
			$privacy = sanitize_text_field(get_user_meta($author_id, BEETEAM368_PREFIX . '_privacy_'.$tab, true));			
			if($privacy === 'private' && $author_id != $user_id){
				do_action('beeteam368_no_data_in_channel_content', $author_id, 'private-infor');
				return;
			}
			?>
            
            <div class="top-section-title has-icon">
                <span class="beeteam368-icon-item"><i class="fas fa-dollar-sign"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Your Balance', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('Your Accumulated points', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            <div class="h1 h1-single myCred-total-balance">
                <?php
                echo do_shortcode(apply_filters('beeteam368_mycred_my_balance', '[mycred_my_balance user_id="'.$author_id.'"]', $author_id));				
                ?>
            </div>            
            <hr>

            <div class="top-section-title has-icon">
                <span class="beeteam368-icon-item"><i class="far fa-money-bill-alt"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('Withdraw', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('Exchange points to cash', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            <?php
            if(is_user_logged_in() && $user_id == $author_id ){
            	echo do_shortcode(apply_filters('beeteam368_mycred_cashcred', '[mycred_cashcred]', $author_id));
				?>
                <hr class="only-show-when-ntc">
            	<?php
            }else{			
				if(is_user_logged_in()){
				?>
                	<h2 class="h6 mycred-private-notice"><?php echo esc_html__('This is a private area, you can only use this feature on your channel.', 'beeteam368-extensions-pro');?></h2>
                    <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_'.$this->module_action.'_tab_name', $this->module_action))));?>" class="btnn-default btnn-primary icon-style">                        
                    	<i class="icon fas fa-chalkboard-teacher"></i><span><?php echo esc_html__('Go to your channel', 'beeteam368-extensions-pro') ?></span>                        
                    </a>
                <?php	
				}elseif($user_id != $author_id){
				?>
                	<h2 class="h6 mycred-private-notice"><?php echo esc_html__('This is a private area. If this is your channel, please login to use.', 'beeteam368-extensions-pro');?></h2>
                	<button type="button" class="icon-style reg-log-popup-control" data-note="<?php echo esc_attr__('Please login to view or use this feature.', 'beeteam368-extensions-pro')?>" data-redirect="<?php echo esc_attr($this->module_action.'_page')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php 
				}
				?>
            	<hr>
            <?php
			}
            ?>
            <div style="clear:both"></div>

            <div class="top-section-title has-icon">
                <span class="beeteam368-icon-item"><i class="fas fa-funnel-dollar"></i></span>
                <span class="sub-title font-main"><?php echo esc_html__('History of giving and receiving your points', 'beeteam368-extensions-pro');?></span>
                <h2 class="h2 h3-mobile main-title-heading">                            
                    <span class="main-title"><?php echo esc_html__('Transfer History', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                </h2>
            </div>
            <?php
			echo do_shortcode(apply_filters('beeteam368_mycred_history', '[mycred_history user_id="'.$author_id.'" number="15"]', $author_id));	
		}/*front show in tab content*/
		
		function add_params_transfer_request($params, $posted){
			
			if(is_array($params)){
				$params['in_single_author_id'] = isset( $posted['in_single_author_id'] ) ? $posted['in_single_author_id'] : '';
				$params['in_single_post_id'] = isset( $posted['in_single_post_id'] ) ? $posted['in_single_post_id'] : '';
			}
			
			return $params;
		}
		
		function log_in_single($transfer_id, $request, $settings, $original){			
			if(isset($original->request) && isset($original->request['in_single_author_id']) && isset($original->request['in_single_post_id']) && $original->request['in_single_post_id'] > 0){
				
				$old_total_points = get_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_total_donation_points', true);	
				if(!is_numeric($old_total_points)){
					$old_total_points = 0;
				}		
				update_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_total_donation_points', $old_total_points + $request['charge']);
				
				$old_point_type = get_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_total_donation_points_'.$request['point_type'], true);	
				if(!is_numeric($old_point_type)){
					$old_point_type = 0;
				}		
				update_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_total_donation_points_'.$request['point_type'], $old_point_type + $request['charge']);
				
				$old_transfer_ids = get_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_donation_ids', true);	
				if(!is_array($old_transfer_ids)){
					$old_transfer_ids = array();
				}
				
				$old_transfer_ids[] = $transfer_id;
				update_post_meta($original->request['in_single_post_id'], BEETEAM368_PREFIX . '_donation_ids', $old_transfer_ids);
				
			}
		}

        function virtual_gifts_front_end($author_id, $post_id){
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$notice = 0;
				if($post_id <= 0){
					$post_author_id = $author_id;
					$notice = 1;
				}else{	
					$post_author_id = get_post_field( 'post_author', $post_id );
				}

                if($post_author_id == $user_id){
                    return;
                }
            }
			
			if(is_user_logged_in()){
				$recipient = get_user_by( 'ID', $author_id );
				if(!$recipient || !isset($recipient->display_name)){
					return;
				}
        	?>
                <div class="virtual-gifts">
                    <button class="icon-style reverse tooltip-style beeteam368-global-open-popup-control" data-author-id="<?php echo esc_attr($author_id)?>" data-post-id="<?php echo esc_attr($post_id)?>" data-popup-id="virtual_gifs_popup" data-text-replace="<?php echo esc_attr($recipient->display_name);?>" data-target-element=".select-recipient-wrapper .form-control-static" data-giver="<?php echo esc_attr($current_user->display_name)?>" data-action="donation">
                        <i class="far fa-star"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Virtual Gifts', 'beeteam368-extensions-pro')?></span>
                    </button>
                    
                    <div class="beeteam368-global-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="virtual_gifs_popup">
                    	<div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                        	<?php 
							$give_points_text = esc_html__('Give Points', 'beeteam368-extensions-pro');
							echo do_shortcode('[mycred_transfer ref="donation" button="'.$give_points_text.'" pay_to="'.$author_id.'" show_balance="1"]')
							?>
                    	</div>
                    </div>
                </div>
        	<?php
			}else{
			?>
            	<div class="virtual-gifts">
                	<a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'virtual_gifts_button'));?>" data-note="<?php echo esc_attr__('Please login to give this member a gift. It\'s the reward that keeps this creator going to keep creating compelling new content.', 'beeteam368-extensions-pro')?>" class="icon-style reverse tooltip-style btnn-default reg-log-popup-control" data-author-id="<?php echo esc_attr($author_id)?>" data-post-id="<?php echo esc_attr($post_id)?>">
                    	<i class="far fa-star"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Virtual Gifts', 'beeteam368-extensions-pro')?></span>
                    </a>
                </div>
            <?php
			}
        }
		
		function transfer_history_menu($user_id){
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || !is_numeric($channel_page) || $channel_page < 1){
				return;
			}
		?>
        	<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_'.$this->module_action.'_tab_name', $this->module_action))));?>" class="transfer-history-menu flex-row-control flex-vertical-middle icon-drop-down-url">                            
                <span class="beeteam368-icon-item">
                    <i class="fas fa-funnel-dollar"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Transfer History', 'beeteam368-extensions-pro')?></span>
                
            </a>
        <?php	
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-myCred-transfer', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/virtual-gifts/assets/myCred-transfer.css', []);
            }
            return $values;
        }
		
		function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-myCred-transfer', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/virtual-gifts/assets/myCred-transfer.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object)){
				$virtual_gifts_default_bonus_points = trim(beeteam368_get_option('_virtual_gifts_default_bonus_points', '_theme_settings', ''));
				
				if($virtual_gifts_default_bonus_points!=''){
                	$define_js_object['virtual_gifts_default_bonus_points'] = $virtual_gifts_default_bonus_points;
					$define_js_object['virtual_gifts_default_bonus_text'] = esc_html__('%giver% gives %points% points', 'beeteam368-extensions-pro');
				}
            }

            return $define_js_object;
        }
		
		function fix_myCred_pagination_logs($current_url, $this_class){
			
			$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
			if(is_page() && beeteam368_get_option('_channel', '_theme_settings', 'on') === 'on' && is_numeric($channel_page) && $channel_page > 1){
				$post_page_id = get_the_ID();
				
				$tab_order = beeteam368_channel_front_end::channel_tab_order();				
				$tab = trim(get_query_var('channel-tab', ''));
				if($tab == ''){
					$tab = $tab_order[0];
				}
						
				if($post_page_id == $channel_page && $tab == $this->module_action){
					$removable_query_args = wp_removable_query_args();
					
					$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
					$current_url = remove_query_arg( $removable_query_args, $current_url );					
					$current_url = apply_filters( 'beeteam368_mycred_log_front_nav_url', $current_url, $this_class );
				}
			}
			
			return $current_url;
		}
    }
}

global $beeteam368_virtual_gifts_front_end;
$beeteam368_virtual_gifts_front_end = new beeteam368_virtual_gifts_front_end();
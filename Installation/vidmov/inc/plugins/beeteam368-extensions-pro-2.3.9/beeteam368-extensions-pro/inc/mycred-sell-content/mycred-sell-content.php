<?php
if (!class_exists('beeteam368_myCred_sell_content_front_end')) {
    class beeteam368_myCred_sell_content_front_end
    {
		public $current_user_id = 0;
		
        public function __construct()
        {
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_filter('beeteam368_media_protect_html', array($this, 'protect'), 20, 4);
			
			add_filter('the_content', array($this, 'handle_watch_trailer_mycred_protect_in_content'), 50, 1);
			
			add_action('beeteam368_show_sales_count_on_featured_img', array($this, 'sales_count_on_listing'), 10, 2);
			add_action('beeteam368_show_sales_count_on_single_media', array($this, 'sales_count_on_single'), 10, 2);
			
			add_action('beeteam368_post_submit_settings_options', array($this, 'enable_sell_content_options'), 10, 1);
			add_action('beeteam368_live_streaming_settings_options', array($this, 'enable_sell_content_options'), 10, 1);
			
			add_action('beeteam368_sell_content_in_submit_form', array($this, 'add_sell_content_to_submit_form'), 10, 2);
			add_action('beeteam368_sell_content_in_live_form', array($this, 'add_sell_content_to_live_form'), 10, 1);
			
			add_action('beeteam368_after_submit_post_success', array($this, 'handle_sell_content_in_submit_form'), 10, 2);
			
			add_filter('beeteam368_total_check_submit_post', array($this, 'check_sell_content_in_submit_form'), 10, 2);
			
			add_filter('beeteam368_sell_content_in_edit_form', array($this, 'add_sell_content_to_edit_form'), 10, 2);
		}
		
		function add_sell_content_to_edit_form($post_id, $post_type){
			if(defined('myCRED_SELL') && beeteam368_get_option('_submit_post_sell_content', '_user_submit_post_settings', 'on') === 'on'){
				
				$settings = mycred_sell_content_settings();
				
				$setup = mycred_get_option( 'mycred_sell_this_' . MYCRED_DEFAULT_TYPE_KEY );					
				if ( $setup['status'] === 'disabled' ){
					return;
				}
				
				$mycred = mycred( MYCRED_DEFAULT_TYPE_KEY );
				
				$suffix = '';
				$sale_setup = (array) mycred_get_post_meta( $post_id, 'myCRED_sell_content' . $suffix );
				$sale_setup = empty($sale_setup) ? $sale_setup : $sale_setup[0];
				
				$sale_setup = shortcode_atts( array(
					'status' => 'disabled',
					'price'  => 0,
					'expire' => 0 
				), $sale_setup );
				
				$price = '';
				$expiration = '';
				if ( $sale_setup['status'] === 'enabled' ){
					$price = $sale_setup['price'];
					$expiration = $sale_setup['expire'];
				}
				
				if ($post_type === BEETEAM368_POST_TYPE_PREFIX . '_video' && !empty( $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ] ) && $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ]['by'] === 'manual' ){
				
					
				?>
                	<div>
                        <label class="h1"><?php echo esc_html__('Video - Pay Per View', 'beeteam368-extensions-pro')?></label>				
                        <div class="data-item">
                            <label for="post_video_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_video_sell_price" id="post_video_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($price);?>">
                        </div>
                        <div class="data-item">
                            <label for="post_video_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_video_sell_expiration" id="post_video_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($expiration);?>">
                        </div>
                        <hr>
                    </div>
				<?php
				}
				
				if ($post_type === BEETEAM368_POST_TYPE_PREFIX . '_audio' && !empty( $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_audio' ] ) && $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_audio' ]['by'] === 'manual' ){
				?>
                	<div>
                        <label class="h1"><?php echo esc_html__('Audio - Pay Per Listen', 'beeteam368-extensions-pro')?></label>
                        <div class="data-item">
                            <label for="post_audio_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_audio_sell_price" id="post_audio_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($price);?>">
                        </div>
                        <div class="data-item">
                            <label for="post_audio_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_audio_sell_expiration" id="post_audio_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($expiration);?>">
                        </div>
                        <hr>
                    </div>
				<?php
				}
				
				if ($post_type === 'post' && !empty( $settings['filters'][ 'post' ] ) && $settings['filters'][ 'post' ]['by'] === 'manual' ){
				?>
                	<div>
                        <label class="h1"><?php echo esc_html__('Sell Content', 'beeteam368-extensions-pro')?></label>
                        <div class="data-item">
                            <label for="post_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_sell_price" id="post_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($price);?>">
                        </div>
                        <div class="data-item">
                            <label for="post_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_sell_expiration" id="post_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>" value="<?php echo esc_attr($expiration);?>">
                        </div>
                        <hr>
                	</div>
				<?php
				}
			}
		}
		
		function check_sell_content_in_submit_form($total_errors, $sm_post_type){
			if(defined('myCRED_SELL') && beeteam368_get_option('_submit_post_sell_content', '_user_submit_post_settings', 'on') === 'on'){
				
				$price = 0;
				$expiration = 0;
				
				switch($sm_post_type){
					case BEETEAM368_POST_TYPE_PREFIX . '_video';
						$price = (isset($_POST['post_video_sell_price']) && is_numeric(trim($_POST['post_video_sell_price'])))?trim($_POST['post_video_sell_price'])+0:0;
						$expiration = (isset($_POST['post_video_sell_expiration']) && is_numeric(trim($_POST['post_video_sell_expiration'])))?trim($_POST['post_video_sell_expiration'])+0:0;
						break;
						
					case BEETEAM368_POST_TYPE_PREFIX . '_audio';
						$price = (isset($_POST['post_audio_sell_price']) && is_numeric(trim($_POST['post_audio_sell_price'])))?trim($_POST['post_audio_sell_price'])+0:0;
						$expiration = (isset($_POST['post_audio_sell_expiration']) && is_numeric(trim($_POST['post_audio_sell_expiration'])))?trim($_POST['post_audio_sell_expiration'])+0:0;
						break;
						
					case 'post':
						$price = (isset($_POST['post_sell_price']) && is_numeric(trim($_POST['post_sell_price'])))?trim($_POST['post_sell_price'])+0:0;
						$expiration = (isset($_POST['post_sell_expiration']) && is_numeric(trim($_POST['post_sell_expiration'])))?trim($_POST['post_sell_expiration'])+0:0;
						break;
				}
				
				if(!is_int($price)){
					$total_errors.='<span>'.esc_html__('Error: Price must be an integer.', 'beeteam368-extensions-pro').'</span>';
				}
				
				if($price < 0){
					$total_errors.='<span>'.esc_html__('Error: Price must be greater than or equal to 0.', 'beeteam368-extensions-pro').'</span>';
				}
				
				if(!is_int($expiration)){
					$total_errors.='<span>'.esc_html__('Error: Expiration value must be an integer.', 'beeteam368-extensions-pro').'</span>';
				}
				
				if($expiration < 0){
					$total_errors.='<span>'.esc_html__('Error: Expiration value must be greater than or equal to 0.', 'beeteam368-extensions-pro').'</span>';
				}
			}
			
			return $total_errors;
		}
		
		function handle_sell_content_in_submit_form($post_id, $sm_post_type){
			if(defined('myCRED_SELL') && beeteam368_get_option('_submit_post_sell_content', '_user_submit_post_settings', 'on') === 'on'){
				
				$price = 0;
				$expiration = 0;
				
				switch($sm_post_type){
					case BEETEAM368_POST_TYPE_PREFIX . '_video';
						$price = (isset($_POST['post_video_sell_price']) && is_numeric(trim($_POST['post_video_sell_price'])) && (int)trim($_POST['post_video_sell_price'])>=1)?(int)trim($_POST['post_video_sell_price']):0;
						$expiration = (isset($_POST['post_video_sell_expiration']) && is_numeric(trim($_POST['post_video_sell_expiration'])) && (int)trim($_POST['post_video_sell_expiration'])>=1)?(int)trim($_POST['post_video_sell_expiration']):0;
						break;
						
					case BEETEAM368_POST_TYPE_PREFIX . '_audio';
						$price = (isset($_POST['post_audio_sell_price']) && is_numeric(trim($_POST['post_audio_sell_price'])) && (int)trim($_POST['post_audio_sell_price'])>=1)?(int)trim($_POST['post_audio_sell_price']):0;
						$expiration = (isset($_POST['post_audio_sell_expiration']) && is_numeric(trim($_POST['post_audio_sell_expiration'])) && (int)trim($_POST['post_audio_sell_expiration'])>=1)?(int)trim($_POST['post_audio_sell_expiration']):0;
						break;
						
					case 'post':
						$price = (isset($_POST['post_sell_price']) && is_numeric(trim($_POST['post_sell_price'])) && (int)trim($_POST['post_sell_price'])>=1)?(int)trim($_POST['post_sell_price']):0;
						$expiration = (isset($_POST['post_sell_expiration']) && is_numeric(trim($_POST['post_sell_expiration'])) && (int)trim($_POST['post_sell_expiration'])>=1)?(int)trim($_POST['post_sell_expiration']):0;
						break;
				}
				
				$mycred = mycred( MYCRED_DEFAULT_TYPE_KEY );					
				$new_setup = array( 'status' => 'disabled', 'price' => 0, 'expire' => 0 );
				$suffix = '';
				
				if($price >= 1){
					
					$new_setup['status'] = 'enabled';
					
					$new_setup['price'] = $mycred->number( sanitize_text_field( $price ) );
					
					if($expiration >=1 ){
						$new_setup['expire'] = absint( sanitize_text_field( $expiration ) );
					}
					
					mycred_update_post_meta( $post_id, 'myCRED_sell_content' . $suffix, $new_setup );
					
				}else{
					
					mycred_update_post_meta( $post_id, 'myCRED_sell_content' . $suffix, $new_setup );
					
				}
			}
		}
		
		function add_sell_content_to_submit_form($arr_tab_submit, $default_switch_post_type){
			if(defined('myCRED_SELL') && beeteam368_get_option('_submit_post_sell_content', '_user_submit_post_settings', 'on') === 'on'){
				
				$_sell_content_roles = beeteam368_get_option('_sell_content_roles', '_user_submit_post_settings', 'off');
				
				if($_sell_content_roles === 'on'){
					$user_submit_id = 0;
					if(is_user_logged_in()){
						$current_user = wp_get_current_user();				
						$user_submit_id = $current_user->ID;
					}
					
					if($user_submit_id > 0){
						$user_meta = get_userdata($user_submit_id);
						$user_roles = $user_meta->roles;
						
						global $wp_roles;
						$all_roles = implode(', ', array_keys($wp_roles->role_names));
						
						$sell_content_roles = explode(',', trim(beeteam368_get_option('_sell_content_role_setting', '_user_submit_post_settings', $all_roles)));
						
						$sell_content_roles_op = array();
						
						foreach($sell_content_roles as $role){
							$role = trim($role);
							if($role!=''){
								$sell_content_roles_op[] = $role;
							}
						}
						
						$permisions_sell_content = array();
						
						$permisions_sell_content = array_intersect($user_roles, $sell_content_roles_op);
						
					}else{
						$permisions_sell_content = array();
					}
					
				}else{
					$permisions_sell_content = array('Anyone');
				}
				
				if(count($permisions_sell_content) <= 0){
					return;
				}
				
				$settings = mycred_sell_content_settings();
				
				if (isset($arr_tab_submit['_submit_videos']) && !empty( $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ] ) && $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ]['by'] === 'manual' ){
				?>
                	<div class="section-video-sell-control <?php if($default_switch_post_type !== 'video'){echo 'is-temp-hidden';}?>">
                        <label class="h1"><?php echo esc_html__('Video - Pay Per View', 'beeteam368-extensions-pro')?></label>				
                        <div class="data-item">
                            <label for="post_video_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_video_sell_price" id="post_video_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>">
                        </div>
                        <div class="data-item">
                            <label for="post_video_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_video_sell_expiration" id="post_video_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>">
                        </div>
                        <hr>
                    </div>
				<?php
				}
				
				if (isset($arr_tab_submit['_submit_audios']) && !empty( $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_audio' ] ) && $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_audio' ]['by'] === 'manual' ){
				?>
                	<div class="section-audio-sell-control <?php if($default_switch_post_type !== 'audio'){echo 'is-temp-hidden';}?>">
                        <label class="h1"><?php echo esc_html__('Audio - Pay Per Listen', 'beeteam368-extensions-pro')?></label>
                        <div class="data-item">
                            <label for="post_audio_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_audio_sell_price" id="post_audio_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>">
                        </div>
                        <div class="data-item">
                            <label for="post_audio_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_audio_sell_expiration" id="post_audio_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>">
                        </div>
                        <hr>
                    </div>
				<?php
				}
				
				if (isset($arr_tab_submit['_submit_posts']) && !empty( $settings['filters'][ 'post' ] ) && $settings['filters'][ 'post' ]['by'] === 'manual' ){
				?>
                	<div class="section-post-sell-control <?php if($default_switch_post_type !== 'post'){echo 'is-temp-hidden';}?>">
                        <label class="h1"><?php echo esc_html__('Sell Content', 'beeteam368-extensions-pro')?></label>
                        <div class="data-item">
                            <label for="post_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                            <input type="number" min="1" step="1" name="post_sell_price" id="post_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>">
                        </div>
                        <div class="data-item">
                            <label for="post_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                            <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                            <input type="number" min="1" step="1" name="post_sell_expiration" id="post_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>">
                        </div>
                        <hr>
                	</div>
				<?php
				}
			}
		}
		
		function add_sell_content_to_live_form(){
			if(defined('myCRED_SELL') && beeteam368_get_option('_submit_post_sell_content', '_live_streaming_settings', 'on') === 'on'){
				
				$_sell_content_roles = beeteam368_get_option('_sell_content_roles', '_live_streaming_settings', 'off');
				
				if($_sell_content_roles === 'on'){
					$user_submit_id = 0;
					if(is_user_logged_in()){
						$current_user = wp_get_current_user();				
						$user_submit_id = $current_user->ID;
					}
					
					if($user_submit_id > 0){
						$user_meta = get_userdata($user_submit_id);
						$user_roles = $user_meta->roles;
						
						global $wp_roles;
						$all_roles = implode(', ', array_keys($wp_roles->role_names));
						
						$sell_content_roles = explode(',', trim(beeteam368_get_option('_sell_content_role_setting', '_live_streaming_settings', $all_roles)));
						
						$sell_content_roles_op = array();
						
						foreach($sell_content_roles as $role){
							$role = trim($role);
							if($role!=''){
								$sell_content_roles_op[] = $role;
							}
						}
						
						$permisions_sell_content = array();
						
						$permisions_sell_content = array_intersect($user_roles, $sell_content_roles_op);
						
					}else{
						$permisions_sell_content = array();
					}
					
				}else{
					$permisions_sell_content = array('Anyone');
				}
				
				if(count($permisions_sell_content) <= 0){
					return;
				}
				
				$settings = mycred_sell_content_settings();
				
				if ( !empty( $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ] ) && $settings['filters'][ BEETEAM368_POST_TYPE_PREFIX . '_video' ]['by'] === 'manual' ){
				?>
                	
                    <label class="h1"><?php echo esc_html__('Video - Pay Per View', 'beeteam368-extensions-pro')?></label>				
                    <div class="data-item">
                        <label for="post_video_sell_price" class="h5"><?php echo esc_html__('Purchase Price', 'beeteam368-extensions-pro')?></label>  
                        <em class="data-item-desc font-size-12"><?php echo esc_html__('If you want to sell access to this content, enter the sale price for it. FREE = 0 or blank', 'beeteam368-extensions-pro')?></em>                             
                        <input type="number" min="1" step="1" name="post_video_sell_price" id="post_video_sell_price" placeholder="<?php echo esc_attr__('1', 'beeteam368-extensions-pro')?>">
                    </div>
                    <div class="data-item">
                        <label for="post_video_sell_expiration" class="h5"><?php echo esc_html__('Expiration', 'beeteam368-extensions-pro')?></label>
                        <em class="data-item-desc font-size-12"><?php echo esc_html__('The default is 0, this is the expiration time to view that content after purchase. No expiration = 0 or blank', 'beeteam368-extensions-pro')?></em>                     
                        <input type="number" min="1" step="1" name="post_video_sell_expiration" id="post_video_sell_expiration" placeholder="<?php echo esc_attr__('0', 'beeteam368-extensions-pro')?>">
                    </div>
                    <hr>
                    
				<?php
				}
				
			}
		}
		
		function enable_sell_content_options($settings_options){
			if(defined('myCRED_SELL')){
				
				global $wp_roles;
				$all_roles = implode(', ', array_keys($wp_roles->role_names));
				
				$settings_options->add_field(array(
					'name' => esc_html__('Sell Content', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_submit_post_sell_content',
					'default' => 'on',
					'type' => 'select',
					'options' => array(
						'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
						'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
					),	
				));
				
					$settings_options->add_field(array(
						'name' => esc_html__('[Sell Content] Roles', 'beeteam368-extensions-pro'),
						'desc' => esc_html__('Define roles for each posting function.', 'beeteam368-extensions-pro'),
						'id' => BEETEAM368_PREFIX . '_sell_content_roles',
						'default' => 'off',
						'type' => 'select',
						'options' => array(
							'off' => esc_html__('OFF', 'beeteam368-extensions-pro'),         
							'on' => esc_html__('ON', 'beeteam368-extensions-pro'),                               
						),
						'attributes' => array(
							'data-conditional-id' => BEETEAM368_PREFIX . '_submit_post_sell_content',
							'data-conditional-value' => 'on',
						),
					));
				
					$settings_options->add_field(array(
						'name' => esc_html__('[Sell Content] Role Setting', 'beeteam368-extensions-pro'),
						'desc' => esc_html__('Permissions can Sell Content. Separated by commas. Eg(s): '.$all_roles, 'beeteam368-extensions-pro'),
						'id' => BEETEAM368_PREFIX . '_sell_content_role_setting',
						'default' => $all_roles,
						'type' => 'text',
						'attributes' => array(
							'data-conditional-id' => BEETEAM368_PREFIX . '_sell_content_roles',
							'data-conditional-value' => 'on',
						),				
					));
			}
		}
		
		function sales_count_on_listing($post_id, $params){
			if(defined('myCRED_SELL')){
				
				if(isset($params['post_type'])){
					$post_type = $params['post_type'];
				}else{
					$post_type = get_post_type($post_id);
				}
				
				$sales_count = mycred_get_content_sales_count($post_id);						
				if($sales_count == 0 && $post_type != BEETEAM368_POST_TYPE_PREFIX . '_video' && $post_type != BEETEAM368_POST_TYPE_PREFIX . '_audio'){				
					return;
				}
				
				echo '<span class="label-icon sales-count font-size-12"><i class="fas fa-chart-line"></i>&nbsp;&nbsp; '.esc_html(apply_filters('beeteam368_number_format', $sales_count)).'</span>';
			}
		}
		
		function sales_count_on_single($post_id, $params){
			if(defined('myCRED_SELL')){
				
				if(isset($params['post_type'])){
					$post_type = $params['post_type'];
				}else{
					$post_type = get_post_type($post_id);
				}
				
				$sales_count = mycred_get_content_sales_count($post_id);						
				if($sales_count == 0 && $post_type != BEETEAM368_POST_TYPE_PREFIX . '_video' && $post_type != BEETEAM368_POST_TYPE_PREFIX . '_audio'){				
					return;
				}	
				
				$sales_text = esc_html__('Sales', 'beeteam368-extensions-pro');	
				if($sales_count == 1){
					$sales_text = esc_html__('Sale', 'beeteam368-extensions-pro');
				}		
				?>
                <span class="post-footer-item post-sales-count post-sales-count-control">
                    <span class="beeteam368-icon-item small-item"><i class="fas fa-chart-line"></i></span><span class="item-number"><?php echo esc_html(apply_filters('beeteam368_number_format', $sales_count));?></span>
                    <span class="item-text"><?php echo esc_html($sales_text);?></span>
                </span>
                <?php
			}
		}
		
		function handle_watch_trailer_mycred_protect_in_content($content){
			$content = str_replace( '%watch_trailer%', '', $content );			
			return $content;
		}
		
		function protect($content, $post_id, $trailer_url, $type){
			if(defined('myCRED_SELL')){
				
				$this->current_user_id = get_current_user_id();
				
				global $mycred_partial_content_sale, $mycred_sell_this, $mycred_modules;
				
				$mycred_partial_content_sale = true;
				$img_background_cover = '';

				// $post_id = mycred_sell_content_post_id();
				$post = mycred_get_post( $post_id );
	
				// If content is for sale
				if ( mycred_post_is_for_sale( $post_id ) ) {
					
					if(has_post_thumbnail($post_id) && $imgsource = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'full')){
						$img_background_cover = 'style="background-image:url('.esc_url($imgsource[0]).');"';
					}
	
					$mycred_sell_this = true;
	
					// Parse shortcodes now just in case it has not been done already
					// $_content = do_shortcode( $content );
	
					// Partial Content Sale - We have already done the work in the shortcode
					// if ( $mycred_partial_content_sale === true )
						// return $_content;
	
					// Logged in users
					if ( is_user_logged_in() ) {
						
						$user_id  = get_current_user_id();
	
						// Authors and admins do not pay
						if ( ! mycred_is_admin() && $post->post_author != $this->current_user_id ) {
	
							// In case we have not paid
							if ( ! mycred_user_paid_for_content( $this->current_user_id, $post_id ) ) {
	
								// Get Payment Options
								$payment_options = mycred_sell_content_payment_buttons( $this->current_user_id, $post_id );
	
								// User can buy
								if ( $payment_options !== false  ) {
	
									$content = $mycred_modules['solo']['content']->sell_content['templates']['members'];
									$content = str_replace( '%buy_button%', $payment_options, $content );
									$content = mycred_sell_content_template( $content, $post, 'mycred-sell-entire-content', 'mycred-sell-unpaid' );
	
								}
	
								// Can not afford to buy
								else {
	
									$content = $mycred_modules['solo']['content']->sell_content['templates']['cantafford'];
									$content = mycred_sell_content_template( $content, $post, 'mycred-sell-entire-content', 'mycred-sell-insufficient' );
	
								}
	
							}
	
						}
	
					}
	
					// Visitors
					else {
	
						$content = $mycred_modules['solo']['content']->sell_content['templates']['visitors'];
						$content = mycred_sell_content_template( $content, $post, 'mycred-sell-entire-content', 'mycred-sell-visitor' );
	
					}
					
					if(trim($content) != ''){
						
						if($trailer_url!=''){
							$content = str_replace( '%watch_trailer%', '<a href="'.esc_url(add_query_arg(array('trailer' => 1), beeteam368_get_post_url($post_id)) ).'" class="btnn-default btnn-primary"><i class="fas fa-photo-video icon"></i><span>'.esc_html__('Trailer', 'beeteam368-extensions-pro').'</span></a>', $content );
						}else{
							$content = str_replace( '%watch_trailer%', '', $content );
						}
						
						$content = apply_filters('beeteam368_handle_protect_mycred', $content, $post_id, $trailer_url, $type);
						
						return '<div class="beeteam368-player beeteam368-player-protect dark-mode">
									<div class="beeteam368-player-wrapper temporaty-ratio">
										<div class="player-banner flex-vertical-middle" '.$img_background_cover.'>
											'.$content.'
										</div>
									</div>	
								</div>';
					}
	
				}
			}
			
			return $content;
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
		{
			if (is_array($values)) {
				$values[] = array('beeteam368-myCred-sell-content', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/mycred-sell-content/assets/mycred-sell-content.css', []);
			}
			return $values;
		}
	}
}

global $beeteam368_myCred_sell_content_front_end;
$beeteam368_myCred_sell_content_front_end = new beeteam368_myCred_sell_content_front_end();
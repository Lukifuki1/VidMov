<?php
if (!class_exists('beeteam368_buyCred_front_end')) {
    class beeteam368_buyCred_front_end
    {
        public function __construct()
        {
			add_action('cmb2_admin_init', array($this, 'settings'));

            add_filter('cmb2_conditionals_enqueue_script', function ($value) {
                global $pagenow;
                if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == BEETEAM368_PREFIX . '_buycred_settings') {
                    return true;
                }
                return $value;
            });
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			add_action('beeteam368_before_page', array($this, 'overwrite_buyCred_default_page'));
			add_action('beeteam368_purchases_item_dropdown_login', array($this, 'purchases_menu'));
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_filter('beeteam368_redirect_buy_cred', array($this, 'redirect_url_after_login'), 10, 1);
			
			add_filter('beeteam368_handle_protect_mycred', array($this, 'handle_link_buycred_page_mycred_protect'), 10, 4);			
			add_filter('the_content', array($this, 'handle_link_buycred_page_mycred_protect_in_content'), 50, 1);
			
			add_action('beeteam368_buyCred_nav_icon', array($this, 'purchases_icon_in_nav'), 10, 2);
		}
		
		function settings()
        {
			$settings_options = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_buycred_settings',
                'title' => esc_html__('buyCred Settings', 'beeteam368-extensions-pro'),
                'menu_title' => esc_html__('buyCred Settings', 'beeteam368-extensions-pro'),
                'object_types' => array('options-page'),
                'option_key' => BEETEAM368_PREFIX . '_buycred_settings',
                'icon_url' => 'dashicons-admin-generic',
                'position' => 2,
                'capability' => BEETEAM368_PREFIX . '_buycred_settings',
                'parent_slug' => BEETEAM368_PREFIX . '_theme_settings',
            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Stripe - buyCred Gateway', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF Stripe Gateway for buyCred.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_stripe_buycred_gateway',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'), 
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),  					                   
                ),
            ));
			
			$group_buy_btns = $settings_options->add_field(array(
				'id'          => BEETEAM368_PREFIX . '_stripe_btns_grp',
				'type'        => 'group',	
				'description' => wp_kses(__(
                    'You need to install myCred Stripe extension to use this feature: <a href="https://mycred.me/store/buycred-stripe/" target="_blank">Stripe - buyCred Gateway</a><br><br>', 'beeteam368-extensions-pro'),
                    array('br'=>array(), 'code'=>array(), 'strong'=>array(), 'a'=>array('href'=>array(), 'target'=>array()))
                ),
				'options'     => array(
					'group_title'   => esc_html__('Button {#}', 'beeteam368-extensions-pro'),
					'add_button'	=> esc_html__('Add Button', 'beeteam368-extensions-pro'),
					'remove_button' => esc_html__('Remove Button', 'beeteam368-extensions-pro'),
					'sortable'		=> true,				
					'closed'		=> true,
				),
				'repeatable'  => true,
			));
			
				$settings_options->add_group_field($group_buy_btns, array(
					'id'   			=> 'amount',
					'name' 			=> esc_html__( 'Amount', 'beeteam368-extensions-pro'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('The amount of points to sell using this shortcode. The price is automatically calculated using this amount and the exchange rate you set. If a user with a custom exchange rate views the shortcode, their price will adjust accordingly.', 'beeteam368-extensions-pro'),
					'repeatable' 	=> false,
					'attributes' => array(
						'type' => 'number',
					),
					'default' 		=> 50,
				));
				
				$settings_options->add_group_field($group_buy_btns, array(
					'id'   			=> 'btn_title',
					'name' 			=> esc_html__( 'Button Title', 'beeteam368-extensions-pro'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('Enter the title of the button.', 'beeteam368-extensions-pro'),
					'repeatable' 	=> false,					
					'default' 		=> esc_html__('Buy 50 Points', 'beeteam368-extensions-pro'),
				));
				
				$settings_options->add_group_field($group_buy_btns, array(
					'id'   			=> 'title',
					'name' 			=> esc_html__( 'Title', 'beeteam368-extensions-pro'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('Option to set a custom title for the Stripe Checkout window. If not set, the default title you set in your gateway settings is used.', 'beeteam368-extensions-pro'),
					'repeatable' 	=> false,
				));
				
				$settings_options->add_group_field($group_buy_btns, array(
					'id'   			=> 'label',
					'name' 			=> esc_html__( 'Label', 'beeteam368-extensions-pro'),
					'type' 			=> 'text',
					'desc' 			=> esc_html__('Option to set a custom button label for the Stripe Checkout window. If not set, the default button label you set in your gateway settings is used.', 'beeteam368-extensions-pro'),
					'repeatable' 	=> false,
				));
				
				$settings_options->add_group_field($group_buy_btns, array(
					'id'   			=> 'desc',
					'name' 			=> esc_html__( 'Descriptions', 'beeteam368-extensions-pro'),
					'type' 			=> 'textarea_code',
                	'options' 		=> array( 'disable_codemirror' => true ),
					'desc' 			=> esc_html__('Option to set a custom description for the Stripe Checkout window. If not set, the default description you set in your gateway settings is used.', 'beeteam368-extensions-pro'),
					'repeatable' 	=> false,
				));
				
			$settings_options->add_field(array(
                'name' => esc_html__('Advanced Methods with WooCommerce', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Turn ON/OFF Woocommerce for buyCred. You need to install the WooCommerce plugin to use this feature.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_woocommerce_buycred_gateway',
                'default' => 'off',
                'type' => 'select',
                'options' => array(
					'off' => esc_html__('OFF', 'beeteam368-extensions-pro'), 
                    'on' => esc_html__('ON', 'beeteam368-extensions-pro'),  					                   
                ),
            ));	
			
			$settings_options->add_field( array(
				'name' => esc_html__( 'WooCommerce Products', 'beeteam368-extensions-pro'),
				'id' => BEETEAM368_PREFIX . '_woo_products',
				'type' => 'post_search_ajax',
				'desc' => esc_html__( 'Start typing product name', 'beeteam368-extensions-pro'),
				'limit' => 2000000000, 		
				'sortable' => true,
				'query_args' => array(
					'post_type' => array( 'product' ),
					'post_status' => array( 'any' ),
					'posts_per_page' => -1
				)
			));
		}
		
		function handle_link_buycred_page_mycred_protect($content, $post_id, $trailer_url, $type){
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');			
			if(is_numeric($buyCred_page) && $buyCred_page > 0){
				$content = str_replace( '%buy_points%', '<a href="'.get_permalink($buyCred_page).'" class="btnn-default btnn-primary"><i class="fas fa-dollar-sign icon"></i><span>'.esc_html__('Buy Points', 'beeteam368-extensions-pro').'</span></a>', $content );
			}
			
			return $content;
		}
		
		function handle_link_buycred_page_mycred_protect_in_content($content){
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');			
			if(is_numeric($buyCred_page) && $buyCred_page > 0){
				$content = str_replace( '%buy_points%', '<a href="'.get_permalink($buyCred_page).'" class="btnn-default btnn-primary"><i class="fas fa-dollar-sign icon"></i><span>'.esc_html__('Buy Points', 'beeteam368-extensions-pro').'</span></a>', $content );
			}
			
			return $content;
		}
		
		function overwrite_buyCred_default_page(){
			
			$page_id = get_the_ID();
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');			
									
			if($page_id != $buyCred_page || $page_id == 0){
				return;
			}
			
			global $beetam368_not_show_default_page_content;
			$beetam368_not_show_default_page_content = 'off';
			?>
            <div class="beeteam368-mycred-buy-form">
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="far fa-credit-card"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Buy points to give away or watch premium videos', 'beeteam368-extensions-pro');?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Buy Points', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                    </h2>
                </div>
                
                <?php
				if(is_user_logged_in()){
					echo do_shortcode(apply_filters('beeteam368_mycred_buy_form', '[mycred_buy_form]'));
				}else{
				?>
                    <h2 class="h5 mycred-login-notice">
                        <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_buy_form_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                            <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                        </a>
                    </h2>
                    <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php	
				}				
				?>
            </div>            
            
            <?php if(beeteam368_get_option('_stripe_buycred_gateway', '_buycred_settings', 'off') === 'on'){?>
            
                <div class="beeteam368-mycred-buy-form buycred-stripe-form">
                    <div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="far fa-credit-card"></i></span>
                        <span class="sub-title font-main"><?php echo esc_html__('Buy points to give away or watch premium videos', 'beeteam368-extensions-pro');?></span>
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Buy points with Stripe', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                        </h2>
                    </div>
                    
                    <?php
                    if(is_user_logged_in()){
						$stripe_btns = beeteam368_get_option('_stripe_btns_grp', '_buycred_settings', array());
						
						if(is_array($stripe_btns) && count($stripe_btns) > 0){
							
							$stripe_btn_string = '';
							
							foreach($stripe_btns as $stripe_btn){
								if(isset($stripe_btn['amount']) && is_numeric($stripe_btn['amount']) && isset($stripe_btn['btn_title']) && trim($stripe_btn['btn_title'])!=''){
									$title = isset($stripe_btn['title'])&&trim($stripe_btn['title'])!=''?' title="'.esc_html(trim($stripe_btn['title'])).'" ':'';
									$label = isset($stripe_btn['label'])&&trim($stripe_btn['label'])!=''?' label="'.esc_html(trim($stripe_btn['label'])).'" ':'';
									$desc = isset($stripe_btn['desc'])&&trim($stripe_btn['desc'])!=''?' desc="'.esc_html(trim($stripe_btn['desc'])).'" ':'';
								}
								
								$stripe_btn_string.='[mycred_stripe_buy classes="beeteam368-stripe-btn" amount="'.$stripe_btn['amount'].'"'.$title.$label.$desc.']'.trim($stripe_btn['btn_title']).'[/mycred_stripe_buy]';
							}
							
							if($stripe_btn_string!=''){
								echo do_shortcode($stripe_btn_string);
							}
						}
                    }else{
                    ?>
                        <h2 class="h5 mycred-login-notice">
                            <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_buy_form_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                                <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                            </a>
                        </h2>
                        <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                    <?php	
                    }
                    ?>
                </div>
            
            <?php
			}
			if(beeteam368_get_option('_woocommerce_buycred_gateway', '_buycred_settings', 'off') === 'on'){
			?>
            
                <div class="beeteam368-mycred-buy-form buycred-woo-form">
                    <div class="top-section-title has-icon">
                        <span class="beeteam368-icon-item"><i class="far fa-credit-card"></i></span>
                        <span class="sub-title font-main"><?php echo esc_html__('Buy points to give away or watch premium videos', 'beeteam368-extensions-pro');?></span>
                        <h2 class="h2 h3-mobile main-title-heading">                            
                            <span class="main-title"><?php echo esc_html__('Buy points with Advanced Method', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                        </h2>
                    </div>
                    
                    <?php
                    if(is_user_logged_in()){
						$_woo_products = beeteam368_get_option('_woo_products', '_buycred_settings', array());
						if(is_array($_woo_products) && count($_woo_products) > 0){
							echo do_shortcode('[products ids="'.implode(',', $_woo_products).'"]');
						}
                    }else{
                    ?>
                        <h2 class="h5 mycred-login-notice">
                            <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_buy_form_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                                <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                            </a>
                        </h2>
                        <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                    <?php	
                    }
                    ?>
                </div> 
                   
			<?php }?>
            
            <div class="beeteam368-mycred-load-coupon">
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="fas fa-comment-dollar"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Enter coupon code to get points', 'beeteam368-extensions-pro');?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Redeeming Coupons', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                    </h2>
                </div>
                
                <?php 
				if(is_user_logged_in()){
					echo do_shortcode(apply_filters('beeteam368_mycred_load_coupon', '[mycred_load_coupon]'));
				}else{
				?>
                    <h2 class="h5 mycred-login-notice">
                        <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_load_coupon_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                            <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                        </a>
                    </h2>
                    <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php	
				}
				?>
            </div>
			
            <div id="your_balance_in_purchases" class="beeteam368-mycred-my-balance">
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="fas fa-dollar-sign"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Your Balance', 'beeteam368-extensions-pro');?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Your Accumulated Points', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                    </h2>
                </div>
                
                <?php 
				if(is_user_logged_in()){					
					echo do_shortcode(apply_filters('beeteam368_mycred_my_balance_in_buycred', '<div class="h1 h1-single myCred-total-balance">[mycred_my_balance]</div>'));
				}else{
				?>
                    <h2 class="h5 mycred-login-notice">
                        <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_my_balance_in_buycred_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                            <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                        </a>
                    </h2>
                    <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php	
				}
				?>
            </div>
                        
            <div class="beeteam368-mycred-buy-pending">
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="fas fa-shopping-basket"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Pending List', 'beeteam368-extensions-pro');?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Pending Payments', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                    </h2>
                </div>
                <?php 
				if(is_user_logged_in()){
					echo do_shortcode(apply_filters('beeteam368_mycred_buy_pending', '[mycred_buy_pending]'));
				}else{
				?>
                    <h2 class="h5 mycred-login-notice">
                        <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_buy_pending_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                            <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                        </a>
                    </h2>
                    <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php	
				}?>
            </div>
            
            <div class="beeteam368-mycred-sales-history">
                <div class="top-section-title has-icon">
                    <span class="beeteam368-icon-item"><i class="fas fa-cash-register"></i></span>
                    <span class="sub-title font-main"><?php echo esc_html__('Your Transaction History', 'beeteam368-extensions-pro');?></span>
                    <h2 class="h2 h3-mobile main-title-heading">                            
                        <span class="main-title"><?php echo esc_html__('Sales History', 'beeteam368-extensions-pro');?></span> <span class="hd-line"></span>
                    </h2>
                </div>
                <?php 
				if(is_user_logged_in()){
					echo do_shortcode(apply_filters('beeteam368_mycred_sales_history', '[mycred_sales_history]'));
				}else{
				?>
                    <h2 class="h5 mycred-login-notice">
                        <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'mycred_sales_history_button'));?>" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>" class="reg-log-popup-control">
                            <?php echo esc_html__('Please login to use this feature.', 'beeteam368-extensions-pro');?>
                        </a>
                    </h2>
                    <button type="button" class="icon-style reg-log-popup-control" data-redirect="buycred_page" data-note="<?php echo esc_attr__('Please login to use this feature!', 'beeteam368-extensions-pro')?>"><i class="fas fa-users-cog icon"></i><span><?php echo esc_html__('Login', 'beeteam368-extensions-pro');?></span></button>
                <?php	
				}?>
            </div>
            <?php
		}
		
		function purchases_icon_in_nav($position, $beeteam368_header_style){
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');
			
			if(!is_numeric($buyCred_page) || $buyCred_page < 1){
				return;
			}
		?>
        	<a href="<?php echo get_permalink($buyCred_page);?>" class="beeteam368-icon-item beeteam368-top-menu-purchases tooltip-style bottom-center">
            	<i class="fas fa-dollar-sign"></i>
                <span class="tooltip-text"><?php echo esc_html__('Purchases', 'beeteam368-extensions-pro')?></span>
            </a>
        <?php	
		}
		
		
		function purchases_menu($user_id){
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');
			
			if(!is_numeric($buyCred_page) || $buyCred_page < 1){
				return;
			}
		?>
            <a href="<?php echo get_permalink($buyCred_page);?>" class="purchases-menu flex-row-control flex-vertical-middle icon-drop-down-url">
                <span class="beeteam368-icon-item">
                   <i class="fas fa-dollar-sign"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Purchases', 'beeteam368-extensions-pro')?></span>
                
            </a>
            
            <a href="<?php echo get_permalink($buyCred_page);?>#your_balance_in_purchases" class="balance-menu flex-row-control flex-vertical-middle icon-drop-down-url">
                <span class="beeteam368-icon-item">
                   <i class="fas fa-wallet"></i>
                </span>
                <span class="nav-font"><?php echo esc_html__('Your Balance:', 'beeteam368-extensions-pro').' '.do_shortcode(apply_filters('beeteam368_mycred_my_balance_in_nav', '[mycred_my_balance wrapper="0" balance_el=""]'))?></span>
                
            </a>
        <?php	
		}
		
		function redirect_url_after_login($url){
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');
			
			if(is_numeric($buyCred_page) && $buyCred_page > 0){
				return get_permalink($buyCred_page);
			}
			
			return $url;
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-buyCred', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/buycred/assets/buycred.css', []);
            }
            return $values;
        }
		
		function localize_script($define_js_object){			
			$buyCred_page = beeteam368_get_option('_buycred_page', '_theme_settings', '');			
			if(is_numeric($buyCred_page) && $buyCred_page > 0){
				$define_js_object['mycred_purchases_page'] = get_permalink($buyCred_page);
				$define_js_object['mycred_purchases_page_text'] = esc_html__('Buy Points', 'beeteam368-extensions-pro');
			}			
            return $define_js_object;
        }
	}
}

global $beeteam368_buyCred_front_end;
$beeteam368_buyCred_front_end = new beeteam368_buyCred_front_end();
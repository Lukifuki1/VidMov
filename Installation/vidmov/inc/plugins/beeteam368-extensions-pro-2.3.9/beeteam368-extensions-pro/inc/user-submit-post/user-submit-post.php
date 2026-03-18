<?php
if (!class_exists('beeteam368_user_submit_post_front_end')) {
    class beeteam368_user_submit_post_front_end
    {
        public function __construct()
        {
			add_filter('beeteam368_channel_side_menu_settings_tab', array($this, 'add_tab_side_menu_settings'));			
			add_action('beeteam368_after_channel_side_menu_settings', array($this, 'add_option_side_menu_settings'));
			
			add_filter('beeteam368_channel_tab_settings_tab', array($this, 'add_tab_tab_settings'));	
			add_filter('beeteam368_channel_settings_tab', array($this, 'add_layout_settings_tab'));		
			add_action('beeteam368_after_channel_tab_settings', array($this, 'add_option_tab_settings'));			
            
            add_action('beeteam368_side_menu_videos', array($this, 'videos_side_menu'), 10, 1);
			add_action('beeteam368_side_menu_audios', array($this, 'audios_side_menu'), 10, 1);
			add_action('beeteam368_side_menu_playlists', array($this, 'playlists_side_menu'), 10, 1);
			add_action('beeteam368_side_menu_posts', array($this, 'posts_side_menu'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_videos', array($this, 'show_in_tab_videos'), 10, 2);
			add_action('beeteam368_channel_fe_tab_audios', array($this, 'show_in_tab_audios'), 10, 2);
			add_action('beeteam368_channel_fe_tab_playlists', array($this, 'show_in_tab_playlists'), 10, 2);
			add_action('beeteam368_channel_fe_tab_posts', array($this, 'show_in_tab_posts'), 10, 2);
			
			add_filter('beeteam368_channel_order_tab', array($this, 'show_in_tab_order'), 10, 1);
			
			add_filter('beeteam368_channel_order_side_menu', array($this, 'show_in_side_menu_order'), 10, 1);
			
			add_action('beeteam368_channel_fe_tab_content_videos', array($this, 'channel_tab_content_videos'), 10, 2);
			add_action('beeteam368_channel_fe_tab_content_audios', array($this, 'channel_tab_content_audios'), 10, 2);
			add_action('beeteam368_channel_fe_tab_content_playlists', array($this, 'channel_tab_content_playlists'), 10, 2);
			add_action('beeteam368_channel_fe_tab_content_posts', array($this, 'channel_tab_content_posts'), 10, 2);
			
			add_action('beeteam368_channel_privacy_videos', array($this, 'profile_privacy_videos'), 10, 1);
			add_action('beeteam368_channel_privacy_audios', array($this, 'profile_privacy_audios'), 10, 1);
			add_action('beeteam368_channel_privacy_playlists', array($this, 'profile_privacy_playlists'), 10, 1);
			add_action('beeteam368_channel_privacy_posts', array($this, 'profile_privacy_posts'), 10, 1);
			
			add_action('wp_ajax_beeteam368_add_new_playlist', array($this, 'add_new_playlist'));
            add_action('wp_ajax_nopriv_beeteam368_add_new_playlist', array($this, 'add_new_playlist'));
			
			add_action('beeteam368_add_playlist_in_single', array($this, 'add_playlist_in_single'), 10, 3);
			
			add_filter('beeteam368_define_js_object', array($this, 'localize_script'), 10, 1);
			
			add_filter('beeteam368_css_footer_party_files', array($this, 'playlist_submit_css'), 10, 4);
			add_filter('beeteam368_js_party_files', array($this, 'playlist_submit_js'), 10, 4);
			
			add_filter('beeteam368_channel_before_query_tab', array($this, 'query_privacy'), 10, 5);
			
			add_action('wp_ajax_beeteam368_add_post_to_playlist_fe', array($this, 'add_post_to_playlist'));
            add_action('wp_ajax_nopriv_beeteam368_add_post_to_playlist_fe', array($this, 'add_post_to_playlist'));
        }
		
		function playlist_opt_ste(){
			return beeteam368_get_option('_playlist', '_theme_settings', 'on');
		}
		
		function add_post_to_playlist(){
			$result = array();
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
            if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['playlist_id']) || !is_numeric($_POST['playlist_id']) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || $this->playlist_opt_ste() === 'off' ) {
				$result['error'] = true;
                wp_send_json($result);
                return;
                die();
            }
			
			$current_user = wp_get_current_user();
			$user_submit_id = $current_user->ID;
			
			$author_id = get_post_field('post_author', $_POST['playlist_id']);
			
			if($user_submit_id == $author_id){
				$old_playlists = get_post_meta($_POST['post_id'], BEETEAM368_PREFIX . '_playlists', true);
				if(!is_array($old_playlists) || count($old_playlists) < 1){
					$old_playlists = array();
				}
				
				if(($found_key = array_search((string)$_POST['playlist_id'], $old_playlists)) !== FALSE){
                     unset($old_playlists[$found_key]);
					 $result['success'] = 'removed';
                }else{
					$old_playlists[] = (string)$_POST['playlist_id'];
					$result['success'] = 'added';
				}
				
				$new_playlists = $old_playlists;
				
				update_post_meta($_POST['post_id'], BEETEAM368_PREFIX . '_playlists', $new_playlists);
			}
			
			wp_send_json($result);
			return;
			die();
		}
		
		function query_privacy($args_query, $source, $post_type, $author_id, $tab){
			if($tab == 'videos' || $tab == 'audios' || $tab == 'playlists' || $tab == 'posts'){
				$user_id = 0;
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();
					$user_id = $current_user->ID;
				}
				
				if($user_id == $author_id){
					$args_query['post_status'] = 'any';
				}
			}
			
			return $args_query;
		}
		
		function add_new_playlist(){
			$result = array();
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			
            if ( !beeteam368_ajax_verify_nonce($security, true) || !isset($_POST['post_id']) || !is_numeric($_POST['post_id']) || $this->playlist_opt_ste() === 'off') {
				$result['messages'] = '<span>'.esc_html__('You do not have permission to submit playlists.', 'beeteam368-extensions-pro').'</span>';
                wp_send_json($result);
                return;
                die();
            }			
			
			$current_user = wp_get_current_user();
			$user_submit_id = $current_user->ID;			
			
			$playlist_title = isset($_POST['playlist_title'])?trim($_POST['playlist_title']):'';
			
			if($playlist_title==''){
				$result['messages'] = '<span>'.esc_html__('Please enter the title of the playlist.', 'beeteam368-extensions-pro').'</span>';
                wp_send_json($result);
                return;
                die();
			}
			
			if (isset($_FILES['playlist_image']) && isset($_FILES['playlist_image']['error']) && $_FILES['playlist_image']['error'] == 0){
				if($_FILES['playlist_image']['size'] > 3145728){
					$result['messages'] = '<span>'.esc_html__('The playlist image is too big.', 'beeteam368-extensions-pro').'</span>';
					wp_send_json($result);
					return;
					die();
				}else{
					if(!function_exists('wp_handle_upload') || !function_exists('wp_crop_image') || !function_exists('wp_generate_attachment_metadata')){
						require_once( ABSPATH . 'wp-admin/includes/admin.php' );
						require_once( ABSPATH . 'wp-admin/includes/image.php' );
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						require_once( ABSPATH . 'wp-admin/includes/media.php' );
					}
					
					$has_playlist_image = 1;
				}
			}
			
			$playlist_privacy = isset($_POST['playlist_privacy'])?trim($_POST['playlist_privacy']):'publish';
			
			$postData = array();			
			$postData['post_type'] = BEETEAM368_POST_TYPE_PREFIX . '_playlist';			
			$postData['post_title'] = $playlist_title;	
			$postData['post_status'] = $playlist_privacy;
			$postData['post_author'] = $user_submit_id;
			
			$newPostID = wp_insert_post($postData);
			
			if(!is_wp_error($newPostID) && $newPostID){
				$result['new_id'] = $newPostID;
				$result['new_title'] = get_the_title($newPostID);
				$result['new_privacy'] = $playlist_privacy;
				$result['post_id'] = $_POST['post_id'];
				
				$icon_privacy = '';
				if($playlist_privacy === 'publish'){
					$icon_privacy = '<i class="playlist-status fas fa-globe-americas"></i>&nbsp;&nbsp; ';
				}else{
					$icon_privacy = '<i class="playlist-status fas fa-lock"></i>&nbsp;&nbsp; ';
				}
				$result['playlist_html'] = '<div class="playlist-item playlist-item-control flex-vertical-middle" data-playlist-id="'.esc_attr($newPostID).'" data-post-id="'.esc_attr($_POST['post_id']).'">
												<div class="beeteam368-icon-item playlist-added-control">
													<i class="fas fa-plus"></i>
												</div>
												<h2 class="playlist-title h5 h6-mobile max-1line">
													'.$icon_privacy.get_the_title($newPostID).'
												</h2>
											</div>';
				
				if(isset($has_playlist_image)){
					
					$upload_overrides 	= array( 'test_form' => false );
					$movefile 			= wp_handle_upload( $_FILES['playlist_image'], $upload_overrides );
					
					if ( $movefile && !isset( $movefile['error'] ) ) {						
						$attachment = array(
							'post_mime_type' 	=> $movefile['type'],
							'post_parent' 		=> $newPostID,
							'post_title' 		=> sanitize_file_name($_FILES['playlist_image']['name']),
							'post_content' 		=> '',
							'post_status' 		=> 'inherit'
						);
						
						$attach_id = wp_insert_attachment( $attachment, $movefile['file'], $newPostID );
						
						$attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
						wp_update_attachment_metadata( $attach_id, $attach_data );	
						set_post_thumbnail( $newPostID, $attach_id );
					}
				}
			}else{
				$result['messages'] = '<span>'.esc_html__('An error has occurred. Please reload the page and try again!', 'beeteam368-extensions-pro').'</span>';
				wp_send_json($result);
				return;
				die();
			}
			
			$result['messages'] = '<span class="success">'.esc_html__('Thanks, you have successfully submitted the playlist!', 'beeteam368-extensions-pro').'</span>';
            wp_send_json($result);
			
			return;
            die();
		}
		
		function add_playlist_in_single($post_id, $pos_style, $wrap){
			if($this->playlist_opt_ste() === 'off'){
				return;
			}
			
			if($wrap){
				echo '<div class="sub-block-wrapper">';
			}
				if(is_user_logged_in()){
					$current_user = wp_get_current_user();
					$user_id = (int)$current_user->ID;
				?>
                    <div class="beeteam368-icon-item is-square tooltip-style beeteam368-global-open-popup-control" data-popup-id="playlist_add_popup" data-action="open_popup_add_playlist">
                        <i class="icon fas fa-list"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Add To Playlist', 'beeteam368-extensions-pro')?></span>
                    </div>
                <?php	
				}else{
				?>
                	<a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'add_new_playlist'));?>" class="beeteam368-icon-item is-square tooltip-style reg-log-popup-control" data-note="<?php echo esc_attr__('Please login to view and update your playlists.', 'beeteam368-extensions-pro')?>">
                        <i class="icon fas fa-list"></i>
                        <span class="tooltip-text"><?php echo esc_html__('Add To Playlist', 'beeteam368-extensions-pro')?></span>
                    </a>                    
				<?php
				}
			if($wrap){				
				echo '</div>';
			}
			
			if(is_user_logged_in()){
			?>
                <div class="beeteam368-global-popup beeteam368-playlist-add-popup beeteam368-global-popup-control flex-row-control flex-vertical-middle flex-row-center" data-popup-id="playlist_add_popup">
                    <div class="beeteam368-global-popup-content beeteam368-global-popup-content-control">
                        <h2 class="h3-mobile"><?php echo esc_html__('Save to...', 'beeteam368-extensions-pro');?></h2>
                        
                        <hr>
                        
                        <div class="beeteam368-playlist-add-wrapper beeteam368-playlist-add-wrapper-control">
							<?php 
                            $args_query = array(
                                'post_type'				=> BEETEAM368_POST_TYPE_PREFIX . '_playlist',
                                'author'				=> $user_id,
                                'posts_per_page' 		=> -1,
                                'post_status' 			=> 'any',
                                'ignore_sticky_posts' 	=> 1,		
                            );
                            
                            $playlists = get_posts($args_query);	
                            ?>
                            <div class="playlist-listing playlist-listing-control">
                                <?php 
                                if($playlists){
									
									$old_playlists = get_post_meta($post_id, BEETEAM368_PREFIX . '_playlists', true);
									if(!is_array($old_playlists) || count($old_playlists) < 1){
										$old_playlists = array();
									}
									
                                    foreach ($playlists as $playlist){
                                        $playlist_id = $playlist->ID;
										$current_status = get_post_status($playlist_id);
										
										if(($found_key = array_search((string)$playlist_id, $old_playlists)) !== FALSE){
											 $icon = '<div class="beeteam368-icon-item primary-color-focus playlist-added-control"><i class="fas fa-list-ul"></i></div>';
										}else{
											 $icon = '<div class="beeteam368-icon-item playlist-added-control"><i class="fas fa-plus"></i></div>';
										}
                                    ?>
                                        <div class="playlist-item playlist-item-control flex-vertical-middle" data-playlist-id="<?php echo esc_attr($playlist_id)?>" data-post-id="<?php echo esc_attr($post_id)?>">
                                            <?php echo apply_filters('beeteam368_icon_add_playlist_in_single_media', $icon);?>
                                            <h2 class="playlist-title h5 h6-mobile max-1line">
												<?php 
												if($current_status === 'publish'){
													echo '<i class="playlist-status fas fa-globe-americas"></i>&nbsp;&nbsp; ';
												}else{
													echo '<i class="playlist-status fas fa-lock"></i>&nbsp;&nbsp; ';
												}
												echo get_the_title($playlist_id);
												?>
                                            </h2>
                                        </div>
                                    <?php	
                                    }
                                }else{
                                ?>
                                    <span class="no-playlist-yet no-playlist-yet-control"><?php echo esc_html__('No playlists yet. Let\'s create a new playlist!', 'beeteam368-extensions-pro');?></span>
                                <?php	
                                }
                                ?>	
                            </div>
                            
                            <div class="playlist-add-wrapper playlist-add-wrapper-control">
                                <hr>                             
                                <div class="playlist-alerts playlist-alerts-control"></div>                        
                                <form name="add-playlist" class="form-playlis-control" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id)?>">
                                    <div class="data-item">
                                    	<label for="playlist_title" class="h5"><?php echo esc_html__('Playlist Title', 'beeteam368-extensions-pro')?></label>
                                        <input type="text" name="playlist_title" id="playlist_title" placeholder="<?php echo esc_attr__('Playlist Title', 'beeteam368-extensions-pro')?>">
                                    </div>
                                    
                                    <div class="data-item">
                                        <label for="playlist_image" class="h5"><?php echo esc_html__('Playlist Image', 'beeteam368-extensions-pro')?></label>
                                        <input type="file" name="playlist_image" id="playlist_image" size="40" accept=".gif,.png,.jpg,.jpeg">
                                    </div>
                                    <div class="data-item">
                                        <label for="playlist_privacy" class="h5"><?php echo esc_html__('Privacy', 'beeteam368-extensions-pro')?></label>
                                        <select name="playlist_privacy" id="playlist_privacy">
                                            <option value="publish"><?php echo esc_html__('Public', 'beeteam368-extensions-pro')?></option>
                                            <option value="private"><?php echo esc_html__('Private', 'beeteam368-extensions-pro')?></option>
                                        </select>
                                    </div>
                                    
                                    <div class="data-item">
                                        <button name="submit" type="button" class="loadmore-btn playlist-add-control">
                                            <span class="loadmore-text loadmore-text-control"><?php echo esc_html__('Create', 'beeteam368-extensions-pro');?></span>
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
                        
                        <hr>
                        
                        <div class="open-add-playlist open-add-playlist-control flex-vertical-middle">
                        	<div class="beeteam368-icon-item">
                                <i class="fas fa-plus"></i>
                            </div>
							<span><?php echo esc_html__('Create New Playlist', 'beeteam368-extensions-pro')?></span>
                        </div>
                    </div>
                </div>
            <?php	
			}
		}
		
		function profile_privacy_videos($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_videos', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="videos"><?php echo esc_html__('Videos Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="videos" id="videos" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function profile_privacy_audios($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_audios', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="audios"><?php echo esc_html__('Audios Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="audios" id="audios" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function profile_privacy_playlists($user_id){
			if($this->playlist_opt_ste() === 'off' || beeteam368_get_option('_channel_playlists_tab_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_playlists', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="playlists"><?php echo esc_html__('Playlists Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="playlists" id="playlists" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function profile_privacy_posts($user_id){
			$user_meta = sanitize_text_field(get_user_meta($user_id, BEETEAM368_PREFIX . '_privacy_posts', true));
		?>
        	<div class="tml-field-wrap site__col">
              <label class="tml-label" for="posts"><?php echo esc_html__('Posts Tab [Privacy]', 'beeteam368-extensions-pro');?></label>
              <select name="posts" id="posts" class="privacy-option">
              	<option value="public" <?php if($user_meta==='public'){echo 'selected';}?>><?php echo esc_html__('Public', 'beeteam368-extensions-pro');?></option>
                <option value="private" <?php if($user_meta==='private'){echo 'selected';}?>><?php echo esc_html__('Private', 'beeteam368-extensions-pro');?></option>
              </select>              
            </div>
        <?php	
		}
		
		function channel_tab_content_videos($author_id, $tab){
			if($tab!='videos'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'videos', array(BEETEAM368_POST_TYPE_PREFIX . '_video'), $author_id, $tab);
			
		}
		
		function channel_tab_content_audios($author_id, $tab){
			if($tab!='audios'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'audios', array(BEETEAM368_POST_TYPE_PREFIX . '_audio'), $author_id, $tab);
			
		}
		
		function channel_tab_content_playlists($author_id, $tab){
			if($tab!='playlists' || $this->playlist_opt_ste() === 'off'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'playlists', array(BEETEAM368_POST_TYPE_PREFIX . '_playlist'), $author_id, $tab);

		}
		
		function channel_tab_content_posts($author_id, $tab){
			if($tab!='posts'){
				return;
			}
			
			do_action('beeteam368_show_posts_in_channel_tab', 'posts', array('post'), $author_id, $tab);

		}
		
		function show_in_side_menu_order($tabs){
			if(beeteam368_get_option('_channel_videos_item', '_channel_settings', 'on') === 'on'){
				$tabs['videos'] = esc_html__('Your Videos', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_audios_item', '_channel_settings', 'on') === 'on'){
				$tabs['audios'] = esc_html__('Your Audios', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_playlists_item', '_channel_settings', 'on') === 'on' && $this->playlist_opt_ste() === 'on'){
				$tabs['playlists'] = esc_html__('Your Playlists', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_posts_item', '_channel_settings', 'on') === 'on'){
				$tabs['posts'] = esc_html__('Your Posts', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab_order($tabs){
			if(beeteam368_get_option('_channel_videos_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['videos'] = esc_html__('Videos', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_audios_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['audios'] = esc_html__('Audios', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_playlists_tab_item', '_channel_settings', 'on') === 'on' && $this->playlist_opt_ste() === 'on'){
				$tabs['playlists'] = esc_html__('Playlists', 'beeteam368-extensions-pro');
			}
			
			if(beeteam368_get_option('_channel_posts_tab_item', '_channel_settings', 'on') === 'on'){
				$tabs['posts'] = esc_html__('Posts', 'beeteam368-extensions-pro');
			}
			return $tabs;
		}
		
		function show_in_tab_videos($author_id, $tab){
			if(beeteam368_get_option('_channel_videos_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_videos_tab_name', 'videos'))));?>" class="swiper-slide tab-item<?php if($tab == 'videos'){echo ' active-item';}?>" title="<?php echo esc_attr__('Videos', 'beeteam368-extensions-pro');?>">                    	
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-video"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Videos', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'videos', $author_id);?>
                </a>
        <?php
			}
		}
		
		function show_in_tab_audios($author_id, $tab){
			if(beeteam368_get_option('_channel_audios_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_audios_tab_name', 'audios'))));?>" class="swiper-slide tab-item<?php if($tab == 'audios'){echo ' active-item';}?>" title="<?php echo esc_attr__('Audios', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-music"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Audios', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'audios', $author_id);?>
                </a>
        <?php
			}
		}
		
		function show_in_tab_playlists($author_id, $tab){
			if(beeteam368_get_option('_channel_playlists_tab_item', '_channel_settings', 'on') === 'on' && $this->playlist_opt_ste() === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_playlists_tab_name', 'playlists'))));?>" class="swiper-slide tab-item<?php if($tab == 'playlists'){echo ' active-item';}?>" title="<?php echo esc_attr__('Playlists', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-list-ul"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Playlists', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'playlists', $author_id);?>
                </a>
        <?php
			}
		}
		
		function show_in_tab_posts($author_id, $tab){
			if(beeteam368_get_option('_channel_posts_tab_item', '_channel_settings', 'on') === 'on'){
		?>
        		<a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($author_id, array('channel-tab' => apply_filters('beeteam368_channel_posts_tab_name', 'posts'))));?>" class="swiper-slide tab-item<?php if($tab == 'posts'){echo ' active-item';}?>" title="<?php echo esc_attr__('Posts', 'beeteam368-extensions-pro');?>">
                    <span class="beeteam368-icon-item tab-icon">
                        <i class="fas fa-blog"></i>
                    </span>
                    <span class="tab-text h5"><?php echo esc_html__('Posts', 'beeteam368-extensions-pro');?></span>
                    <?php do_action('beeteam368_channel_privacy_label', 'posts', $author_id);?>
                </a>        
        <?php
			}
		}
		
		function add_tab_side_menu_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_videos_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_audios_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_playlists_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_posts_item';
			return $tabs;
		}
		
		function add_option_side_menu_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Your Videos" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Your Videos" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Your Audios" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Your Audios" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
			
			if($this->playlist_opt_ste() === 'on'){
				$settings_options->add_field(array(
					'name' => esc_html__('Display "Your Playlists" Item (Pro)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Hide or show "Your Playlists" item on Side Menu.', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_item',
					'default' => 'on',
					'type' => 'select',
					'options' => array(
						'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
						'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
					),
	
				));
			}
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Your Posts" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Your Posts" item on Side Menu.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
		}
		
		function add_tab_tab_settings($tabs){
			$tabs[] = BEETEAM368_PREFIX . '_channel_videos_tab_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_audios_tab_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_playlists_tab_item';
			$tabs[] = BEETEAM368_PREFIX . '_channel_posts_tab_item';
			return $tabs;
		}
		
		function add_layout_settings_tab($all_tabs){
			$all_tabs[] = array(
				'id' => 'videos-tab-settings',
				'icon' => 'dashicons-format-video',
				'title' => esc_html__('Videos', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_videos', array(
					BEETEAM368_PREFIX . '_channel_videos_tab_layout',
					BEETEAM368_PREFIX . '_channel_videos_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_videos_tab_pagination',
					BEETEAM368_PREFIX . '_channel_videos_tab_order',
					BEETEAM368_PREFIX . '_channel_videos_tab_categories'
				)),
			);
			
			$all_tabs[] = array(
				'id' => 'audios-tab-settings',
				'icon' => 'dashicons-format-audio',
				'title' => esc_html__('Audios', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_audios', array(
					BEETEAM368_PREFIX . '_channel_audios_tab_layout',
					BEETEAM368_PREFIX . '_channel_audios_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_audios_tab_pagination',
					BEETEAM368_PREFIX . '_channel_audios_tab_order',
					BEETEAM368_PREFIX . '_channel_audios_tab_categories'
				)),
			);
			
			if($this->playlist_opt_ste() === 'on'){
				$all_tabs[] = array(
					'id' => 'playlists-tab-settings',
					'icon' => 'dashicons-playlist-video',
					'title' => esc_html__('Playlists', 'beeteam368-extensions-pro'),
					'fields' => apply_filters('beeteam368_channel_tab_playlists', array(
						BEETEAM368_PREFIX . '_channel_playlists_tab_layout',
						BEETEAM368_PREFIX . '_channel_playlists_tab_items_per_page',
						BEETEAM368_PREFIX . '_channel_playlists_tab_pagination',
						BEETEAM368_PREFIX . '_channel_playlists_tab_order',
						BEETEAM368_PREFIX . '_channel_playlists_tab_categories'
					)),
				);
			}
			
			$all_tabs[] = array(
				'id' => 'posts-tab-settings',
				'icon' => 'dashicons-admin-post',
				'title' => esc_html__('Posts', 'beeteam368-extensions-pro'),
				'fields' => apply_filters('beeteam368_channel_tab_posts', array(
					BEETEAM368_PREFIX . '_channel_posts_tab_layout',
					BEETEAM368_PREFIX . '_channel_posts_tab_items_per_page',
					BEETEAM368_PREFIX . '_channel_posts_tab_pagination',
					BEETEAM368_PREFIX . '_channel_posts_tab_order',
					BEETEAM368_PREFIX . '_channel_posts_tab_categories'
				)),
			);
			
			return $all_tabs;
		}
		
		function add_option_tab_settings($settings_options){
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Videos" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Videos" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
			
			/*Videos*/
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),				
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_pagination',
                'default' => 'wp-default',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions-pro'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions-pro'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions-pro'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions-pro'),
                    */
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_order',
                'default' => 'new',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions-pro'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions-pro'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_videos_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));/*Videos*/
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Audios" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Audios" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),

            ));
			
			/*Audios*/
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),				
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_pagination',
                'default' => 'wp-default',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions-pro'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions-pro'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions-pro'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions-pro'),
                    */
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_order',
                'default' => 'new',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(                    
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions-pro'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions-pro'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_audios_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));/*Audios*/
			
			if($this->playlist_opt_ste() === 'on'){
				$settings_options->add_field(array(
					'name' => esc_html__('Display "Playlists" Item (Pro)', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Hide or show "Playlists" item on Tab.', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_item',
					'default' => 'on',
					'type' => 'select',
					'options' => array(
						'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
						'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
					),
	
				));
			}
			
			/*Playlists*/
			if($this->playlist_opt_ste() === 'on'){
				$settings_options->add_field(array(
					'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_layout',
					'default' => '',
					'type' => 'radio_image',
					'images_path' => get_template_directory_uri(),
					'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
						'' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
					)),
					'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
						'' => '/inc/theme-options/images/archive-to.png',
					)),
					'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),				
				));
				$settings_options->add_field(array(
					'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_items_per_page',
					'default' => 10,
					'type' => 'text',
					'attributes' => array(
						'type' => 'number',
					),
				));
				$settings_options->add_field(array(
					'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_pagination',
					'default' => 'wp-default',
					'type' => 'select',
					'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
						'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions-pro'),
						'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions-pro'),
						'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions-pro'),
						/*
						'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions-pro'),
						*/
					)),
				));
				$settings_options->add_field(array(
					'name' => esc_html__('Default Ordering', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_order',
					'default' => 'new',
					'type' => 'select',
					'options' => apply_filters('beeteam368_ordering_options', array(                    
						'new' => esc_html__('Newest Items', 'beeteam368-extensions-pro'),
						'old' => esc_html__('Oldest Items', 'beeteam368-extensions-pro'),
						'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
						'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
					)),
				));
				$settings_options->add_field(array(
					'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
					'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
					'id' => BEETEAM368_PREFIX . '_channel_playlists_tab_categories',
					'default' => 'on',
					'type' => 'select',
					'options' => array(
						'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
						'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
					),
				));
			}/*Playlists*/
			
			$settings_options->add_field(array(
                'name' => esc_html__('Display "Posts" Item (Pro)', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show "Posts" item on Tab.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_item',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));
			
			/*Posts*/
			$settings_options->add_field(array(
                'name' => esc_html__('Layout', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_layout',
                'default' => '',
                'type' => 'radio_image',
                'images_path' => get_template_directory_uri(),
                'options' => apply_filters('beeteam368_register_layouts_plugin_settings_name', array(
                    '' => esc_html__('Theme Options', 'beeteam368-extensions-pro'),
                )),
                'images' => apply_filters('beeteam368_register_layouts_plugin_settings_image', array(
                    '' => '/inc/theme-options/images/archive-to.png',
                )),
                'desc' => esc_html__('Change display layout for posts. Select "Theme Options" to use settings in Theme Options > Blog Settings.', 'beeteam368-extensions-pro'),				
            ));
            $settings_options->add_field(array(
                'name' => esc_html__('Items Per Page', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Number of items to show per page. Defaults to: 10', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_items_per_page',
                'default' => 10,
                'type' => 'text',
                'attributes' => array(
                    'type' => 'number',
                ),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Pagination', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Choose type of navigation. For WP PageNavi, you will need to install WP PageNavi plugin.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_pagination',
                'default' => 'wp-default',
                'type' => 'select',
                'options' => apply_filters('beeteam368_register_pagination_plugin_settings', array(
                    'wp-default' => esc_html__('WordPress Default', 'beeteam368-extensions-pro'),
                    'loadmore-btn' => esc_html__('Load More Button (Ajax)', 'beeteam368-extensions-pro'),
                    'infinite-scroll' => esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions-pro'),
                    /*
                    'pagenavi_plugin'  	=> esc_html__('WP PageNavi (Plugin)', 'beeteam368-extensions-pro'),
                    */
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Default Ordering', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Default display order for posts.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_order',
                'default' => 'new',
                'type' => 'select',
                'options' => apply_filters('beeteam368_ordering_options', array(                    
                    'new' => esc_html__('Newest Items', 'beeteam368-extensions-pro'),
                    'old' => esc_html__('Oldest Items', 'beeteam368-extensions-pro'),
					'title_a_z' => esc_html__('Alphabetical (A-Z)', 'beeteam368-extensions-pro'),
					'title_z_a' => esc_html__('Alphabetical (Z-A)', 'beeteam368-extensions-pro'),
                )),
            ));
			$settings_options->add_field(array(
                'name' => esc_html__('Display Categories', 'beeteam368-extensions-pro'),
                'desc' => esc_html__('Hide or show categories on post list.', 'beeteam368-extensions-pro'),
                'id' => BEETEAM368_PREFIX . '_channel_posts_tab_categories',
                'default' => 'on',
                'type' => 'select',
                'options' => array(
                    'on' => esc_html__('YES', 'beeteam368-extensions-pro'),
                    'off' => esc_html__('NO', 'beeteam368-extensions-pro'),
                ),
            ));/*Posts*/
		}

        function videos_side_menu($beeteam368_header_style)
        {
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_videos_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'videos'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_videos_tab_name', 'videos'))));?>" class="ctrl-show-hidden-elm your-video-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-video"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Videos', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'your_videos_page'));?>" data-redirect="your_videos_page" data-note="<?php echo esc_attr__('Sign in to see your videos.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm your-video-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-video"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Videos', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}
        }


		function audios_side_menu($beeteam368_header_style)
        {			
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_audios_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'audios'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_audios_tab_name', 'audios'))));?>" class="ctrl-show-hidden-elm your-audio-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-music"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Audios', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'your_audios_page'));?>" data-redirect="your_audios_page" data-note="<?php echo esc_attr__('Sign in to see your audios.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm your-audio-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-music"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Audios', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}
        }
		
		function playlists_side_menu($beeteam368_header_style)
        {			
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_playlists_item', '_channel_settings', 'on') === 'off' || $this->playlist_opt_ste() === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'playlists'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_playlists_tab_name', 'playlists'))));?>" class="ctrl-show-hidden-elm your-playlist-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-list-ul"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Playlists', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'your_playlists_page'));?>" data-redirect="your_playlists_page" data-note="<?php echo esc_attr__('Sign in to see your playlists.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm your-playlist-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-list-ul"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Playlists', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}
        }
		
		function posts_side_menu($beeteam368_header_style)
        {			
			if(beeteam368_get_option('_channel', '_theme_settings', 'on') === 'off' || beeteam368_get_option('_channel_posts_item', '_channel_settings', 'on') === 'off'){
				return;
			}
			
			if(is_user_logged_in()){
				$current_user = wp_get_current_user();
                $user_id = $current_user->ID;
				
				$active_class = '';
				$channel_page = beeteam368_get_option('_channel_page', '_channel_settings', '');
				if(is_numeric($channel_page) && $channel_page >= 0 && is_page($channel_page) && get_query_var('channel-id') == $user_id && get_query_var('channel-tab') == 'posts'){
					$active_class = 'side-active';
				}
            ?>
                <a href="<?php echo esc_url(beeteam368_channel_front_end::get_channel_url($user_id, array('channel-tab' => apply_filters('beeteam368_channel_your_posts_tab_name', 'posts'))));?>" class="ctrl-show-hidden-elm your-post-items flex-row-control flex-vertical-middle <?php echo esc_attr($active_class);?>">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-blog"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Posts', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}else{
			?>
                <a href="<?php echo esc_url(apply_filters('beeteam368_register_login_url', '#', 'your_posts_page'));?>" data-redirect="your_posts_page" data-note="<?php echo esc_attr__('Sign in to see your posts.', 'beeteam368-extensions-pro')?>" class="ctrl-show-hidden-elm your-post-items flex-row-control flex-vertical-middle reg-log-popup-control">
                    <span class="layer-show">
                        <span class="beeteam368-icon-item">
                            <i class="fas fa-blog"></i>
                        </span>
                    </span>
    
                    <span class="layer-hidden">
                        <span class="nav-font category-menu"><?php echo esc_html__('Your Posts', 'beeteam368-extensions-pro') ?></span>
                    </span>
                </a>
            <?php
			}
        }
		
		function playlist_submit_css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values) && $this->playlist_opt_ste() === 'on') {
                $values[] = array('beeteam368-playlist-submit', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/playlist-submit.css', []);
            }
            return $values;
        }
		
		function playlist_submit_js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values) && $this->playlist_opt_ste() === 'on') {
                $values[] = array('beeteam368-playlist-submit', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/user-submit-post/assets/playlist-submit.js', [], true);
            }
            return $values;
        }
		
		function localize_script($define_js_object){
            if(is_array($define_js_object) && $this->playlist_opt_ste() === 'on'){                
				$define_js_object['playlist_error_enter_title'] = esc_html__( 'Please enter the title of the playlist.', 'beeteam368-extensions-pro');
            }

            return $define_js_object;
        }

    }
}

global $beeteam368_user_submit_post_front_end;
$beeteam368_user_submit_post_front_end = new beeteam368_user_submit_post_front_end();

require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/user-submit-post/post-submit.php';
require BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/user-submit-post/edit-post.php';
<?php
if(!class_exists('beeteam368_mycred_video_viewing_class')){
	class beeteam368_mycred_video_viewing_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_viewing_video_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('%plural% for Viewing Video', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'beeteam368_mycred_add_video_references'));
			
			add_action('wp_ajax_beeteam368_update_mycred_points', array($this, 'update_mycred_points'));
			add_action('wp_ajax_nopriv_beeteam368_update_mycred_points', array($this, 'update_mycred_points'));	
		}
		
		function update_mycred_points(){
			
			if(!is_user_logged_in()){
				wp_send_json(array('error_login'));
				return;
				die();	
			}			
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';			
			if (!beeteam368_ajax_verify_nonce($security, true)){
				wp_send_json(array('error_nonce'));
				return;
				die();			
			}
			
			if(!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){
				wp_send_json(array('error_post_id'));
				return;
				die();
			}
			
			$current_user_id = get_current_user_id();
			$author_id = get_post_field('post_author', $_POST['post_id']);
			
			if(!empty($author_id) && is_numeric($author_id) && (int)$author_id !== (int)$current_user_id){
				do_action('beeteam368_video_viewing_action', $current_user_id, $_POST['post_id']);
				wp_send_json(array('update_points'));
				return;
				die();
			}
			
			wp_send_json(array('nothing'));
			return;
			die();
			
		}
		
		public function beeteam368_mycred_add_video_references($references) {
			$references['beeteam368_viewing_video_hook'] = esc_html__('[VidMov] [Member] for Viewing Videos', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_video_viewing_action',  array($this, 'beeteam368_video_viewing_update_action'), 10, 2);
			add_action('beeteam368_trigger_real_times_media', array($this, 'adjust_js'), 10, 2);
		}
		
		public function beeteam368_video_viewing_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_video_viewing_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
		public function adjust_js($rnd_id, $params){
			
			if (!is_user_logged_in()){
				return;
			}
			
			if(!empty($this->prefs['award_logic']) && is_numeric($this->prefs['award_logic'])){
			?>
                var mycred_real_percent_<?php echo $rnd_id;?> = 'on';
                
                jQuery(document).on('beeteam368PlayerRealTimess<?php echo $rnd_id;?>', function(e, player_id, fN_params, video_current_time, video_duration, real_percent){
                    
                    if(real_percent >= <?php echo ((int)$this->prefs['award_logic']) * 0.9;?> && mycred_real_percent_<?php echo $rnd_id;?> === 'on'){
                    	
                        mycred_real_percent_<?php echo $rnd_id;?> = 'off';
                                                
                        var data = {
                            'action': 'beeteam368_update_mycred_points',
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
		
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
            
            <label class="subheader"><?php esc_html_e('Award Logic', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
                
					<div class="h2">
                    	<input type="number" min="0" max="100" name="<?php echo esc_attr($this->field_name('award_logic')); ?>" id="<?php echo esc_attr($this->field_id('award_logic'));?>" value="<?php echo esc_attr($prefs['award_logic']);?>" class="long" />
                    </div>
                    
                    <span class="description">
						<?php esc_html_e('If you set to -1: As soon as video/audio starts playing / If you set it to greater than 0: calculated in percent ( Length of video/audio ).', 'beeteam368-extensions-pro');?>
                     </span>
                     
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
			$new_data['award_logic'] = ( !empty( $data['award_logic'] ) ) ? sanitize_text_field( $data['award_logic'] ) : $this->defaults['award_logic'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_creator_video_viewing_class')){
	class beeteam368_mycred_creator_video_viewing_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_creator_viewing_video_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when others watch your videos.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
			
			add_action('wp_ajax_beeteam368_creator_update_mycred_points', array($this, 'update_mycred_points'));
			add_action('wp_ajax_nopriv_beeteam368_creator_update_mycred_points', array($this, 'update_mycred_points'));	
		}
		
		function update_mycred_points(){
			
			if(!is_user_logged_in()){
				wp_send_json(array('error_login'));
				return;
				die();	
			}			
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';			
			if (!beeteam368_ajax_verify_nonce($security, true)){
				wp_send_json(array('error_nonce'));
				return;
				die();			
			}
			
			if(!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){
				wp_send_json(array('error_post_id'));
				return;
				die();
			}
			
			$current_user_id = get_current_user_id();
			$author_id = get_post_field('post_author', $_POST['post_id']);
			
			if(!empty($author_id) && is_numeric($author_id) && (int)$author_id !== (int)$current_user_id){
				do_action('beeteam368_creator_video_viewing_action', $author_id, $_POST['post_id']);
				wp_send_json(array('update_points'));
				return;
				die();
			}
			
			wp_send_json(array('nothing'));
			return;
			die();
			
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_creator_viewing_video_hook'] = esc_html__('[VidMov] [Creator] for Viewing Videos', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_creator_video_viewing_action',  array($this, 'beeteam368_creator_video_viewing_update_action'), 10, 2);
			add_action('beeteam368_trigger_real_times_media', array($this, 'adjust_js'), 10, 2);
		}
		
		public function beeteam368_creator_video_viewing_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_creator_video_viewing_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
		public function adjust_js($rnd_id, $params){
			
			if (!is_user_logged_in()){
				return;
			}
			
			if(!empty($this->prefs['award_logic']) && is_numeric($this->prefs['award_logic'])){
			?>
                var mycred_creator_real_percent_<?php echo $rnd_id;?> = 'on';
                
                jQuery(document).on('beeteam368PlayerRealTimess<?php echo $rnd_id;?>', function(e, player_id, fN_params, video_current_time, video_duration, real_percent){
                    
                    if(real_percent >= <?php echo ((int)$this->prefs['award_logic']) * 0.9;?> && mycred_creator_real_percent_<?php echo $rnd_id;?> === 'on'){
                    	
                        mycred_creator_real_percent_<?php echo $rnd_id;?> = 'off';
                                                
                        var data = {
                            'action': 'beeteam368_creator_update_mycred_points',
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
		
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
            
            <label class="subheader"><?php esc_html_e('Award Logic', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
                
					<div class="h2">
                    	<input type="number" min="0" max="100" name="<?php echo esc_attr($this->field_name('award_logic')); ?>" id="<?php echo esc_attr($this->field_id('award_logic'));?>" value="<?php echo esc_attr($prefs['award_logic']);?>" class="long" />
                    </div>
                    
                    <span class="description">
						<?php esc_html_e('If you set to -1: As soon as video/audio starts playing / If you set it to greater than 0: calculated in percent ( Length of video/audio ).', 'beeteam368-extensions-pro');?>
                     </span>
                     
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
			$new_data['award_logic'] = ( !empty( $data['award_logic'] ) ) ? sanitize_text_field( $data['award_logic'] ) : $this->defaults['award_logic'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_creator_video_viewing_nl_class')){
	class beeteam368_mycred_creator_video_viewing_nl_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_creator_viewing_nl_video_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when others watch your videos.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
			
			add_action('wp_ajax_beeteam368_creator_update_mycred_nl_points', array($this, 'update_mycred_points'));
			add_action('wp_ajax_nopriv_beeteam368_creator_update_mycred_nl_points', array($this, 'update_mycred_points'));	
		}
		
		function update_mycred_points(){
			
			if(is_user_logged_in()){
				wp_send_json(array('Logged_in_does_not_count'));
				return;
				die();	
			}			
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';			
			if (!beeteam368_ajax_verify_nonce($security, false)){
				wp_send_json(array('error_nonce'));
				return;
				die();			
			}
			
			if(!isset($_POST['post_id']) || !is_numeric($_POST['post_id'])){
				wp_send_json(array('error_post_id'));
				return;
				die();
			}

			$author_id = get_post_field('post_author', $_POST['post_id']);
			
			if(!empty($author_id) && is_numeric($author_id)){
				do_action('beeteam368_creator_video_viewing_nl_action', $author_id, $_POST['post_id']);
				wp_send_json(array('update_points'));
				return;
				die();
			}
			
			wp_send_json(array('nothing'));
			return;
			die();
			
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_creator_viewing_nl_video_hook'] = esc_html__('[VidMov] [Creator] [Not logged in] for Viewing Videos', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_creator_video_viewing_nl_action',  array($this, 'beeteam368_creator_video_viewing_nl_update_action'), 10, 2);
			add_action('beeteam368_trigger_real_times_media', array($this, 'adjust_js'), 10, 2);
		}
		
		public function beeteam368_creator_video_viewing_nl_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_creator_video_viewing_nl_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
		public function adjust_js($rnd_id, $params){
			
			if (is_user_logged_in()){
				return;
			}
			
			if(!empty($this->prefs['award_logic']) && is_numeric($this->prefs['award_logic'])){
			?>
                var mycred_creator_nl_real_percent_<?php echo $rnd_id;?> = 'on';
                
                jQuery(document).on('beeteam368PlayerRealTimess<?php echo $rnd_id;?>', function(e, player_id, fN_params, video_current_time, video_duration, real_percent){
                    
                    if(real_percent >= <?php echo ((int)$this->prefs['award_logic']) * 0.9;?> && mycred_creator_nl_real_percent_<?php echo $rnd_id;?> === 'on'){
                    	
                        mycred_creator_nl_real_percent_<?php echo $rnd_id;?> = 'off';
                                                
                        var data = {
                            'action': 'beeteam368_creator_update_mycred_nl_points',
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
		
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
            
            <label class="subheader"><?php esc_html_e('Award Logic', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
                
					<div class="h2">
                    	<input type="number" min="0" max="100" name="<?php echo esc_attr($this->field_name('award_logic')); ?>" id="<?php echo esc_attr($this->field_id('award_logic'));?>" value="<?php echo esc_attr($prefs['award_logic']);?>" class="long" />
                    </div>
                    
                    <span class="description">
						<?php esc_html_e('If you set to -1: As soon as video/audio starts playing / If you set it to greater than 0: calculated in percent ( Length of video/audio ).', 'beeteam368-extensions-pro');?>
                     </span>
                     
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
			$new_data['award_logic'] = ( !empty( $data['award_logic'] ) ) ? sanitize_text_field( $data['award_logic'] ) : $this->defaults['award_logic'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_user_reaction_minus_class')){
	class beeteam368_mycred_user_reaction_minus_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_mycred_user_reaction_minus_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when you un-reacts to any post.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_creator_viewing_nl_video_hook'] = esc_html__('[VidMov] [Members] When Unreacted', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_myCred_reaction_user_minus_action',  array($this, 'beeteam368_mycred_user_reaction_minus_update_action'), 10, 2);
		}
		
		public function beeteam368_mycred_user_reaction_minus_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_mycred_user_reaction_minus_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
				
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_author_reaction_minus_class')){
	class beeteam368_mycred_author_reaction_minus_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_mycred_author_reaction_minus_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when someone un-reacts to your post.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_author_viewing_nl_video_hook'] = esc_html__('[VidMov] [Creator] When Unreacted', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_myCred_reaction_author_minus_action',  array($this, 'beeteam368_mycred_author_reaction_minus_update_action'), 10, 2);
		}
		
		public function beeteam368_mycred_author_reaction_minus_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_mycred_author_reaction_minus_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
				
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_user_reaction_plus_class')){
	class beeteam368_mycred_user_reaction_plus_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_mycred_user_reaction_plus_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when you react to any post.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_creator_viewing_nl_video_hook'] = esc_html__('[VidMov] [Members] When Reacted', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_myCred_reaction_user_plus_action',  array($this, 'beeteam368_mycred_user_reaction_plus_update_action'), 10, 2);
		}
		
		public function beeteam368_mycred_user_reaction_plus_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_mycred_user_reaction_plus_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
				
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
	
			return $new_data;
		}
	}
}

if(!class_exists('beeteam368_mycred_author_reaction_plus_class')){
	class beeteam368_mycred_author_reaction_plus_class extends myCRED_Hook
	{
		function __construct($hook_prefs, $type = 'mycred_default'){
			parent::__construct( array(
				'id'       => 'beeteam368_mycred_author_reaction_plus_hook',
				'defaults' => array(
					'creds'   => 1,
					'log'     => esc_html__('Bonus %plural% from the system when someone reacts to your post.', 'beeteam368-extensions-pro'),
					'award_logic' => 1
				)
			), $hook_prefs, $type);
			
			add_filter('mycred_all_references',  array($this, 'mycred_add_references'));
		}
		
		public function mycred_add_references($references) {
			$references['beeteam368_author_viewing_nl_video_hook'] = esc_html__('[VidMov] [Creator] When Reacted', 'beeteam368-extensions-pro');
			return $references;
		
		}
		
		public function run() {
			add_action('beeteam368_myCred_reaction_author_plus_action',  array($this, 'beeteam368_mycred_author_reaction_plus_update_action'), 10, 2);
		}
		
		public function beeteam368_mycred_author_reaction_plus_update_action($user_id, $post_id){
			
			if($this->core->exclude_user($user_id)){
				return;
			}
			
			$this->core->add_creds(
				'beeteam368_mycred_author_reaction_plus_update_action',
				$user_id,
				$this->prefs['creds'],
				$this->prefs['log'],
				'',
				'',
				$this->mycred_type
			);
		}
		
				
		public function preferences(){			
			$prefs = $this->prefs; 
		?>			
			<label class="subheader"><?php echo esc_html($this->core->plural());?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('creds'));?>" id="<?php echo $this->field_id('creds'); ?>" value="<?php echo esc_attr($prefs['creds']);?>" size="8"/></div>
				</li>
			</ol>
			
			<label class="subheader"><?php esc_html_e('Log template', 'beeteam368-extensions-pro');?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo esc_attr($this->field_name('log'));?>" id="<?php echo esc_attr($this->field_id('log')); ?>" value="<?php echo esc_attr($prefs['log']);?>" class="long"/></div>
				</li>
			</ol>
		<?php
		}

		public function sanitise_preferences( $data ) {
			
			$new_data = $data;

			$new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
			$new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];
	
			return $new_data;
		}
	}
}

if (!class_exists('beeteam368_mycred_hooks')) {
    class beeteam368_mycred_hooks
    {
		public function __construct()
        {
			add_filter('mycred_setup_hooks', array($this, 'mycred_video_hook'));
			add_filter('mycred_setup_hooks', array($this, 'mycred_creator_video_hook'));
			add_filter('mycred_setup_hooks', array($this, 'mycred_creator_nl_video_hook'));
			
			add_filter('mycred_setup_hooks', array($this, 'mycred_user_reaction_minus'));
			add_filter('mycred_setup_hooks', array($this, 'mycred_author_reaction_minus'));
			
			add_filter('mycred_setup_hooks', array($this, 'mycred_user_reaction_plus'));
			add_filter('mycred_setup_hooks', array($this, 'mycred_author_reaction_plus'));
		}
		
		function mycred_video_hook($installed){
			$installed['beeteam368_viewing_video_hook'] = array(
				'title' => esc_html__('[VidMov] [Member] Earn %plural% for watching videos/audios', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives members a certain amount of %plural% when they watch videos/audios.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_video_viewing_class')
			);
			return $installed;
		}
		
		function mycred_creator_video_hook($installed){
			$installed['beeteam368_creator_viewing_video_hook'] = array(
				'title' => esc_html__('[VidMov] [Creator] Earn %plural% for watching videos/audios', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives creators a certain amount of %plural% when someone watches their videos/audios.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_creator_video_viewing_class')
			);
			return $installed;
		}
		
		function mycred_creator_nl_video_hook($installed){
			$installed['beeteam368_creator_viewing_nl_video_hook'] = array(
				'title' => esc_html__('[VidMov] [Creator] [Not logged in] Earn %plural% for watching videos/audios', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives creators a certain amount of %plural% when someone watches their videos/audios.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_creator_video_viewing_nl_class')
			);
			return $installed;
		}
		
		function mycred_user_reaction_minus($installed){
			$installed['beeteam368_mycred_user_reaction_minus_hook'] = array(
				'title' => esc_html__('[VidMov] [Members] Earn %plural% for un-reacts to any post', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives members a certain amount of %plural% when they un-reacts to any post.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_user_reaction_minus_class')
			);
			return $installed;
		}
		
		function mycred_author_reaction_minus($installed){
			$installed['beeteam368_mycred_author_reaction_minus_hook'] = array(
				'title' => esc_html__('[VidMov] [Creators] Earn %plural% for un-reacts to any post', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives creators a certain amount of %plural% when someone un-reacts to their post.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_author_reaction_minus_class')
			);
			return $installed;
		}
		
		function mycred_user_reaction_plus($installed){
			$installed['beeteam368_mycred_user_reaction_plus_hook'] = array(
				'title' => esc_html__('[VidMov] [Members] Earn %plural% for reacts to any post', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives members a certain amount of %plural% when they reacts to any post.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_user_reaction_plus_class')
			);
			return $installed;
		}
		
		function mycred_author_reaction_plus($installed){
			$installed['beeteam368_mycred_author_reaction_plus_hook'] = array(
				'title' => esc_html__('[VidMov] [Creators] Earn %plural% for reacts to any post', 'beeteam368-extensions-pro'),
				'description' => esc_html__('This hook gives creators a certain amount of %plural% when someone reacts to their post.', 'beeteam368-extensions-pro'),
				'callback' => array('beeteam368_mycred_author_reaction_plus_class')
			);
			return $installed;
		}
	}
}

global $beeteam368_mycred_hooks;
$beeteam368_mycred_hooks = new beeteam368_mycred_hooks();
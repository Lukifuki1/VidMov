<?php
if(!function_exists('beeteam368_before_content_import')){
	function beeteam368_before_content_import($selected_import){
		
		update_option('elementor_disable_color_schemes', 'yes');			
		update_option('elementor_disable_typography_schemes', 'yes');
		
		if( 'VidMov - Main Demo' === $selected_import['import_file_name'] ) {
			$beeteam368_theme_settings = 'a:43:{s:18:"beeteam368_channel";s:2:"on";s:19:"beeteam368_playlist";s:2:"on";s:17:"beeteam368_series";s:2:"on";s:22:"beeteam368_multi_links";s:2:"on";s:23:"beeteam368_video_report";s:2:"on";s:22:"beeteam368_video_actor";s:2:"on";s:25:"beeteam368_video_director";s:2:"on";s:22:"beeteam368_watch_later";s:2:"on";s:33:"beeteam368_social_network_account";s:2:"on";s:17:"beeteam368_review";s:2:"on";s:25:"beeteam368_review_display";s:6:"always";s:20:"beeteam368_wp_nonces";s:2:"on";s:28:"beeteam368_custom_font_count";s:1:"2";s:23:"beeteam368_like_dislike";s:2:"on";s:15:"beeteam368_like";s:2:"on";s:18:"beeteam368_dislike";s:2:"on";s:23:"beeteam368_squint_tears";s:2:"on";s:14:"beeteam368_cry";s:2:"on";s:24:"beeteam368_views_counter";s:2:"on";s:23:"beeteam368_subscription";s:2:"on";s:23:"beeteam368_notification";s:2:"on";s:30:"beeteam368_member_verification";s:2:"on";s:24:"beeteam368_virtual_gifts";s:2:"on";s:18:"beeteam368_buycred";s:2:"on";s:23:"beeteam368_buycred_page";s:2:"15";s:30:"beeteam368_mycred_sell_content";s:2:"on";s:18:"beeteam368_history";s:2:"on";s:25:"beeteam368_youtube_import";s:2:"on";s:23:"beeteam368_vimeo_import";s:2:"on";s:27:"beeteam368_user_submit_post";s:2:"on";s:28:"beeteam368_video_advertising";s:2:"on";s:22:"beeteam368_tmdb_import";s:2:"on";s:20:"beeteam368_timestamp";s:2:"on";s:25:"beeteam368_login_register";s:2:"on";s:20:"beeteam368_mega_menu";s:2:"on";s:22:"beeteam368_live_search";s:2:"on";s:19:"beeteam368_trending";s:2:"on";s:24:"beeteam368_trending_page";s:2:"17";s:25:"beeteam368_trending_based";s:3:"nov";s:24:"beeteam368_trending_time";s:4:"year";s:30:"beeteam368_trending_categories";s:2:"on";s:15:"beeteam368_cast";s:2:"on";s:26:"beeteam368_trending_layout";s:6:"alyssa";}';
			update_option('beeteam368_theme_settings', unserialize($beeteam368_theme_settings));
			
			$beeteam368_video_settings = 'a:21:{s:21:"beeteam368_video_slug";s:5:"video";s:30:"beeteam368_video_category_base";s:14:"video-category";s:39:"beeteam368_video_archive_items_per_page";s:2:"16";s:39:"beeteam368_video_single_player_position";s:7:"classic";s:37:"beeteam368_video_single_apply_element";s:2:"no";s:33:"beeteam368_video_collapse_content";s:2:"on";s:29:"beeteam368_mtb_turn_off_light";s:2:"on";s:29:"beeteam368_mtb_previous_video";s:2:"on";s:25:"beeteam368_mtb_next_video";s:2:"on";s:30:"beeteam368_mtb_add_to_playlist";s:2:"on";s:21:"beeteam368_mtb_report";s:2:"on";s:26:"beeteam368_mtb_watch_later";s:2:"on";s:20:"beeteam368_mtb_share";s:2:"on";s:24:"beeteam368_mtb_auto_next";s:2:"on";s:29:"beeteam368_mtb_auto_next_mode";s:3:"off";s:25:"beeteam368_video_autoplay";s:3:"off";s:26:"beeteam368_video_load_opti";s:2:"on";s:24:"beeteam368_video_preview";s:2:"on";s:32:"beeteam368_video_player_language";s:2:"en";s:20:"beeteam368_video_ads";s:3:"off";s:35:"beeteam368_video_archive_pagination";s:15:"pagenavi_plugin";}';
			update_option('beeteam368_video_settings', unserialize($beeteam368_video_settings));
			
			$beeteam368_audio_settings  = 'a:15:{s:21:"beeteam368_audio_slug";s:5:"audio";s:30:"beeteam368_audio_category_base";s:14:"audio-category";s:39:"beeteam368_audio_archive_items_per_page";s:2:"16";s:39:"beeteam368_audio_single_player_position";s:7:"classic";s:37:"beeteam368_audio_single_apply_element";s:2:"no";s:33:"beeteam368_audio_collapse_content";s:2:"on";s:29:"beeteam368_mtb_previous_audio";s:2:"on";s:25:"beeteam368_mtb_next_audio";s:2:"on";s:30:"beeteam368_mtb_add_to_playlist";s:2:"on";s:21:"beeteam368_mtb_report";s:2:"on";s:26:"beeteam368_mtb_watch_later";s:2:"on";s:20:"beeteam368_mtb_share";s:2:"on";s:32:"beeteam368_audio_player_language";s:2:"en";s:35:"beeteam368_audio_archive_pagination";s:15:"pagenavi_plugin";s:31:"beeteam368_audio_archive_layout";s:10:"marguerite";}';
			update_option('beeteam368_audio_settings', unserialize($beeteam368_audio_settings));
			
			$beeteam368_channel_settings = 'a:70:{s:23:"beeteam368_channel_page";s:2:"11";s:38:"beeteam368_replace_author_with_channel";s:2:"on";s:35:"beeteam368_channel_watch_later_item";s:2:"on";s:29:"beeteam368_channel_rated_item";s:2:"on";s:31:"beeteam368_channel_reacted_item";s:2:"on";s:40:"beeteam368_channel_transfer_history_item";s:2:"on";s:31:"beeteam368_channel_history_item";s:2:"on";s:30:"beeteam368_channel_videos_item";s:2:"on";s:30:"beeteam368_channel_audios_item";s:2:"on";s:33:"beeteam368_channel_playlists_item";s:2:"on";s:29:"beeteam368_channel_posts_item";s:2:"on";s:37:"beeteam368_channel_subscriptions_item";s:2:"on";s:37:"beeteam368_channel_notifications_item";s:2:"on";s:39:"beeteam368_channel_order_side_menu_item";a:11:{i:0;s:7:"history";i:1;s:13:"notifications";i:2;s:13:"subscriptions";i:3;s:6:"videos";i:4;s:6:"audios";i:5;s:9:"playlists";i:6;s:5:"posts";i:7;s:16:"transfer_history";i:8;s:11:"watch_later";i:9;s:5:"rated";i:10;s:7:"reacted";}s:33:"beeteam368_channel_about_tab_item";s:2:"on";s:38:"beeteam368_channel_discussion_tab_item";s:2:"on";s:39:"beeteam368_channel_watch_later_tab_item";s:2:"on";s:49:"beeteam368_channel_watch_later_tab_items_per_page";s:1:"9";s:45:"beeteam368_channel_watch_later_tab_pagination";s:15:"pagenavi_plugin";s:40:"beeteam368_channel_watch_later_tab_order";s:3:"new";s:45:"beeteam368_channel_watch_later_tab_categories";s:2:"on";s:33:"beeteam368_channel_rated_tab_item";s:2:"on";s:43:"beeteam368_channel_rated_tab_items_per_page";s:1:"9";s:39:"beeteam368_channel_rated_tab_pagination";s:15:"pagenavi_plugin";s:34:"beeteam368_channel_rated_tab_order";s:3:"new";s:39:"beeteam368_channel_rated_tab_categories";s:2:"on";s:35:"beeteam368_channel_reacted_tab_item";s:2:"on";s:45:"beeteam368_channel_reacted_tab_items_per_page";s:1:"9";s:41:"beeteam368_channel_reacted_tab_pagination";s:15:"pagenavi_plugin";s:36:"beeteam368_channel_reacted_tab_order";s:3:"new";s:41:"beeteam368_channel_reacted_tab_categories";s:2:"on";s:44:"beeteam368_channel_transfer_history_tab_item";s:2:"on";s:35:"beeteam368_channel_history_tab_item";s:2:"on";s:45:"beeteam368_channel_history_tab_items_per_page";s:1:"9";s:41:"beeteam368_channel_history_tab_pagination";s:15:"pagenavi_plugin";s:36:"beeteam368_channel_history_tab_order";s:3:"new";s:41:"beeteam368_channel_history_tab_categories";s:2:"on";s:34:"beeteam368_channel_videos_tab_item";s:2:"on";s:44:"beeteam368_channel_videos_tab_items_per_page";s:2:"12";s:40:"beeteam368_channel_videos_tab_pagination";s:12:"loadmore-btn";s:35:"beeteam368_channel_videos_tab_order";s:3:"new";s:40:"beeteam368_channel_videos_tab_categories";s:2:"on";s:34:"beeteam368_channel_audios_tab_item";s:2:"on";s:44:"beeteam368_channel_audios_tab_items_per_page";s:1:"8";s:40:"beeteam368_channel_audios_tab_pagination";s:15:"infinite-scroll";s:35:"beeteam368_channel_audios_tab_order";s:3:"new";s:40:"beeteam368_channel_audios_tab_categories";s:2:"on";s:37:"beeteam368_channel_playlists_tab_item";s:2:"on";s:47:"beeteam368_channel_playlists_tab_items_per_page";s:1:"9";s:43:"beeteam368_channel_playlists_tab_pagination";s:12:"loadmore-btn";s:38:"beeteam368_channel_playlists_tab_order";s:3:"new";s:43:"beeteam368_channel_playlists_tab_categories";s:2:"on";s:33:"beeteam368_channel_posts_tab_item";s:2:"on";s:43:"beeteam368_channel_posts_tab_items_per_page";s:1:"9";s:39:"beeteam368_channel_posts_tab_pagination";s:15:"pagenavi_plugin";s:34:"beeteam368_channel_posts_tab_order";s:3:"new";s:39:"beeteam368_channel_posts_tab_categories";s:2:"on";s:41:"beeteam368_channel_subscriptions_tab_item";s:2:"on";s:51:"beeteam368_channel_subscriptions_tab_items_per_page";s:2:"12";s:47:"beeteam368_channel_subscriptions_tab_pagination";s:12:"loadmore-btn";s:41:"beeteam368_channel_notifications_tab_item";s:2:"on";s:51:"beeteam368_channel_notifications_tab_items_per_page";s:2:"12";s:47:"beeteam368_channel_notifications_tab_categories";s:2:"on";s:33:"beeteam368_channel_order_tab_item";a:13:{i:0;s:6:"videos";i:1;s:6:"audios";i:2;s:9:"playlists";i:3;s:5:"posts";i:4;s:16:"transfer_history";i:5;s:13:"subscriptions";i:6;s:11:"watch_later";i:7;s:13:"notifications";i:8;s:7:"history";i:9;s:5:"rated";i:10;s:7:"reacted";i:11;s:5:"about";i:12;s:10:"discussion";}s:36:"beeteam368_channel_videos_tab_layout";s:4:"lily";s:36:"beeteam368_channel_audios_tab_layout";s:10:"marguerite";s:22:"beeteam368_member_page";s:4:"1084";s:37:"beeteam368_member_page_items_per_page";s:2:"12";s:33:"beeteam368_member_page_pagination";s:12:"loadmore-btn";s:43:"beeteam368_channel_notifications_tab_layout";s:10:"marguerite";}';
			update_option('beeteam368_channel_settings', unserialize($beeteam368_channel_settings));
			
			$beeteam368_playlist_settings = 'a:8:{s:24:"beeteam368_playlist_slug";s:8:"playlist";s:33:"beeteam368_playlist_category_base";s:17:"playlist-category";s:42:"beeteam368_playlist_archive_items_per_page";s:2:"16";s:31:"beeteam368_playlist_video_order";s:8:"post__in";s:30:"beeteam368_playlist_video_sort";s:4:"DESC";s:41:"beeteam368_playlist_single_items_per_page";s:2:"10";s:37:"beeteam368_playlist_single_pagination";s:15:"pagenavi_plugin";s:38:"beeteam368_playlist_archive_pagination";s:15:"infinite-scroll";}';
			update_option('beeteam368_playlist_settings', unserialize($beeteam368_playlist_settings));
			
			$beeteam368_series_settings = 'a:8:{s:22:"beeteam368_series_slug";s:6:"series";s:31:"beeteam368_series_category_base";s:15:"series-category";s:40:"beeteam368_series_archive_items_per_page";s:2:"16";s:36:"beeteam368_series_archive_pagination";s:15:"infinite-scroll";s:29:"beeteam368_series_video_order";s:8:"post__in";s:28:"beeteam368_series_video_sort";s:4:"DESC";s:39:"beeteam368_series_single_items_per_page";s:2:"10";s:35:"beeteam368_series_single_pagination";s:15:"pagenavi_plugin";}';
			update_option('beeteam368_series_settings', unserialize($beeteam368_series_settings));
			
			$beeteam368_cast_settings = 'a:11:{s:20:"beeteam368_cast_slug";s:4:"cast";s:38:"beeteam368_cast_archive_items_per_page";s:2:"10";s:37:"beeteam368_cast_single_items_per_page";s:1:"9";s:27:"beeteam368_cast_media_order";s:3:"new";s:39:"beeteam368_cast_single_media_categories";s:2:"on";s:21:"beeteam368_cast_clone";a:2:{i:0;a:5:{s:15:"clone_post_type";s:4:"crew";s:10:"clone_slug";s:4:"crew";s:14:"clone_singular";s:4:"Crew";s:12:"clone_plural";s:5:"Crews";s:10:"clone_icon";s:28:"<i class="fas fa-users"></i>";}i:1;a:5:{s:15:"clone_post_type";s:6:"artist";s:10:"clone_slug";s:6:"artist";s:14:"clone_singular";s:6:"Artist";s:12:"clone_plural";s:7:"Artists";s:10:"clone_icon";s:30:"<i class="fab fa-napster"></i>";}}s:29:"beeteam368_cast_single_layout";s:10:"marguerite";s:33:"beeteam368_cast_single_pagination";s:12:"loadmore-btn";s:30:"beeteam368_cast_archive_layout";s:4:"rose";s:31:"beeteam368_cast_archive_sidebar";s:6:"hidden";s:34:"beeteam368_cast_archive_pagination";s:15:"pagenavi_plugin";}';
			update_option('beeteam368_cast_settings', unserialize($beeteam368_cast_settings));
			
			$beeteam368_user_submit_post_settings = 'a:9:{s:24:"beeteam368_submit_videos";s:2:"on";s:24:"beeteam368_submit_audios";s:2:"on";s:23:"beeteam368_submit_posts";s:2:"on";s:32:"beeteam368_submit_media_max_size";s:2:"10";s:41:"beeteam368_submit_featured_image_max_size";s:1:"5";s:33:"beeteam368_submit_post_moderation";s:3:"off";s:35:"beeteam368_submit_media_description";s:69:"Supports: *.mp4, *.m4v, *.webm, *.ogv. Maximum upload file size: 10mb";s:44:"beeteam368_submit_featured_image_description";s:68:"Supports: *.png, *.jpg, *.gif, *.jpeg. Maximum upload file size: 5mb";s:35:"beeteam368_submit_post_sell_content";s:2:"on";}';
			update_option('beeteam368_user_submit_post_settings', unserialize($beeteam368_user_submit_post_settings));
			
			$heateor_sss = 'a:74:{s:24:"horizontal_sharing_shape";s:5:"round";s:23:"horizontal_sharing_size";s:2:"35";s:24:"horizontal_sharing_width";s:2:"70";s:25:"horizontal_sharing_height";s:2:"35";s:24:"horizontal_border_radius";s:0:"";s:29:"horizontal_font_color_default";s:0:"";s:32:"horizontal_sharing_replace_color";s:4:"#fff";s:27:"horizontal_font_color_hover";s:0:"";s:38:"horizontal_sharing_replace_color_hover";s:4:"#fff";s:27:"horizontal_bg_color_default";s:0:"";s:25:"horizontal_bg_color_hover";s:0:"";s:31:"horizontal_border_width_default";s:0:"";s:31:"horizontal_border_color_default";s:0:"";s:29:"horizontal_border_width_hover";s:0:"";s:29:"horizontal_border_color_hover";s:0:"";s:22:"vertical_sharing_shape";s:6:"square";s:21:"vertical_sharing_size";s:2:"40";s:22:"vertical_sharing_width";s:2:"80";s:23:"vertical_sharing_height";s:2:"40";s:22:"vertical_border_radius";s:0:"";s:27:"vertical_font_color_default";s:0:"";s:30:"vertical_sharing_replace_color";s:4:"#fff";s:25:"vertical_font_color_hover";s:0:"";s:36:"vertical_sharing_replace_color_hover";s:4:"#fff";s:25:"vertical_bg_color_default";s:0:"";s:23:"vertical_bg_color_hover";s:0:"";s:29:"vertical_border_width_default";s:0:"";s:29:"vertical_border_color_default";s:0:"";s:27:"vertical_border_width_hover";s:0:"";s:27:"vertical_border_color_hover";s:0:"";s:10:"hor_enable";s:1:"1";s:21:"horizontal_target_url";s:7:"default";s:28:"horizontal_target_url_custom";s:0:"";s:5:"title";s:15:"Spread the love";s:18:"instagram_username";s:0:"";s:16:"youtube_username";s:0:"";s:15:"rutube_username";s:0:"";s:20:"comment_container_id";s:7:"respond";s:23:"horizontal_re_providers";a:8:{i:0;s:8:"facebook";i:1;s:7:"twitter";i:2;s:6:"reddit";i:3;s:8:"linkedin";i:4;s:9:"pinterest";i:5;s:4:"MeWe";i:6;s:3:"mix";i:7;s:8:"whatsapp";}s:21:"hor_sharing_alignment";s:4:"left";s:15:"horizontal_more";s:1:"1";s:19:"vertical_target_url";s:7:"default";s:26:"vertical_target_url_custom";s:0:"";s:27:"vertical_instagram_username";s:0:"";s:25:"vertical_youtube_username";s:0:"";s:24:"vertical_rutube_username";s:0:"";s:29:"vertical_comment_container_id";s:7:"respond";s:21:"vertical_re_providers";a:8:{i:0;s:8:"facebook";i:1;s:7:"twitter";i:2;s:6:"reddit";i:3;s:8:"linkedin";i:4;s:9:"pinterest";i:5;s:4:"MeWe";i:6;s:3:"mix";i:7;s:8:"whatsapp";}s:11:"vertical_bg";s:0:"";s:9:"alignment";s:4:"left";s:11:"left_offset";s:3:"-10";s:12:"right_offset";s:3:"-10";s:10:"top_offset";s:3:"100";s:13:"vertical_home";s:1:"1";s:13:"vertical_post";s:1:"1";s:13:"vertical_page";s:1:"1";s:13:"vertical_more";s:1:"1";s:19:"hide_mobile_sharing";s:1:"1";s:21:"vertical_screen_width";s:3:"783";s:21:"bottom_mobile_sharing";s:1:"1";s:23:"horizontal_screen_width";s:3:"783";s:23:"bottom_sharing_position";s:1:"0";s:24:"bottom_sharing_alignment";s:4:"left";s:29:"bottom_sharing_position_radio";s:10:"responsive";s:13:"footer_script";s:1:"1";s:14:"delete_options";s:1:"1";s:31:"share_count_cache_refresh_count";s:2:"10";s:30:"share_count_cache_refresh_unit";s:7:"minutes";s:18:"bitly_access_token";s:0:"";s:8:"language";s:5:"en_US";s:16:"twitter_username";s:0:"";s:15:"buffer_username";s:0:"";s:10:"amp_enable";s:1:"1";s:10:"custom_css";s:0:"";}';			
			update_option('heateor_sss', unserialize($heateor_sss));
			update_option('heateor_sss_gdpr_notification_read', 1);
			
			update_option('tml_ajax', 1);
			update_option('tml_auto_login', 1);			
			update_option('tml_dashboard_slug', 'main-profile');
			update_option('tml_login_slug', 'main-login');
			update_option('tml_login_type', 'default');
			update_option('tml_logout_slug', 'main-logout');
			update_option('tml_lostpassword_slug', 'main-lostpassword');
			update_option('tml_register_slug', 'main-register');
			update_option('tml_registration_type', 'default');
			update_option('tml_resetpass_slug', 'main-resetpass');
			update_option('tml_user_passwords', 1);
			
			/*
			update_option('mycred_addons_upgrade', 'done');			
			update_option('mycred_default_point_image', 7);
			update_option('mycred_key', 'aO9keV|hZeZjh/;q');			
			
			$mycred_pref_addons = 'a:2:{s:6:"active";a:12:{i:0;s:6:"badges";i:1;s:9:"buy-creds";i:2;s:10:"cash-creds";i:3;s:7:"banking";i:4;s:7:"coupons";i:5;s:13:"email-notices";i:6;s:7:"gateway";i:7;s:13:"notifications";i:8;s:5:"ranks";i:9;s:12:"sell-content";i:10;s:5:"stats";i:11;s:8:"transfer";}s:9:"installed";a:12:{s:6:"badges";a:8:{s:4:"name";s:6:"Badges";s:11:"description";s:68:"Give your users badges based on their interaction with your website.";s:9:"addon_url";s:42:"http://codex.mycred.me/chapter-iii/badges/";s:7:"version";s:3:"1.3";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:82:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/badges-addon.png";s:8:"requires";a:0:{}}s:9:"buy-creds";a:8:{s:4:"name";s:7:"buyCRED";s:11:"description";s:197:"The <strong>buy</strong>CRED Add-on allows your users to buy points using PayPal, Skrill (Moneybookers) or NETbilling. <strong>buy</strong>CRED can also let your users buy points for other members.";s:9:"addon_url";s:43:"http://codex.mycred.me/chapter-iii/buycred/";s:7:"version";s:3:"1.5";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:85:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/buy-creds-addon.png";s:8:"requires";a:0:{}}s:10:"cash-creds";a:8:{s:4:"name";s:8:"cashCRED";s:11:"description";s:0:"";s:9:"addon_url";s:45:"https://codex.mycred.me/chapter-iii/cashcred/";s:7:"version";s:3:"1.0";s:6:"author";s:19:"Gabriel S Merovingi";s:10:"author_url";s:25:"https://www.merovingi.com";s:10:"screenshot";s:83:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/banking-addon.png";s:8:"requires";a:0:{}}s:7:"banking";a:8:{s:4:"name";s:15:"Central Deposit";s:11:"description";s:76:"Setup recurring payouts or offer / charge interest on user account balances.";s:9:"addon_url";s:59:"https://codex.mycred.me/chapter-iii/central-deposit-add-on/";s:7:"version";s:3:"2.0";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:83:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/banking-addon.png";s:8:"requires";a:0:{}}s:7:"coupons";a:8:{s:4:"name";s:7:"Coupons";s:11:"description";s:99:"The coupons add-on allows you to create coupons that users can use to add points to their accounts.";s:9:"addon_url";s:43:"http://codex.mycred.me/chapter-iii/coupons/";s:7:"version";s:3:"1.4";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:83:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/coupons-addon.png";s:8:"requires";a:0:{}}s:13:"email-notices";a:8:{s:4:"name";s:19:"Email Notifications";s:11:"description";s:53:"Create email notices for any type of myCRED instance.";s:9:"addon_url";s:48:"http://codex.mycred.me/chapter-iii/email-notice/";s:7:"version";s:3:"1.4";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:95:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/email-notifications-addon.png";s:8:"requires";a:0:{}}s:7:"gateway";a:8:{s:4:"name";s:7:"Gateway";s:11:"description";s:205:"Let your users pay using their <strong>my</strong>CRED points balance. Supported Carts: WooCommerce, MarketPress and WP E-Commerce. Supported Event Bookings: Event Espresso and Events Manager (free & pro).";s:9:"addon_url";s:43:"http://codex.mycred.me/chapter-iii/gateway/";s:7:"version";s:3:"1.4";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:83:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/gateway-addon.png";s:8:"requires";a:0:{}}s:13:"notifications";a:9:{s:4:"name";s:13:"Notifications";s:11:"description";s:64:"Create pop-up notifications for when users gain or loose points.";s:9:"addon_url";s:49:"http://codex.mycred.me/chapter-iii/notifications/";s:7:"version";s:5:"1.1.2";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:7:"pro_url";s:50:"https://mycred.me/store/notifications-plus-add-on/";s:10:"screenshot";s:89:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/notifications-addon.png";s:8:"requires";a:0:{}}s:5:"ranks";a:8:{s:4:"name";s:5:"Ranks";s:11:"description";s:105:"Create ranks for users reaching a certain number of %_plural% with the option to add logos for each rank.";s:9:"addon_url";s:41:"http://codex.mycred.me/chapter-iii/ranks/";s:7:"version";s:3:"1.6";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:81:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/ranks-addon.png";s:8:"requires";a:0:{}}s:12:"sell-content";a:8:{s:4:"name";s:12:"Sell Content";s:11:"description";s:208:"This add-on allows you to sell posts, pages or any public post types on your website. You can either sell the entire content or using our shortcode, sell parts of your content allowing you to offer "teasers".";s:9:"addon_url";s:48:"http://codex.mycred.me/chapter-iii/sell-content/";s:7:"version";s:5:"2.0.1";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:88:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/sell-content-addon.png";s:8:"requires";a:1:{i:0;s:3:"log";}}s:5:"stats";a:7:{s:4:"name";s:10:"Statistics";s:11:"description";s:79:"Gives you access to your myCRED Statistics based on your users gains and loses.";s:9:"addon_url";s:46:"http://codex.mycred.me/chapter-iii/statistics/";s:7:"version";s:3:"2.0";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:10:"screenshot";s:86:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/statistics-addon.png";}s:8:"transfer";a:9:{s:4:"name";s:9:"Transfers";s:11:"description";s:137:"Allow your users to send or "donate" points to other members by either using the mycred_transfer shortcode or the myCRED Transfer widget.";s:9:"addon_url";s:45:"http://codex.mycred.me/chapter-iii/transfers/";s:7:"version";s:3:"1.6";s:6:"author";s:6:"myCred";s:10:"author_url";s:21:"https://www.mycred.me";s:7:"pro_url";s:38:"https://mycred.me/store/transfer-plus/";s:10:"screenshot";s:84:"https://vm.beeteam368.net/wp-content/plugins/mycred/assets/images/transfer-addon.png";s:8:"requires";a:0:{}}}}';
			update_option('mycred_pref_addons', unserialize($mycred_pref_addons));
			
			$mycred_pref_buycreds = 'a:3:{s:6:"active";a:2:{i:0;s:15:"paypal-standard";i:1;s:4:"bank";}s:13:"gateway_prefs";a:5:{s:15:"paypal-standard";a:6:{s:7:"sandbox";i:1;s:8:"currency";s:3:"USD";s:7:"account";s:32:"beeteam368-facilitator@gmail.com";s:9:"item_name";s:27:"Purchase of myCRED %plural%";s:8:"logo_url";s:0:"";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}s:6:"bitpay";a:12:{s:10:"api_secret";s:0:"";s:10:"api_public";s:0:"";s:8:"api_sign";s:0:"";s:9:"api_token";s:0:"";s:9:"api_label";s:0:"";s:7:"sandbox";i:1;s:8:"currency";s:3:"USD";s:9:"item_name";s:27:"Purchase of myCRED %plural%";s:8:"logo_url";s:0:"";s:5:"speed";s:4:"high";s:13:"notifications";s:1:"1";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}s:10:"netbilling";a:8:{s:7:"sandbox";i:1;s:7:"account";s:5:"admin";s:8:"currency";s:3:"USD";s:8:"site_tag";s:0:"";s:9:"cryptokey";s:14:"VaiLonEm121189";s:9:"item_name";s:27:"Purchase of myCRED %plural%";s:8:"logo_url";s:0:"";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}s:6:"skrill";a:10:{s:7:"sandbox";i:1;s:8:"currency";s:0:"";s:7:"account";s:0:"";s:4:"word";s:0:"";s:9:"item_name";s:27:"Purchase of myCRED %plural%";s:8:"logo_url";s:0:"";s:13:"email_receipt";i:1;s:13:"account_title";s:0:"";s:17:"confirmation_note";s:0:"";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}s:4:"bank";a:5:{s:5:"title";s:13:"Bank Transfer";s:8:"logo_url";s:0:"";s:7:"account";s:465:"<h5 style="text-align: center;margin-top: 30px">Copy the transaction number above, and enter it in the description before bank transfer to us.</h5>
<p style="text-align: center">Bank Name: <strong>France Bank</strong></p>
<p style="text-align: center">Account name: <strong>Nicolas</strong></p>
<p style="text-align: center">Account number: <strong>1234567890</strong></p>
<p style="text-align: center;margin-bottom: 0">Swift code: <strong>SWFTRFCV</strong></p>";s:8:"currency";s:3:"USD";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}}s:9:"installed";s:4:"bank";}';
			update_option('mycred_pref_buycreds', unserialize($mycred_pref_buycreds));
			
			$mycred_pref_cashcreds = 'a:3:{s:6:"active";a:1:{i:0;s:4:"bank";}s:13:"gateway_prefs";a:1:{s:4:"bank";a:5:{s:16:"additional_notes";s:0:"";s:14:"minimum_amount";s:2:"50";s:14:"maximum_amount";s:4:"1000";s:8:"currency";s:3:"USD";s:8:"exchange";a:1:{s:14:"mycred_default";s:1:"1";}}}s:9:"installed";s:4:"bank";}';
			update_option('mycred_pref_cashcreds', unserialize($mycred_pref_cashcreds));
			
			$mycred_pref_core = 'a:23:{s:6:"format";a:3:{s:4:"type";s:6:"bigint";s:8:"decimals";i:0;s:10:"separators";a:2:{s:7:"decimal";s:1:".";s:8:"thousand";s:1:",";}}s:7:"cred_id";s:14:"mycred_default";s:4:"name";a:2:{s:8:"singular";s:5:"Point";s:6:"plural";s:6:"Points";}s:6:"before";s:0:"";s:5:"after";s:0:"";s:4:"caps";a:2:{s:6:"plugin";s:14:"manage_options";s:5:"creds";s:6:"export";}s:3:"max";i:0;s:7:"exclude";a:4:{s:14:"plugin_editors";i:0;s:12:"cred_editors";i:0;s:4:"list";s:0:"";s:8:"by_roles";s:0:"";}s:11:"delete_user";i:0;s:13:"attachment_id";s:1:"7";s:7:"caching";a:3:{s:7:"history";s:3:"off";s:12:"leaderboards";s:3:"off";s:10:"autodelete";i:0;}s:6:"export";a:4:{s:5:"front";i:0;s:12:"front_format";s:9:"formatted";s:5:"admin";i:0;s:12:"admin_format";s:4:"both";}s:15:"br_social_share";a:6:{s:20:"enable_open_badge_ss";s:1:"0";s:12:"button_style";s:12:"button_style";s:9:"enable_fb";s:1:"1";s:14:"enable_twitter";s:1:"1";s:9:"enable_li";s:1:"1";s:9:"enable_pt";s:1:"1";}s:6:"badges";a:12:{s:11:"show_all_bp";i:0;s:11:"show_all_bb";i:0;s:10:"buddypress";s:0:"";s:7:"bbpress";s:0:"";s:10:"open_badge";i:0;s:22:"show_level_description";i:0;s:15:"show_congo_text";i:0;s:11:"show_levels";i:0;s:17:"show_level_points";i:0;s:21:"show_steps_to_achieve";i:0;s:12:"show_earners";i:0;s:24:"open_badge_evidence_page";i:0;}s:9:"buy_creds";a:8:{s:5:"types";a:1:{i:0;s:14:"mycred_default";}s:8:"checkout";s:4:"page";s:3:"log";s:17:"%plural% purchase";s:5:"login";s:34:"Please login to purchase %_plural%";s:8:"thankyou";a:3:{s:4:"page";i:15;s:6:"custom";s:0:"";s:3:"use";s:4:"page";}s:9:"cancelled";a:3:{s:4:"page";i:15;s:6:"custom";s:0:"";s:3:"use";s:4:"page";}s:10:"custom_log";i:0;s:7:"gifting";a:3:{s:7:"members";i:1;s:7:"authors";i:1;s:3:"log";s:34:"Gift purchase from %display_name%.";}}s:9:"cashcreds";a:1:{s:9:"debugging";s:7:"disable";}s:7:"coupons";a:8:{s:3:"log";s:17:"Coupon redemption";s:7:"invalid";s:26:"This is not a valid coupon";s:7:"expired";s:23:"This coupon has expired";s:10:"user_limit";s:33:"You have already used this coupon";s:3:"min";s:52:"A minimum of %amount% is required to use this coupon";s:3:"max";s:52:"A maximum of %amount% is required to use this coupon";s:8:"excluded";s:24:"You can not use coupons.";s:7:"success";s:49:"%amount% successfully deposited into your account";}s:12:"emailnotices";a:7:{s:8:"use_html";i:1;s:6:"filter";a:2:{s:7:"subject";i:0;s:7:"content";i:0;}s:4:"from";a:3:{s:4:"name";s:12:"VidMov Theme";s:5:"email";s:22:"support@beeteam368.com";s:8:"reply_to";s:22:"support@beeteam368.com";}s:7:"content";s:0:"";s:7:"styling";s:0:"";s:4:"send";s:0:"";s:8:"override";i:0;}s:13:"notifications";a:4:{s:7:"use_css";i:1;s:8:"template";s:31:"<p>%entry%</p><h1>%cred_f%</h1>";s:4:"life";i:7;s:8:"duration";i:3;}s:12:"sell_content";a:6:{s:10:"post_types";s:30:"post,vidmov_video,vidmov_audio";s:7:"filters";a:3:{s:4:"post";a:2:{s:2:"by";s:6:"manual";s:4:"list";s:0:"";}s:12:"vidmov_video";a:2:{s:2:"by";s:6:"manual";s:4:"list";s:0:"";}s:12:"vidmov_audio";a:2:{s:2:"by";s:6:"manual";s:4:"list";s:0:"";}}s:4:"type";a:1:{i:0;s:14:"mycred_default";}s:6:"reload";i:1;s:7:"working";s:14:"Processing ...";s:9:"templates";a:3:{s:7:"members";s:177:"<div class="text-center">
<h2 class="h1 h4-mobile">Premium Content</h2>
<div class="sell-descriptions">Buy access to this content</div>
%buy_button% %watch_trailer%

</div>";s:8:"visitors";s:187:"<div class="text-center">
<h2 class="h1 h4-mobile">Premium Content</h2>
<div class="sell-descriptions">Login to buy access to this content.</div>
%login_form% %watch_trailer%

</div>";s:10:"cantafford";s:169:"<div class="text-center">
<h2 class="h1 h4-mobile">Premium Content</h2>
<div class="sell-descriptions">Insufficient Funds</div>
%buy_points% %watch_trailer%

</div>";}}s:9:"transfers";a:8:{s:5:"types";a:1:{i:0;s:14:"mycred_default";}s:6:"reload";i:1;s:7:"message";i:128;s:8:"autofill";s:10:"user_login";s:5:"limit";a:2:{s:5:"limit";s:4:"none";s:6:"amount";i:1000;}s:9:"templates";a:4:{s:6:"button";s:8:"Transfer";s:5:"login";s:0:"";s:5:"limit";s:62:"<strong>Your current %limit% transfer limit is %left%</strong>";s:7:"balance";s:61:"<strong>Your current "%plural%" balance is %balance%</strong>";}s:4:"logs";a:2:{s:7:"sending";s:31:"Give %plural% to %display_name%";s:9:"receiving";s:33:"Give %plural% from %display_name%";}s:6:"errors";a:2:{s:3:"low";s:40:"You do not have enough %plural% to send.";s:4:"over";s:46:"You have exceeded your %limit% transfer limit.";}}s:4:"rank";a:9:{s:7:"support";a:5:{s:7:"content";b:0;s:7:"excerpt";b:0;s:8:"comments";b:0;s:15:"page-attributes";b:0;s:13:"custom-fields";b:0;}s:4:"base";s:7:"current";s:6:"public";b:0;s:4:"slug";s:11:"mycred_rank";s:5:"order";s:3:"ASC";s:11:"bb_location";s:0:"";s:11:"bb_template";s:0:"";s:11:"bp_location";s:0:"";s:11:"bp_template";s:0:"";}s:5:"stats";a:5:{s:14:"color_positive";s:7:"#ccaf0b";s:14:"color_negative";s:7:"#3350f4";s:7:"animate";i:1;s:6:"bezier";i:1;s:7:"caching";s:3:"off";}}';
			update_option('mycred_pref_core', unserialize($mycred_pref_core));
			
			$mycred_pref_hooks = 'a:3:{s:9:"installed";a:0:{}s:6:"active";a:0:{}s:10:"hook_prefs";a:0:{}}';
			update_option('mycred_pref_hooks', unserialize($mycred_pref_hooks));
			
			$mycred_pref_remote = 'a:4:{s:7:"enabled";i:0;s:3:"key";s:0:"";s:3:"uri";s:7:"api-dev";s:5:"debug";i:0;}';
			update_option('mycred_pref_remote', unserialize($mycred_pref_remote));
			
			update_option('mycred_sell_content_one_seven_updated', current_time('timestamp'));
			
			$mycred_sell_this_mycred_default = 'a:8:{s:6:"status";s:7:"enabled";s:5:"price";s:1:"0";s:6:"expire";s:1:"0";s:12:"profit_share";s:2:"70";s:12:"button_label";s:70:"<i class="fas fa-dollar-sign icon"></i><span>Pay %price% Points</span>";s:14:"button_classes";s:22:"btn btn-primary btn-lg";s:11:"log_payment";s:29:"Purchase of %link_with_title%";s:8:"log_sale";s:25:"Sale of %link_with_title%";}'; 
			update_option('mycred_sell_this_mycred_default', unserialize($mycred_sell_this_mycred_default));
			
			update_option('mycred_setup_completed', current_time('timestamp'));
			
			$mycred_types = 'a:1:{s:14:"mycred_default";s:6:"myCRED";}';
			update_option('mycred_types', unserialize($mycred_types));
			*/
			
			$sidebars_widgets = get_option( 'sidebars_widgets' );
			if(isset($sidebars_widgets) && is_array($sidebars_widgets)){				
				if(isset($sidebars_widgets['main-sidebar']) && is_array($sidebars_widgets['main-sidebar']) && count($sidebars_widgets['main-sidebar']) > 0){				
					foreach ($sidebars_widgets['main-sidebar'] as $i => $value) {
						unset($sidebars_widgets['main-sidebar'][$i]);
					}
					update_option('sidebars_widgets', $sidebars_widgets);
				}
				
				if(isset($sidebars_widgets['footer-sidebar']) && is_array($sidebars_widgets['footer-sidebar']) && count($sidebars_widgets['footer-sidebar']) > 0){
					foreach ($sidebars_widgets['footer-sidebar'] as $i => $value) {
						unset($sidebars_widgets['footer-sidebar'][$i]);
					}
					update_option('sidebars_widgets', $sidebars_widgets);
				}
				
				if(isset($sidebars_widgets['sidemenu-sidebar']) && is_array($sidebars_widgets['sidemenu-sidebar']) && count($sidebars_widgets['sidemenu-sidebar']) > 0){
					foreach ($sidebars_widgets['sidemenu-sidebar'] as $i => $value) {
						unset($sidebars_widgets['sidemenu-sidebar'][$i]);
					}
					update_option('sidebars_widgets', $sidebars_widgets);
				}
				
			}
			
			global $wp_rewrite;
    		$wp_rewrite->set_permalink_structure( '/%postname%/' );
			
			add_option('beeteam368_extensions_pro_activated_plugin', 'BEETEAM368_EXTENSIONS_PRO');
			flush_rewrite_rules();
		}
	}
}
add_action('ocdi/before_content_import', 'beeteam368_before_content_import');

if(!function_exists('beeteam368_after_content_import')){
	function beeteam368_after_content_import(){
		$front_page = get_page_by_path( 'main-demo' );
		if($front_page){
			update_option('show_on_front', 'page');
			update_option('page_on_front', $front_page->ID);
		}
		
		$menus = get_terms('nav_menu', array('hide_empty' => false));
		if ( is_array($menus) && !empty($menus) ) {
			foreach ($menus as $single_menu) {
				if (is_object( $single_menu ) && isset($single_menu->name, $single_menu->term_id)){		
					if(trim($single_menu->name) == 'Main Menu'){			
						$locations = get_theme_mod( 'nav_menu_locations' );
						$locations['beeteam368-MainMenu'] = $single_menu->term_id;
						set_theme_mod ( 'nav_menu_locations', $locations );	
					}elseif(trim($single_menu->name) == 'Side Menu'){
						$locations = get_theme_mod( 'nav_menu_locations' );
						$locations['beeteam368-SideMenu'] = $single_menu->term_id;
						set_theme_mod ( 'nav_menu_locations', $locations );
					}
				}
			}
		}
		
		$_theme_settings = get_option(BEETEAM368_PREFIX . '_theme_settings');		
		$trending_page = get_page_by_path('trending');
		if($trending_page){
			$_theme_settings[BEETEAM368_PREFIX . '_trending_page'] = $trending_page->ID;
			update_option(BEETEAM368_PREFIX . '_theme_settings', $_theme_settings);
		}
		
		/*
		if( class_exists( 'myCRED_Setup_Import_Export' ) ):
			$myCRED_Setup_Import_Export = new myCRED_Setup_Import_Export();
			$myCRED_Setup_Import_Export->import_setup_json(trailingslashit( BEETEAM368_EXTENSIONS_PRO_PATH ) . 'inc/sample-data/raw-export-setup.json');
		endif;
		*/
		
		$buycred_page = get_page_by_path('buy');
		if($buycred_page){
			$_theme_settings[BEETEAM368_PREFIX . '_buycred_page'] = $buycred_page->ID;			
			update_option(BEETEAM368_PREFIX . '_theme_settings', $_theme_settings);
			
			/*
			$mycred_pref_core = get_option('mycred_pref_core');
			$mycred_pref_core['buy_creds']['thankyou']['page'] = $buycred_page->ID;
			$mycred_pref_core['buy_creds']['cancelled']['page'] = $buycred_page->ID;			
			update_option('mycred_pref_core', $mycred_pref_core);
			*/
		}
		
		$_channel_settings = get_option(BEETEAM368_PREFIX . '_channel_settings');
		$channel_page = get_page_by_path('channel');
		if($channel_page){
			$_channel_settings[BEETEAM368_PREFIX . '_channel_page'] = $channel_page->ID;
			update_option(BEETEAM368_PREFIX . '_channel_settings', $_channel_settings);
		}		
		$member_page = get_page_by_path('member-list');
		if($member_page){
			$_channel_settings[BEETEAM368_PREFIX . '_member_page'] = $member_page->ID;
			update_option(BEETEAM368_PREFIX . '_channel_settings', $_channel_settings);
		}		
		
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		
		add_option('beeteam368_extensions_pro_activated_plugin', 'BEETEAM368_EXTENSIONS_PRO');
		flush_rewrite_rules();
	}
}
add_action('ocdi/after_import', 'beeteam368_after_content_import' );

if(!function_exists('beeteam368_register_plugins')){
	function beeteam368_register_plugins($plugins){
		
		$template_directory = get_template_directory();
		
		$theme_plugins = [
			array(
				'name'               => esc_html__( 'BeeTeam368 Extensions', 'beeteam368-extensions-pro'), 
				'slug'               => 'beeteam368-extensions',
				'source'             => $template_directory . '/inc/plugins/beeteam368-extensions-2.3.9.zip',
				'required'           => true,
			),
			
			array(
				'name'               => esc_html__( 'BeeTeam368 Extensions Pro', 'beeteam368-extensions-pro'), 
				'slug'               => 'beeteam368-extensions-pro',
				'source'             => $template_directory . '/inc/plugins/beeteam368-extensions-2.3.9.zip',
				'required'           => true,
			),
			
			array(
				  'name'     => esc_html__('CMB2', 'beeteam368-extensions-pro'),
				  'slug'     => 'cmb2',
				  'required' => true
			),
			
			array(
				  'name'     => esc_html__('Redux Framework', 'beeteam368-extensions-pro'),
				  'slug'     => 'redux-framework',
				  'required' => true
			),
			
			array(
				  'name'     => esc_html__('Elementor', 'beeteam368-extensions-pro'),
				  'slug'     => 'elementor',
				  'required' => true
			),			
			
			array(
				  'name'     => esc_html__('WP PageNavi', 'beeteam368-extensions-pro'),
				  'slug'     => 'wp-pagenavi',
				  'required' => true
			),
			
			array(
				  'name'     => esc_html__('myCred', 'beeteam368-extensions-pro'),
				  'slug'     => 'mycred',
				  'required' => true
			),
			
			array(
				  'name'     => esc_html__('Theme My Login', 'beeteam368-extensions-pro'),
				  'slug'     => 'theme-my-login',
				  'required' => true
			),
			
			array(
				  'name'     => esc_html__('Sassy Social Share', 'beeteam368-extensions-pro'),
				  'slug'     => 'sassy-social-share',
				  'required' => true
			)
		];
		
		if(isset($_GET['step']) && $_GET['step'] === 'import' && isset($_GET['import'])){
			$beeteam368_cast_settings = 'a:11:{s:20:"beeteam368_cast_slug";s:4:"cast";s:38:"beeteam368_cast_archive_items_per_page";s:2:"10";s:37:"beeteam368_cast_single_items_per_page";s:1:"9";s:27:"beeteam368_cast_media_order";s:3:"new";s:39:"beeteam368_cast_single_media_categories";s:2:"on";s:21:"beeteam368_cast_clone";a:2:{i:0;a:5:{s:15:"clone_post_type";s:4:"crew";s:10:"clone_slug";s:4:"crew";s:14:"clone_singular";s:4:"Crew";s:12:"clone_plural";s:5:"Crews";s:10:"clone_icon";s:28:"<i class="fas fa-users"></i>";}i:1;a:5:{s:15:"clone_post_type";s:6:"artist";s:10:"clone_slug";s:6:"artist";s:14:"clone_singular";s:6:"Artist";s:12:"clone_plural";s:7:"Artists";s:10:"clone_icon";s:30:"<i class="fab fa-napster"></i>";}}s:29:"beeteam368_cast_single_layout";s:10:"marguerite";s:33:"beeteam368_cast_single_pagination";s:12:"loadmore-btn";s:30:"beeteam368_cast_archive_layout";s:4:"rose";s:31:"beeteam368_cast_archive_sidebar";s:6:"hidden";s:34:"beeteam368_cast_archive_pagination";s:15:"pagenavi_plugin";}';
			update_option('beeteam368_cast_settings', unserialize($beeteam368_cast_settings));
			
			global $wp_rewrite;
    		$wp_rewrite->set_permalink_structure( '/%postname%/' );
			
			add_option('beeteam368_extensions_pro_activated_plugin', 'BEETEAM368_EXTENSIONS_PRO');
			flush_rewrite_rules();
	 	}
		
		return array_merge($plugins, $theme_plugins);
	}
}
add_filter( 'ocdi/register_plugins', 'beeteam368_register_plugins' );

if(!function_exists('beeteam368_import_files')){
	function beeteam368_import_files() {
		return [
			[
				'import_file_name'           => 'VidMov - Main Demo',
				'local_import_file'          => trailingslashit( BEETEAM368_EXTENSIONS_PRO_PATH ) . 'inc/sample-data/vidmov-theme-data.xml',
				'local_import_widget_file'   => trailingslashit( BEETEAM368_EXTENSIONS_PRO_PATH ) . 'inc/sample-data/vidmov-theme-widgets.wie',
				'local_import_redux'         => [
				[
					'file_path'    => trailingslashit( BEETEAM368_EXTENSIONS_PRO_PATH ) . 'inc/sample-data/vidmov-theme-options.json',
					'option_name' => apply_filters(BEETEAM368_PREFIX . '_theme_options/opt_name', BEETEAM368_PREFIX . '_theme_options'),
				],
				],
				'preview_url'                => 'https://vm.beeteam368.net/',
			],
		];
	}
}
add_filter( 'ocdi/import_files', 'beeteam368_import_files' );
 remove-purchase-verification

main

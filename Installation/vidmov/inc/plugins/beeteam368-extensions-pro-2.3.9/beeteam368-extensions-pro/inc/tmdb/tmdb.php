<?php
if (!class_exists('beeteam368_tmdb')) {
	class beeteam368_tmdb
    {
		public function __construct()
        {			
			add_action('wp_ajax_beeteam368_adminAjaxGetAllTMDBMovies', array($this, 'beeteam368_adminAjaxGetAllTMDBMovies'));
            add_action('wp_ajax_nopriv_beeteam368_adminAjaxGetAllTMDBMovies', array($this, 'beeteam368_adminAjaxGetAllTMDBMovies'));
			
			add_action('wp_ajax_beeteam368_adminAjaxGetAllTMDBTVShows', array($this, 'beeteam368_adminAjaxGetAllTMDBTVShows'));
            add_action('wp_ajax_nopriv_beeteam368_adminAjaxGetAllTMDBTVShows', array($this, 'beeteam368_adminAjaxGetAllTMDBTVShows'));	
			
			add_action('cmb2_admin_init', array($this, 'add_meta_box_for_post'), 10);
			
			add_filter('beeteam368_after_save_post_data', array($this, 'fetch_tmdb'), 9, 3);
			add_filter('beeteam368_after_user_save_post_data', array($this, 'fetch_tmdb'), 9, 3);
			
			add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
			
			add_action('beeteam368_after_content_post', array($this, 'tmdb_single_block_html'), 15, 1);
			add_action('beeteam368_after_content_post', array($this, 'tmdb_tv_single_block_html'), 15, 1);
        }
		
		function tmdb_single_block_html(){
			
			$post_id 	= get_the_ID();
			$tmdb_block	= get_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_data', true);
			
			if(is_array($tmdb_block) && count($tmdb_block)>0){
				foreach($tmdb_block as $movie_data){
					if(is_array($movie_data) && (count($movie_data) === 2 || count($movie_data) === 3) && isset($movie_data['movie_details']) && isset($movie_data['movie_credits'])){
						$movie_details = gettype($movie_data['movie_details'])==='object'?$movie_data['movie_details']:json_decode($movie_data['movie_details']);
						$movie_credits = gettype($movie_data['movie_credits'])==='object'?$movie_data['movie_credits']:json_decode($movie_data['movie_credits']);;
						
						if(isset($movie_details->{'id'})){
							$backdrop_path 	= isset($movie_details->{'backdrop_path'})&&$movie_details->{'backdrop_path'}!=''?'style="background-image:url(https://image.tmdb.org/t/p/w1280'.$movie_details->{'backdrop_path'}.');"':'';
							$poster_path 	= isset($movie_details->{'poster_path'})&&$movie_details->{'poster_path'}!=''?'<img class="blog-picture tmdb-picture" src="https://image.tmdb.org/t/p/w300'.$movie_details->{'poster_path'}.'">':'';
							$original_title = isset($movie_details->{'original_title'})&&$movie_details->{'original_title'}!=''?$movie_details->{'original_title'}:'';
							$global_title	= isset($movie_details->{'title'})&&$movie_details->{'title'}!=''?$movie_details->{'title'}:'';
						?>
						
							<header class="entry-header tmdb-movie-banner dark-background movie-style" <?php echo $backdrop_path;?>>
								<div class="pp-wrapper">
								
									<div class="pp-image"><?php echo $poster_path;?></div>
									
									<div class="pp-content-wrapper">
                                    
                                    	<div class="posted-on ft-post-meta font-meta flex-row-control">
                                            <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                <span class="post-footer-item">                                                	
                                                    <span class="item-text"><?php echo esc_html__('Status:', 'beeteam368-extensions-pro').' '.$movie_details->{'status'};?></span>
                                                </span>
                                                <span class="post-footer-item">                                                	
                                                    <span class="item-text"><?php echo esc_html__('Release Date:', 'beeteam368-extensions-pro').' '.$movie_details->{'release_date'};?></span>
                                                </span>
                                            </div>                                                
                                        </div>
										
										<h2 class="entry-title h1 h5-mobile">
											<?php 
												if($original_title!=$global_title){
													echo esc_html($global_title);
													echo '<span class="tmdb-original-title h6">'.esc_html($original_title).'</span>';
												}else{
													echo esc_html($original_title);
												}
											?>
										</h2>
										
										<?php 
										if( isset($movie_details->{'genres'}) && count($movie_details->{'genres'})>0 ){
										?>
                                        	<div class="posted-on ft-post-meta font-meta flex-row-control">
                                                <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                	<?php foreach($movie_details->{'genres'} as $genres){?>
                                                        <span class="post-footer-item">                                                	
                                                            <span class="item-text"><?php echo esc_html($genres->{'name'})?></span>
                                                        </span>
                                                    <?php }?>	
                                                </div>                                                
                                            </div>
										<?php
										}
										?>
														
									</div>
								</div>
							</header>
						
						<?php
						}
						if(isset($movie_details->{'overview'}) && $movie_details->{'overview'}!=''){
						?>
                        	
                            <div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-info"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Overview', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Overview', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper">
                            	<?php echo esc_html($movie_details->{'overview'});?>
                            </div>
						<?php	
						}
						
						if(isset($movie_details->{'trailers'}) && isset($movie_details->{'trailers'}->{'youtube'}) && isset($movie_details->{'trailers'}->{'youtube'}[0]->{'source'})){
						?>
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-film"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Trailer', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Trailer', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper">
                            	<div class="tmdb-trailer">
                                	<iframe src="https://www.youtube.com/embed/<?php echo esc_attr($movie_details->{'trailers'}->{'youtube'}[0]->{'source'})?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                </div>                            	
                            </div>
						<?php	
						}elseif(isset($movie_data['movie_details_en_us'])){
							$movie_details_en_us = gettype($movie_data['movie_details_en_us'])==='object'?$movie_data['movie_details_en_us']:json_decode($movie_data['movie_details_en_us']);
							if(isset($movie_details_en_us->{'trailers'}) && isset($movie_details_en_us->{'trailers'}->{'youtube'}) && isset($movie_details_en_us->{'trailers'}->{'youtube'}[0]->{'source'})){
						?>
                        		<div class="top-section-title has-icon">
                                    <span class="beeteam368-icon-item"><i class="fas fa-film"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Trailer', 'beeteam368-extensions-pro');?></span>
                                    <h2 class="h2 h3-mobile main-title-heading">
                                    <span class="main-title"><?php echo esc_html__('Trailer', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                    </h2>
                                </div>
                                
                                <div class="cast-variant-items-wrapper">
                                    <div class="tmdb-trailer">
                                        <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($movie_details_en_us->{'trailers'}->{'youtube'}[0]->{'source'});?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>                            	
                                </div>								
						<?php		
							}
						}
						
						if(isset($movie_credits->{'cast'}) && count($movie_credits->{'cast'})>0){						
						?>
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-people-carry"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Cast', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Cast', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper tmdb-listing-ct tmdb-listing-ct-control">
                            	<div class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php							
									foreach ( $movie_credits->{'cast'} as $item) :
									   $profile_path 	= isset($item->{'profile_path'})&&$item->{'profile_path'}!=''?'<img class="blog-img-link blog-img-link-control" src="https://image.tmdb.org/t/p/w92'.$item->{'profile_path'}.'">':'<span class="no-images img-2x3"><span class="no-images-content flex-row-control flex-vertical-middle flex-row-center"><i class="far fa-image"></i><br><span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions-pro').'</span></span></span>';
									   $name 			= isset($item->{'name'})&&$item->{'name'}!=''?$item->{'name'}:'';
									   $character 		= isset($item->{'character'})&&$item->{'character'}!=''?$item->{'character'}:'';
										?>	
										<div class="flex-vertical-middle cast-variant-item ">
                                            <div class="blog-img-wrapper">
                                                <?php echo $profile_path;?>
                                            </div>
                                            <div class="cast-variant-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                	<?php echo esc_html($name)?>
                                                </h3>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html($character)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>				
										<?php				
									endforeach;
									?>
								</div>
								<div class="show-more-cl show-more-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-down"></i><span><?php echo esc_html__('Show More', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
								
								<div class="show-less-cl show-less-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-up"></i><span><?php echo esc_html__('Show Less', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
                            </div>
						
						<?php 
						}
						
						if(isset($movie_credits->{'crew'}) && count($movie_credits->{'crew'})>0){	
						?>  
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-person-booth"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Crew', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Crew', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper tmdb-listing-ct tmdb-listing-ct-control">
                            	<div class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php							
									foreach ( $movie_credits->{'crew'} as $item) :
									   $profile_path 	= isset($item->{'profile_path'})&&$item->{'profile_path'}!=''?'<img class="blog-img-link blog-img-link-control" src="https://image.tmdb.org/t/p/w92'.$item->{'profile_path'}.'">':'<span class="no-images img-2x3"><span class="no-images-content flex-row-control flex-vertical-middle flex-row-center"><i class="far fa-image"></i><br><span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions-pro').'</span></span></span>';
									   $name 			= isset($item->{'name'})&&$item->{'name'}!=''?$item->{'name'}:'';
									   $department 		= isset($item->{'department'})&&$item->{'department'}!=''?$item->{'department'}:'';
										?>	
										<div class="flex-vertical-middle cast-variant-item ">
                                            <div class="blog-img-wrapper">
                                                <?php echo $profile_path;?>
                                            </div>
                                            <div class="cast-variant-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                	<?php echo esc_html($name)?>
                                                </h3>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html($department)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>				
										<?php				
									endforeach;
									?>
								</div>
								<div class="show-more-cl show-more-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-down"></i><span><?php echo esc_html__('Show More', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
								
								<div class="show-less-cl show-less-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-up"></i><span><?php echo esc_html__('Show Less', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
                            </div>
						<?php
						}
					}
				}
			}
		}
		
		function tmdb_tv_single_block_html(){
			
			$post_id 	= get_the_ID();
			$tmdb_block	= get_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_tv_data', true);
			
			if(is_array($tmdb_block) && count($tmdb_block)>0){
				foreach($tmdb_block as $tv_data){
					
					if(is_array($tv_data) && count($tv_data)==2 && isset($tv_data['tv_details']) && isset($tv_data['tv_credits'])){
						$tv_details = gettype($tv_data['tv_details'])==='object'?$tv_data['tv_details']:json_decode($tv_data['tv_details']);
						$tv_credits = gettype($tv_data['tv_credits'])==='object'?$tv_data['tv_credits']:json_decode($tv_data['tv_credits']);
						
						if(isset($tv_details->{'id'})){
							$backdrop_path 	= isset($tv_details->{'backdrop_path'})&&$tv_details->{'backdrop_path'}!=''?'style="background-image:url(https://image.tmdb.org/t/p/w1280'.$tv_details->{'backdrop_path'}.');"':'';
							$poster_path 	= isset($tv_details->{'poster_path'})&&$tv_details->{'poster_path'}!=''?'<img class="blog-picture tmdb-picture" src="https://image.tmdb.org/t/p/w300'.$tv_details->{'poster_path'}.'">':'';
							$original_name = isset($tv_details->{'original_name'})&&$tv_details->{'original_name'}!=''?$tv_details->{'original_name'}:'';
							$global_name	= isset($tv_details->{'name'})&&$tv_details->{'name'}!=''?$tv_details->{'name'}:'';
							?>
                            
                            <header class="entry-header tmdb-movie-banner dark-background movie-style" <?php echo $backdrop_path;?>>
								<div class="pp-wrapper">
								
									<div class="pp-image"><?php echo $poster_path;?></div>
									
									<div class="pp-content-wrapper">
                                    
                                    	<div class="posted-on ft-post-meta font-meta flex-row-control">
                                            <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                <span class="post-footer-item">                                                	
                                                    <span class="item-text"><?php echo esc_html__('First aired:', 'beeteam368-extensions-pro').' '.$tv_details->{'first_air_date'};?></span>
                                                </span>
                                                <span class="post-footer-item">                                                	
                                                    <span class="item-text"><?php echo esc_html__('Last air date:', 'beeteam368-extensions-pro').' '.$tv_details->{'last_air_date'};?></span>
                                                </span>
                                                <span class="post-footer-item">                                                	
                                                    <span class="item-text"><?php echo esc_html__('Episodes/Seasons:', 'beeteam368-extensions-pro').' '.$tv_details->{'number_of_episodes'}.' / '.$tv_details->{'number_of_seasons'};?></span>
                                                </span>
                                            </div>                                                
                                        </div>
										
										<h2 class="entry-title h1 h5-mobile">
											<?php 
												if($original_name!=$global_name){
													echo esc_html($global_name);
													echo '<span class="tmdb-original-title h6">'.esc_html($original_name).'</span>';
												}else{
													echo esc_html($original_name);
												}
											?>
										</h2>
										
										<?php 
										if( isset($tv_details->{'genres'}) && count($tv_details->{'genres'})>0 ){
										?>
                                        	<div class="posted-on ft-post-meta font-meta flex-row-control">
                                                <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                	<?php foreach($tv_details->{'genres'} as $genres){?>
                                                        <span class="post-footer-item">                                                	
                                                            <span class="item-text"><?php echo esc_html($genres->{'name'})?></span>
                                                        </span>
                                                    <?php }?>	
                                                </div>                                                
                                            </div>
										<?php
										}
										?>
														
									</div>
								</div>
							</header>
                            
                            <?php
						}
						
						if(isset($tv_details->{'overview'}) && $tv_details->{'overview'}!=''){
						?>
                        	
                            <div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-info"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Overview', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Overview', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper">
                            	<?php echo esc_html($tv_details->{'overview'});?>
                            </div>
						<?php	
						}
						
						if(isset($tv_credits->{'cast'}) && count($tv_credits->{'cast'})>0){						
						?>
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-people-carry"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Cast', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Cast', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper tmdb-listing-ct tmdb-listing-ct-control">
                            	<div class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php							
									foreach ( $tv_credits->{'cast'} as $item) :
									   $profile_path 	= isset($item->{'profile_path'})&&$item->{'profile_path'}!=''?'<img class="blog-img-link blog-img-link-control" src="https://image.tmdb.org/t/p/w92'.$item->{'profile_path'}.'">':'<span class="no-images img-2x3"><span class="no-images-content flex-row-control flex-vertical-middle flex-row-center"><i class="far fa-image"></i><br><span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions-pro').'</span></span></span>';
									   $name 			= isset($item->{'name'})&&$item->{'name'}!=''?$item->{'name'}:'';
									   $character 		= isset($item->{'character'})&&$item->{'character'}!=''?$item->{'character'}:'';
										?>	
										<div class="flex-vertical-middle cast-variant-item ">
                                            <div class="blog-img-wrapper">
                                                <?php echo $profile_path;?>
                                            </div>
                                            <div class="cast-variant-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                	<?php echo esc_html($name)?>
                                                </h3>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html($character)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>				
										<?php				
									endforeach;
									?>
								</div>
								<div class="show-more-cl show-more-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-down"></i><span><?php echo esc_html__('Show More', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
								
								<div class="show-less-cl show-less-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-up"></i><span><?php echo esc_html__('Show Less', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
                            </div>
						
						<?php 
						}
						
						if(isset($tv_credits->{'crew'}) && count($tv_credits->{'crew'})>0){	
						?>  
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-person-booth"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Crew', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Crew', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper tmdb-listing-ct tmdb-listing-ct-control">
                            	<div class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php							
									foreach ( $tv_credits->{'crew'} as $item) :
									   $profile_path 	= isset($item->{'profile_path'})&&$item->{'profile_path'}!=''?'<img class="blog-img-link blog-img-link-control" src="https://image.tmdb.org/t/p/w92'.$item->{'profile_path'}.'">':'<span class="no-images img-2x3"><span class="no-images-content flex-row-control flex-vertical-middle flex-row-center"><i class="far fa-image"></i><br><span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions-pro').'</span></span></span>';
									   $name 			= isset($item->{'name'})&&$item->{'name'}!=''?$item->{'name'}:'';
									   $department 		= isset($item->{'department'})&&$item->{'department'}!=''?$item->{'department'}:'';
										?>	
										<div class="flex-vertical-middle cast-variant-item ">
                                            <div class="blog-img-wrapper">
                                                <?php echo $profile_path;?>
                                            </div>
                                            <div class="cast-variant-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                	<?php echo esc_html($name)?>
                                                </h3>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html($department)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>				
										<?php				
									endforeach;
									?>
								</div>
								<div class="show-more-cl show-more-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-down"></i><span><?php echo esc_html__('Show More', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
								
								<div class="show-less-cl show-less-cl-control">
									<button class="small-style reverse btn-show-more-cl-control">
										<i class="icon fas fa-angle-double-up"></i><span><?php echo esc_html__('Show Less', 'beeteam368-extensions-pro');?></span>
									</button>
								</div>
                            </div>
						<?php
						}
						
						if(isset($tv_details->{'seasons'}) && count($tv_details->{'seasons'})>0){
						?>
                        	<div class="top-section-title has-icon">
                                <span class="beeteam368-icon-item"><i class="fas fa-person-booth"></i></span> <span class="sub-title font-main"><?php echo esc_html__('Seasons', 'beeteam368-extensions-pro');?></span>
                                <h2 class="h2 h3-mobile main-title-heading">
                                <span class="main-title"><?php echo esc_html__('Seasons', 'beeteam368-extensions-pro');?></span><span class="hd-line"></span>
                                </h2>
                            </div>
                            
                            <div class="cast-variant-items-wrapper tmdb-listing-ct">
                            	<div class="cast-variant-items-row flex-row-control blog-wrapper-control">
									<?php							
									foreach ( $tv_details->{'seasons'} as $item) :
									   	$poster_path 	= isset($item->{'poster_path'})&&$item->{'poster_path'}!=''?'<img class="blog-img-link blog-img-link-control" src="https://image.tmdb.org/t/p/w92'.$item->{'poster_path'}.'">':'<span class="no-images img-2x3"><span class="no-images-content flex-row-control flex-vertical-middle flex-row-center"><i class="far fa-image"></i><br><span class="font-size-12-mobile">'.esc_html__('No Image Available', 'beeteam368-extensions-pro').'</span></span></span>';
									   	$name 			= isset($item->{'name'})&&$item->{'name'}!=''?$item->{'name'}:'';
									   	$air_date 		= isset($item->{'air_date'})&&$item->{'air_date'}!=''?$item->{'air_date'}:'--:--';
										$episode_count	= isset($item->{'episode_count'})&&$item->{'episode_count'}!=''?$item->{'episode_count'}:'--:--';
										?>	
										<div class="flex-vertical-middle cast-variant-item ">
                                            <div class="blog-img-wrapper">
                                                <?php echo $poster_path;?>
                                            </div>
                                            <div class="cast-variant-content">
                                                <h3 class="entry-title post-title max-2lines h6">
                                                	<?php echo esc_html($name)?>
                                                </h3>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html__('Air Date:', 'beeteam368-extensions-pro').' '.esc_html($air_date)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                                <div class="posted-on ft-post-meta font-meta font-meta-size-12 flex-row-control">
                                                    <div class="post-lt-ft-left flex-row-control flex-vertical-middle flex-row-center">
                                                        <span class="post-footer-item">                                                	
                                                            <?php echo esc_html__('Episodes:', 'beeteam368-extensions-pro').' '.esc_html($episode_count)?>
                                                        </span>
                                                    </div>                                                
                                                </div>
                                            </div>
                                        </div>				
										<?php				
									endforeach;
									?>
								</div>
                            </div>
                        <?php
						}
					}
					
				}
			}
		}
		
		function timeout(){
			return 368;
		}
		
		function construct_filename( $post_id ) {
			$filename = get_the_title( $post_id );
			$filename = sanitize_title( $filename, $post_id );
			$filename = urldecode( $filename );
			$filename = preg_replace( '/[^a-zA-Z0-9\-]/', '', $filename );
			$filename = substr( $filename, 0, 32 );
			$filename = trim( $filename, '-' );
			if ( $filename == '' ) $filename = (string) $post_id;
			return $filename;
		}
		
		function update_img($post_id = 0, $image_url = '', $img_name = ''){
			if(empty($post_id) || $post_id == NULL || $post_id == 0 || $image_url == ''){
				return;
			}
			
			$error = '';		
			
			$args = array(
				'timeout'     => $this->timeout(),				
			); 
			
			$response = wp_remote_get($image_url, $args);
			
			if( is_wp_error( $response ) ) {
				$error = new WP_Error('thumbnail_retrieval', sprintf( esc_html__( 'Error retrieving a thumbnail from the URL %1$s using wp_remote_get(). If opening that URL in your web browser returns anything else than an error page, the problem may be related to your web server and might be something your host administrator can solve.', 'beeteam368-extensions-pro'), $image_url ) . esc_html__( 'Error Details:', 'beeteam368-extensions-pro') . ' ' . $response->get_error_message());
			}else{
				$image_contents = $response['body'];
				$image_type = wp_remote_retrieve_header($response, 'content-type');
			}
	
			if ($error != ''){
				return $error;
			}else{
	
				if($image_type == 'image/jpeg'){
					$image_extension = '.jpg';
				}elseif ($image_type == 'image/png'){
					$image_extension = '.png';
				}elseif($image_type == 'image/gif'){
					$image_extension = '.gif';
				} else {
					return new WP_Error('thumbnail_upload', esc_html__( 'Unsupported MIME type:', 'beeteam368-extensions-pro') . ' ' . $image_type);
				}
	
				$new_filename = $this->construct_filename($post_id) . $image_extension;
				
				$upload = wp_upload_bits($new_filename, null, $image_contents);
	
				if($upload['error']){
					$error = new WP_Error('thumbnail_upload', esc_html__( 'Error uploading image data:', 'beeteam368-extensions-pro') . ' ' . $upload['error']);
					return $error;
				}else{
		
					$wp_filetype = wp_check_filetype(basename( $upload['file'] ), NULL);
	
					$upload = apply_filters('wp_handle_upload', array(
						'file' => $upload['file'],
						'url'  => $upload['url'],
						'type' => $wp_filetype['type']
					), 'sideload');
	
					$attachment = array(
						'post_mime_type'	=> $upload['type'],
						'post_title'		=> get_the_title($post_id),
						'post_content'		=> '',
						'post_status'		=> 'inherit'
					);
					
					$attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
					wp_update_attachment_metadata($attach_id, $attach_data);	
					set_post_thumbnail($post_id, $attach_id);
	
				}	
			}
	
			return $attach_id;
		}
		
		function fetch_tmdb($post_data, $post_id, $post_type){
			
			if($post_type != BEETEAM368_POST_TYPE_PREFIX . '_video' && $post_type != BEETEAM368_POST_TYPE_PREFIX . '_series'){
				return $post_data;				
			}
			
			if(isset($_POST[BEETEAM368_PREFIX . '_vid_tmdb_movie']) && is_array($_POST[BEETEAM368_PREFIX . '_vid_tmdb_movie'])){
				
				$arr_movie_data = array();
				$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
				$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');
				
				foreach($_POST[BEETEAM368_PREFIX . '_vid_tmdb_movie'] as $movie_item){
					if(is_numeric($movie_item)){
						$response_movie 	= wp_remote_get('https://api.themoviedb.org/3/movie/'.$movie_item.'?api_key='.$tmdb_api_key.'&language='.$tmdb_language.'&append_to_response=trailers', array('timeout' => $this->timeout()) );
						$response_credits 	= wp_remote_get('https://api.themoviedb.org/3/movie/'.$movie_item.'/credits?api_key='.$tmdb_api_key.'&language='.$tmdb_language, array('timeout' => $this->timeout()) );
						
						if($tmdb_language!='en-US'){
							$response_movie_en_us = wp_remote_get('https://api.themoviedb.org/3/movie/'.$movie_item.'?api_key='.$tmdb_api_key.'&language=en-US&append_to_response=trailers', array('timeout' => $this->timeout()) );
						}
						
						if(!is_wp_error($response_movie) && !is_wp_error($response_credits)){
							
							$response_movie_body 	= json_decode($response_movie['body']);
							$response_credits_body 	= json_decode($response_credits['body']);
							
							if(isset($response_movie_en_us)){
								$response_movie_body_en_us = json_decode($response_movie_en_us['body']);
								$arr_movie_data[] = array('movie_details'=> $response_movie_body, 'movie_credits'=> $response_credits_body, 'movie_details_en_us'=> $response_movie_body_en_us);
							}else{
								$arr_movie_data[] = array('movie_details'=> $response_movie_body, 'movie_credits'=> $response_credits_body);
							}	
							
							$use_data_tmdb = $response_movie_body;
							if(isset($response_movie_body_en_us)){
								$use_data_tmdb_en_us = $response_movie_body_en_us;
							}
							
							if(isset($use_data_tmdb->{'imdb_id'}) && $use_data_tmdb->{'imdb_id'}!=''){
								update_post_meta($post_id, BEETEAM368_PREFIX . '_imdb_ratings', '');
							}
							
							if(isset($use_data_tmdb->{'trailers'}) && isset($use_data_tmdb->{'trailers'}->{'youtube'}) && isset($use_data_tmdb->{'trailers'}->{'youtube'}[0]->{'source'})){
								
								update_post_meta($post_id, BEETEAM368_PREFIX . '_video_url_preview', 'https://www.youtube.com/watch?v='.esc_attr($use_data_tmdb->{'trailers'}->{'youtube'}[0]->{'source'}));
								update_post_meta($post_id, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');
								
								$_POST[BEETEAM368_PREFIX . '_video_url_preview'] = 'https://www.youtube.com/watch?v='.esc_attr($use_data_tmdb->{'trailers'}->{'youtube'}[0]->{'source'});
								$_POST[BEETEAM368_PREFIX . '_video_formats_preview'] = 'auto';
								
							}elseif(isset($use_data_tmdb_en_us)){								
								if(isset($use_data_tmdb_en_us->{'trailers'}) && isset($use_data_tmdb_en_us->{'trailers'}->{'youtube'}) && isset($use_data_tmdb_en_us->{'trailers'}->{'youtube'}[0]->{'source'})){
									
									update_post_meta($post_id, BEETEAM368_PREFIX . '_video_url_preview', 'https://www.youtube.com/watch?v='.esc_attr($use_data_tmdb_en_us->{'trailers'}->{'youtube'}[0]->{'source'}));
									update_post_meta($post_id, BEETEAM368_PREFIX . '_video_formats_preview', 'auto');
									
									$_POST[BEETEAM368_PREFIX . '_video_url_preview'] = 'https://www.youtube.com/watch?v='.esc_attr($use_data_tmdb_en_us->{'trailers'}->{'youtube'}[0]->{'source'});
									$_POST[BEETEAM368_PREFIX . '_video_formats_preview'] = 'auto';									
								}
							}
							
							if(!isset($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb']) || trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])=='poster_sizes' || trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])=='backdrop_sizes'){
								
								$tmdb_original_title = isset($use_data_tmdb->{'original_title'})&&$use_data_tmdb->{'original_title'}!=''?$use_data_tmdb->{'original_title'}:'';
								
								/*
									"backdrop_sizes": [
									  "w300",
									  "w780",
									  "w1280",
									  "original"
									],
									"logo_sizes": [
									  "w45",
									  "w92",
									  "w154",
									  "w185",
									  "w300",
									  "w500",
									  "original"
									],
									"poster_sizes": [
									  "w92",
									  "w154",
									  "w185",
									  "w342",
									  "w500",
									  "w780",
									  "original"
									],
									"profile_sizes": [
									  "w45",
									  "w185",
									  "h632",
									  "original"
									],
									"still_sizes": [
									  "w92",
									  "w185",
									  "w300",
									  "original"
									]
								*/
								
								switch(trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])){
									case 'poster_sizes':
										if(isset($use_data_tmdb->{'poster_path'}) && $use_data_tmdb->{'poster_path'}!=''){
											$tmdb_thumb_imp = 'https://image.tmdb.org/t/p/w780'.$use_data_tmdb->{'poster_path'};
										}elseif(isset($use_data_tmdb_en_us) && isset($use_data_tmdb_en_us->{'poster_path'}) && $use_data_tmdb_en_us->{'poster_path'}!=''){
											$tmdb_thumb_imp = 'https://image.tmdb.org/t/p/w780'.$use_data_tmdb_en_us->{'poster_path'};
										}
										break;
										
									case 'backdrop_sizes':
										if(isset($use_data_tmdb->{'backdrop_path'}) && $use_data_tmdb->{'backdrop_path'}!=''){
											$tmdb_thumb_imp = 'https://image.tmdb.org/t/p/w1280'.$use_data_tmdb->{'backdrop_path'};
										}elseif(isset($use_data_tmdb_en_us) && isset($use_data_tmdb_en_us->{'backdrop_path'}) && $use_data_tmdb_en_us->{'backdrop_path'}!=''){
											$tmdb_thumb_imp = 'https://image.tmdb.org/t/p/w1280'.$use_data_tmdb_en_us->{'backdrop_path'};
										}
										break;	
								}
								
								if(isset($tmdb_thumb_imp) && $tmdb_thumb_imp!=''){
									if(!has_post_thumbnail( $post_id )){
										$this->update_img($post_id, $tmdb_thumb_imp, $tmdb_original_title);
									}
								}
								
							}
						}
					}
				}
				
				update_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_data', $arr_movie_data);
			}else{
				
				update_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_data', '');
				
			}
			
			if(isset($_POST[BEETEAM368_PREFIX . '_vid_tmdb_tv_shows']) && is_array($_POST[BEETEAM368_PREFIX . '_vid_tmdb_tv_shows'])){
				
				$arr_tv_data 	= array();
				$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
				$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');
				
				foreach($_POST[BEETEAM368_PREFIX . '_vid_tmdb_tv_shows'] as $tv_item){
					
					if(is_numeric($tv_item)){
						$response_tvshows 			= wp_remote_get('https://api.themoviedb.org/3/tv/'.$tv_item.'?api_key='.$tmdb_api_key.'&language='.$tmdb_language.'&append_to_response=trailers', array('timeout' => self::timeout()) );
						$response_tvshows_credits 	= wp_remote_get('https://api.themoviedb.org/3/tv/'.$tv_item.'/credits?api_key='.$tmdb_api_key.'&language='.$tmdb_language, array('timeout' => self::timeout()) );
						
						if(!is_wp_error($response_tvshows) && !is_wp_error($response_tvshows_credits)){
							
							$response_tvshows_body 			= json_decode($response_tvshows['body']);							
							$response_tvshows_credits_body 	= json_decode($response_tvshows_credits['body']);
							
							$arr_tv_data[] = array('tv_details'=> $response_tvshows_body, 'tv_credits'=> $response_tvshows_credits_body);
							
							if(!isset($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb']) || trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])=='poster_sizes_tv' || trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])=='backdrop_sizes_tv'){
								$tmdb_tv_name = isset($response_tvshows_body->{'name'})&&$response_tvshows_body->{'name'}!=''?$response_tvshows_body->{'name'}:'';
								
								switch(trim($_POST[BEETEAM368_PREFIX . '_vid_tmdb_thumb'])){
									case 'poster_sizes_tv':
										
										if(isset($response_tvshows_body->{'poster_path'}) && $response_tvshows_body->{'poster_path'}!=''){
											$tmdb_thumb_imp_tv = 'https://image.tmdb.org/t/p/w780'.$response_tvshows_body->{'poster_path'};
										}
										
										break;
										
									case 'backdrop_sizes_tv':
										
										if(isset($response_tvshows_body->{'backdrop_path'}) && $response_tvshows_body->{'backdrop_path'}!=''){
											$tmdb_thumb_imp_tv = 'https://image.tmdb.org/t/p/w1280'.$response_tvshows_body->{'backdrop_path'};
										}
										
										break;	
								}
								
								if(isset($tmdb_thumb_imp_tv) && $tmdb_thumb_imp_tv!=''){
									if(!has_post_thumbnail( $post_id )){
										$this->update_img($post_id, $tmdb_thumb_imp_tv, $tmdb_tv_name );
									}
								}
							}
						}
					}
					
				}
				
				update_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_tv_data', $arr_tv_data);
				
			}else{
				
				update_post_meta($post_id, BEETEAM368_PREFIX . '_tmdb_tv_data', '');
				
			}
			
			return $post_data;
		}
		
		function beeteam368_custom_field_tmdb_search($field_args, $field){		
			$id          = $field->args( 'id' );
			$label       = $field->args( 'name' );
			$name        = $field->args( '_name' );
			$value       = $field->escaped_value();
			$description = $field->args( 'description' );
			$post_id	 = is_numeric($field->object_id)?$field->object_id:'';
		?>
			<div class="custom-column-display custom-filter-tmdb-movie-display-control">
				<p><label for="<?php echo esc_attr($id);?>"><?php echo esc_html($label);?></label></p>
				<p class="bee_select_2">
					<select id="<?php echo esc_attr($id);?>" data-placeholder="<?php echo esc_attr__('Select a Movie', 'beeteam368-extensions-pro');?>" class="beeteam36-admin-sl-ajax admin-ajax-find-tmdb-movie-control" name="<?php echo esc_attr($name);?>[]" multiple>
						<?php
						if($post_id!='' && is_array($value) && count($value)>0){
							$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
							$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');		
							foreach ( $value as $item ) {							
								if(is_numeric($item)){
									
									$query_url = 'https://api.themoviedb.org/3/movie/'.($item).'?api_key='.$tmdb_api_key.'&language='.$tmdb_language;
									$args = array(
										'timeout'     => 368,				
									);
									$response = wp_remote_get($query_url, $args);
									
									if(is_wp_error($response)){
										
									}else {
										$result = json_decode($response['body']);
										if(!isset($result->{'id'}) || !is_numeric($result->{'id'}) || $result->{'id'}<1){				
											
										}else{
										?>
											<option value="<?php echo esc_attr($result->id);?>" selected="selected"><?php echo esc_html($result->original_title);?></option>
										<?php	
										}
									}				
								}
							}
						}
						?>
					</select>
				</p>
				<p class="description"><?php echo wp_kses_post($description); ?></p>
			</div>
		<?php	
		}
		
		function beeteam368_adminAjaxGetAllTMDBMovies(){
			$json_params 			= array();
			$json_params['results'] = array();
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			$keyword = (isset($_POST['keyword'])&&trim($_POST['keyword'])!=''&&strlen($_POST['keyword'])>=2)?trim($_POST['keyword']):'';			
						
			if($keyword=='' || !beeteam368_ajax_verify_nonce($security, false)){
				wp_send_json($json_params);
				return;
				die();
			}
			
			$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
			$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');
			
			$query_url = 'https://api.themoviedb.org/3/search/movie?api_key='.$tmdb_api_key.'&language='.$tmdb_language.'&page=1&include_adult=true&query='.$keyword;
			
			$args = array(
				'timeout'     => 368,				
			);			
			$response = wp_remote_get($query_url, $args);
			
			if(is_wp_error($response)){
				wp_send_json($json_params);
				return;
				die();
			}else {
				$result = json_decode($response['body']);
				if(!isset($result->{'total_results'}) || !is_numeric($result->{'total_results'}) || $result->{'total_results'}<1 || !isset($result->{'results'}) || !is_array($result->{'results'}) || count($result->{'results'})<1){				
					wp_send_json($json_params);
					return;
					die();
				}else{
					foreach ( $result->{'results'} as $movie_item ) {
						array_push($json_params['results'], array('id'=>esc_html($movie_item->id), 'text'=>esc_html($movie_item->original_title)));
					}
				}
			}
					
			wp_send_json($json_params);
			return;
			die();
		}
		
		function beeteam368_custom_field_tmdb_search_tv_shows($field_args, $field){		
			$id          = $field->args( 'id' );
			$label       = $field->args( 'name' );
			$name        = $field->args( '_name' );
			$value       = $field->escaped_value();
			$description = $field->args( 'description' );
			$post_id	 = is_numeric($field->object_id)?$field->object_id:'';
		?>
			<div class="custom-column-display custom-filter-tmdb-tv-shows-display-control">
				<p><label for="<?php echo esc_attr($id);?>"><?php echo esc_html($label);?></label></p>
				<p class="bee_select_2">
					<select id="<?php echo esc_attr($id);?>" data-placeholder="<?php echo esc_attr__('Select a TV-Shows', 'beeteam368-extensions-pro');?>" class="beeteam36-admin-sl-ajax admin-ajax-find-tmdb-tv-shows-control" name="<?php echo esc_attr($name);?>[]" multiple>
						<?php
						if($post_id!='' && is_array($value) && count($value)>0){
							$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
							$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');		
							foreach ( $value as $item ) {							
								if(is_numeric($item)){
									
									$query_url = 'https://api.themoviedb.org/3/tv/'.($item).'?api_key='.$tmdb_api_key.'&language='.$tmdb_language;
									$args = array(
										'timeout'     => 368,				
									);
									$response = wp_remote_get($query_url, $args);
									
									if(is_wp_error($response)){
										
									}else {
										$result = json_decode($response['body']);
										if(!isset($result->{'id'}) || !is_numeric($result->{'id'}) || $result->{'id'}<1){				
											
										}else{
										?>
											<option value="<?php echo esc_attr($result->id);?>" selected="selected"><?php echo esc_html($result->name);?></option>
										<?php	
										}
									}				
								}
							}
						}
						?>
					</select>
				</p>
				<p class="description"><?php echo wp_kses_post($description); ?></p>
			</div>
		<?php	
		}
		
		function beeteam368_adminAjaxGetAllTMDBTVShows(){
			$json_params 			= array();
			$json_params['results'] = array();
			
			$security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
			$keyword = (isset($_POST['keyword'])&&trim($_POST['keyword'])!=''&&strlen($_POST['keyword'])>=2)?trim($_POST['keyword']):'';
			
			$theme_data = wp_get_theme();
			if($keyword=='' || !beeteam368_ajax_verify_nonce($security, false)){
				wp_send_json($json_params);
				return;
				die();
			}
			
			$tmdb_api_key 	= apply_filters('beeteam368_tmdb_movie_api_key', '6f2a688b4bd7ca287e759544a0198ecd');
			$tmdb_language 	= apply_filters('beeteam368_tmdb_movie_language', 'en-US');
			
			$query_url = 'https://api.themoviedb.org/3/search/tv?api_key='.$tmdb_api_key.'&language='.$tmdb_language.'&page=1&include_adult=true&query='.$keyword;
			
			$args = array(
				'timeout'     => 368,				
			);
			$response = wp_remote_get($query_url, $args);
			
			if(is_wp_error($response)){
				wp_send_json($json_params);
				return;
				die();
			}else {
				$result = json_decode($response['body']);
				if(!isset($result->{'total_results'}) || !is_numeric($result->{'total_results'}) || $result->{'total_results'}<1 || !isset($result->{'results'}) || !is_array($result->{'results'}) || count($result->{'results'})<1){				
					wp_send_json($json_params);
					return;
					die();
				}else{
					foreach ( $result->{'results'} as $tv_item ) {
						array_push($json_params['results'], array('id'=>esc_html($tv_item->id), 'text'=>esc_html($tv_item->name)));
					}
				}
			}
					
			wp_send_json($json_params);
			return;
			die();
		}
		
		function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-cast-variant', BEETEAM368_EXTENSIONS_URL . 'inc/cast/assets/cast-variant.css', []);
            }
            return $values;
        }
		
		function add_meta_box_for_post(){
			$object_types = apply_filters('beeteam368_post_video_ads_settings_object_types', array(BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_series'));

            $tmdb_import_settings = new_cmb2_box(array(
                'id' => BEETEAM368_PREFIX . '_tmdb_import_settings',
                'title' => esc_html__('TMDB Import Settings', 'beeteam368-extensions-pro'),
                'object_types' => $object_types,
                'context' => 'normal',
                'priority' => 'high',
                'show_names' => true,
                'show_in_rest' => WP_REST_Server::ALLMETHODS,
            ));
			
			$tmdb_import_settings->add_field(array(
				'name' 			=> esc_html__('TMDB Movie Block', 'beeteam368-extensions-pro'),
				'desc' 			=> esc_html__('Start typing movie name. Eg: fast & furious, 127 hours...', 'beeteam368-extensions-pro'),
				'id'			=> BEETEAM368_PREFIX . '_vid_tmdb_movie',
				'type' 			=> 'text',
				'column'  		=> false,
				'render_row_cb' => array($this, 'beeteam368_custom_field_tmdb_search'),
				'save_field' 	=> true,
			));
			
			$tmdb_import_settings->add_field(array(
				'name' 			=> esc_html__( 'TMDB TV-Shows Block', 'beeteam368-extensions-pro'),
				'desc' 			=> esc_html__( 'Start typing TV-Shows name. Eg: The Good Doctor, The Flash...', 'beeteam368-extensions-pro'),
				'id'			=> BEETEAM368_PREFIX . '_vid_tmdb_tv_shows',
				'type' 			=> 'text',
				'column'  		=> false,
				'render_row_cb' => array($this, 'beeteam368_custom_field_tmdb_search_tv_shows'),
				'save_field' 	=> true,
			));
			
			$tmdb_import_settings->add_field( array(
				'name'      	=> esc_html__( 'TMDB - Automatically generate the Post Thumbnail', 'beeteam368-extensions-pro'),			
				'id'        	=> BEETEAM368_PREFIX . '_vid_tmdb_thumb',
				'type'      	=> 'select',			
				'column'  		=> false,
				'default'		=> 'backdrop_sizes',	
				'options'       => array(
					'backdrop_sizes' 	=> esc_html__('YES - Movie Backdrop', 'beeteam368-extensions-pro'),
					'poster_sizes' 		=> esc_html__('YES - Movie Poster', 'beeteam368-extensions-pro'),
					'backdrop_sizes_tv' => esc_html__('YES - TV-Shows Backdrop', 'beeteam368-extensions-pro'),
					'poster_sizes_tv' 	=> esc_html__('YES - TV-Shows Poster', 'beeteam368-extensions-pro'),				
					'no' 				=> esc_html__('NO', 'beeteam368-extensions-pro'),
				),			
			));
		}	
	}
}

global $beeteam368_tmdb;
$beeteam368_tmdb = new beeteam368_tmdb();
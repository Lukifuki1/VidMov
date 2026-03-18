<?php
$post_id = get_the_ID();
$bg_url = beeteam368_post_thumbnail($post_id, apply_filters('beeteam368_post_thumbnail_params', array('size' => 'full', 'ratio' => 'img-16x9', 'position' => 'slider-layout-sunflower', 'html' => 'url-only', 'echo' => false), $post_id));
?>
<article class="post-item flex-vertical-middle slider-preview-control" style="background-image: url('<?php echo esc_url($bg_url) ?>');">
    <div class="post-item-wrap <?php echo esc_attr(beeteam368_container_classes_control('slider-sunflower-large')); ?>">
    	<div class="site__row">
        	<div class="site__col">
            	<div class="slider-content">                	
                    <?php do_action('beeteam368_post_listing_top_meta', $post_id, apply_filters('beeteam368_post_listing_top_meta_params', array('style' => 'slider-sunflower', 'position' => 'archive-layout-alyssa'), $post_id)); ?>
                    
                	<?php do_action('beeteam368_post_listing_footer', $post_id, apply_filters('beeteam368_post_listing_footer_params', array('style' => 'slider-sunflower', 'position' => 'slider-sunflower-large', 'class' => 'flex-row-control', 'reaction_count' => 4, 'show_view_details' => false), $post_id)); ?>
                    
                	<?php do_action('beeteam368_post_listing_title', $post_id, apply_filters('beeteam368_post_listing_title_params', array('style' => 'slider-sunflower', 'heading' => 'h3', 'heading_class' => '', 'position' => 'slider-sunflower-large'), $post_id)); ?>
                    
                    <?php do_action('beeteam368_after_content_slider_pro', $post_id, array('style' => 'slider-sunflower', 'position' => 'slider-sunflower-large'));?>                    
                </div>            	
            </div>
        </div>    	
    </div>
</article>
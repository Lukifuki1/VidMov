<?php
if(!function_exists('vidmov_enqueue_parent_styles')):
	function vidmov_enqueue_parent_styles() {
		wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'vidmov_enqueue_parent_styles' );
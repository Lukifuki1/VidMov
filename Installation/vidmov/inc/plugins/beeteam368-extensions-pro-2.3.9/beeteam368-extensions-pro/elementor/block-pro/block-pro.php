<?php
if (!class_exists('Beeteam368_Elementor_Addon_Blocks_Pro')) {

    class Beeteam368_Elementor_Addon_Blocks_Pro
    {
        public function __construct()
        {
            add_action('beeteam368_after_enqueue_elementor_style', array($this, 'css'));
            add_action('beeteam368_after_register_block_script', array($this, 'register_script'));
            add_filter('beeteam368_block_script_depends', array($this, 'script_depends'));
            add_filter('beeteam368_elementor_block_layouts', array($this, 'pro_layouts'));
            add_filter('beeteam368_elementor_block_layouts_file', array($this, 'pro_layouts_file'));
        }

        public function css()
        {
            wp_enqueue_style('pagination-js', BEETEAM368_EXTENSIONS_PRO_URL . 'assets/front-end/pagination/pagination.css', array('beeteam368-style-block'), BEETEAM368_EXTENSIONS_PRO_VER);
            wp_enqueue_style('beeteam368-style-block-pro', BEETEAM368_EXTENSIONS_PRO_URL . 'elementor/assets/block-pro/block-pro.css', array('pagination-js'), BEETEAM368_EXTENSIONS_PRO_VER);
        }

        public function register_script()
        {
            wp_register_script('pagination-js', BEETEAM368_EXTENSIONS_PRO_URL . 'assets/front-end/pagination/pagination.min.js', ['jquery'], BEETEAM368_EXTENSIONS_PRO_VER, true);
            wp_register_script('beeteam368-script-block-pro', BEETEAM368_EXTENSIONS_PRO_URL . 'elementor/assets/block-pro/block-pro.js', ['pagination-js'], BEETEAM368_EXTENSIONS_PRO_VER, true);
        }

        public function script_depends($script)
        {
            $script[] = 'pagination-js';
            $script[] = 'beeteam368-script-block-pro';

            return $script;
        }

        public function pro_layouts($layouts)
        {
            //$layouts['grid_pro'] = esc_html__('Grid Pro', 'beeteam368-extensions-pro');

            return $layouts;
        }

        public function pro_layouts_file($files)
        {
            //$files['grid_pro'] = BEETEAM368_EXTENSIONS_PRO_PATH . 'elementor/block-pro/layouts-pro/grid_pro.php';

            return $files;
        }
    }

}

global $Beeteam368_Elementor_Addon_Blocks_Pro;
$Beeteam368_Elementor_Addon_Blocks_Pro = new Beeteam368_Elementor_Addon_Blocks_Pro();
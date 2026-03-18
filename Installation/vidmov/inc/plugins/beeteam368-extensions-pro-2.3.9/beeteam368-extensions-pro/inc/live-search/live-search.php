<?php
if (!class_exists('beeteam368_live_search_front_end')) {
    class beeteam368_live_search_front_end
    {
        public function __construct()
        {
            /*add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);*/
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);

            add_action('wp_ajax_live_search_request', array($this, 'query'));
            add_action('wp_ajax_nopriv_live_search_request', array($this, 'query'));
        }

        /*function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-live-search', $template_directory_uri . '/css/live-search/live-search.css', []);
            }
            return $values;
        }*/

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-live-search', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/live-search/assets/live-search.js', [], true);
            }
            return $values;
        }

        function query(){

            $result = array(
                'conn'=> '1',
                'result'=> '<span class="beeteam368-suggestion-item beeteam368-suggestion-item-dynamic flex-row-control flex-vertical-middle">
                                <span class="beeteam368-icon-item small-item"><i class="fas fa-search-minus"></i></span>
                                <span class="beeteam368-suggestion-item-content">
                                    <span class="beeteam368-suggestion-item-title h6 h-light">'.esc_html__('Sorry, no results were found.', 'beeteam368-extensions-pro').'</span>
                                </span>
                            </span>'
            );

            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false) || !isset($_POST['keyword']) || trim($_POST['keyword'])=='') {
                wp_send_json($result);
                return;
                die();
            }

            $keyword = trim($_POST['keyword']);

            $args_query = apply_filters('beeteam368_live_search_queries', array(
                'post_type'				=> apply_filters('beeteam368_live_search_post_type', array('post')),
                'posts_per_page' 		=> apply_filters('beeteam368_live_search_posts_per_page', 5),
                'post_status' 			=> 'publish',
                'ignore_sticky_posts' 	=> 1,
                's'                     => $keyword,
            ));

            $posts = get_posts($args_query);

            if($posts) {
                $html = '';
                foreach ($posts as $post){
                    ob_start();
                        $post_id = $post->ID;
                        $thumb = trim(beeteam368_post_thumbnail($post_id, apply_filters('beeteam368_post_thumbnail_params', array('size' => 'thumbnail', 'ratio' => 'img-1x1', 'position' => 'search_box_suggestion', 'html' => 'img-only', 'echo' => false), $post_id)));

                        $wordsToHighlight = explode(' ', $keyword);
						
						$post_type = get_post_type_object(get_post_type($post_id));
                        ?>
                        <a href="<?php echo esc_url(beeteam368_get_post_url($post_id))?>" class="beeteam368-suggestion-item beeteam368-suggestion-item-dynamic flex-row-control flex-vertical-middle">
                            <span class="beeteam368-icon-item small-item"><i class="fas fa-quote-left"></i></span>
                            <span class="beeteam368-suggestion-item-content">
                                <span class="beeteam368-suggestion-item-title h6 h-light"><?php echo preg_replace('/'.implode('|', $wordsToHighlight).'/i', '<span class="beeteam368-highlighted">$0</span>', get_the_title($post_id))?></span>
                                <span class="beeteam368-suggestion-item-tax font-size-10"><?php echo esc_html($post_type->labels->singular_name)?></span>
                            </span>
                            <?php
                            if($thumb != ''){
                                ?>
                                <span class="beeteam368-suggestion-item-image"><?php echo apply_filters('beeteam368_search_suggestion_results_items', $thumb);?></span>
                                <?php
                            }
                            ?>
                        </a>
                    <?php
                    $output_string = ob_get_contents();
                    ob_end_clean();
                    $html.= $output_string;
                }

                $result = array('conn'=> '1', 'result'=> $html);
                wp_send_json($result);

            }else{
                wp_send_json($result);
            }

            return;
            die();
        }
    }
}

global $beeteam368_live_search_front_end;
$beeteam368_live_search_front_end = new beeteam368_live_search_front_end();
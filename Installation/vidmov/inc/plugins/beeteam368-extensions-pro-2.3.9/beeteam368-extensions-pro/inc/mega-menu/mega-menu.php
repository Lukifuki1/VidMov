<?php
if (!class_exists('beeteam368_mega_menu', false)) {
    class beeteam368_mega_menu
    {
        public function __construct()
        {
            add_action('admin_init', array($this, 'beeteam368_register_filter_action_megamenu'));
            add_filter('beeteam368_css_party_files', array($this, 'css'), 10, 4);
            add_filter('beeteam368_js_party_files', array($this, 'js'), 10, 4);

            add_action('wp_ajax_megamenu_first_request', array($this, 'megamenu_first_request'));
            add_action('wp_ajax_nopriv_megamenu_first_request', array($this, 'megamenu_first_request'));

            add_action('wp_ajax_megamenu_blog_request', array($this, 'megamenu_blog_request'));
            add_action('wp_ajax_nopriv_megamenu_blog_request', array($this, 'megamenu_blog_request'));
        }

        function beeteam368_megamenu_fields_list()
        {
            return array(
                'beeteam368-megamenu-active' => esc_html__('Activate MegaMenu', 'beeteam368-extensions-pro'),
                'beeteam368-megamenu-extra_class' => esc_html__('Extra Class Name', 'beeteam368-extensions-pro'),
            );
        }

        function beeteam368_megamenu_fields($id, $item, $depth, $args)
        {

            $fields = $this->beeteam368_megamenu_fields_list();

            if (isset($depth) && is_numeric($depth) && $depth == 0) {
                foreach ($fields as $_key => $label) :
                    $key = sprintf('menu-item-%s', $_key);
                    $id = sprintf('edit-%s-%s', $key, $item->ID);
                    $name = sprintf('%s[%s]', $key, $item->ID);
                    $value = get_post_meta($item->ID, $key, true);
                    $class = sprintf('field-%s', $_key);
                    if ($_key == 'beeteam368-megamenu-extra_class') {
                        ?>
                        <p class="description description-wide <?php echo esc_attr($class) ?>">
                            <label for="<?php echo esc_attr($id); ?>">
                                <?php echo esc_attr($label); ?>
                                <br>
                                <input type="text" id="<?php echo esc_attr($id); ?>"
                                       class="widefat code edit-menu-item-url" name="<?php echo esc_attr($name); ?>"
                                       value="<?php echo esc_attr($value) ?>">
                            </label>
                        </p>
                        <?php
                    } else {
                        $checked_itb = ($value == 1) ? 'checked' : '';
                        ?>
                        <p class="description description-wide <?php echo esc_attr($class) ?>">
                            <label for="<?php echo esc_attr($id); ?>"><input type="checkbox"
                                                                             id="<?php echo esc_attr($id); ?>"
                                                                             name="<?php echo esc_attr($name); ?>"
                                                                             value="1" <?php echo esc_attr($checked_itb); ?> /><?php echo esc_attr($label); ?>
                            </label>
                        </p>
                        <?php
                    }
                endforeach;
            }
        }

        function beeteam368_megamenu_columns($columns)
        {
            $fields = $this->beeteam368_megamenu_fields_list();
            $columns = array_merge($columns, $fields);
            return $columns;
        }

        function beeteam368_megamenu_save($menu_id, $menu_item_db_id, $menu_item_args)
        {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return;
            }
            check_admin_referer('update-nav_menu', 'update-nav-menu-nonce');
            $fields = $this->beeteam368_megamenu_fields_list();
            foreach ($fields as $_key => $label) {

                $key = sprintf('menu-item-%s', $_key);

                if (!empty($_POST[$key][$menu_item_db_id])) {

                    $value = sanitize_text_field($_POST[$key][$menu_item_db_id]);
                } else {
                    $value = null;
                }

                if (!is_null($value)) {
                    update_post_meta($menu_item_db_id, $key, $value);
                } else {
                    delete_post_meta($menu_item_db_id, $key);
                }
            }
        }

        function beeteam368_megamenu_filter_walker($walker)
        {
            $walker = 'beeteam368_MegaMenu_Walker_Edit';
            if (!class_exists($walker, false)) {
                require_once BEETEAM368_EXTENSIONS_PRO_PATH . '/inc/mega-menu/menu-edit.php';
            }
            return $walker;
        }

        function beeteam368_register_filter_action_megamenu()
        {
            add_action('wp_nav_menu_item_custom_fields', array($this, 'beeteam368_megamenu_fields'), 10, 4);
            add_filter('manage_nav-menus_columns', array($this, 'beeteam368_megamenu_columns'), 99);
            add_action('wp_update_nav_menu_item', array($this, 'beeteam368_megamenu_save'), 10, 3);
            add_filter('wp_edit_nav_menu_walker', array($this, 'beeteam368_megamenu_filter_walker'), 99);
        }

        function css($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-megamenu', $template_directory_uri . '/css/header/mega-menu/mega-menu.css', []);
            }
            return $values;
        }

        function js($values, $beeteam368_header_style, $template_directory_uri, $beeteam368_theme_version)
        {
            if (is_array($values)) {
                $values[] = array('beeteam368-megamenu', BEETEAM368_EXTENSIONS_PRO_URL . 'inc/mega-menu/assets/mega-menu.js', [], true);
            }
            return $values;
        }

        function megamenu_first_request()
        {
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false)) {
                wp_send_json(array());
                die();
            }

            $taxs = $_POST['taxs'];

            if (!is_array($taxs)) {
                wp_send_json(array());
                die();
            }

            $return_query = [];

            foreach ($taxs as $tax) {
                if (is_array($tax) & count($tax) === 3) {

                    $items_per_page = apply_filters('beeteam368_megamenu_posts_per_page', 4);

                    $args_query = array(
                        'post_type' => apply_filters('beeteam368_megamenu_post_types', array('post', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio')),
                        'posts_per_page' => $items_per_page,
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                        'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => $tax[1],
                                'terms' => array($tax[2]),
                            ),
                        )
                    );

                    $query = new WP_Query($args_query);
                    if ($query->have_posts()):

                        $total_posts = apply_filters('beeteam368_megamenu_total_posts', 12);
                        $totalCountPosts = $query->found_posts;

                        if ($totalCountPosts > $total_posts) {
                            $totalCountPosts = $total_posts;
                        }

                        $allItems = $totalCountPosts;
                        $allItemsPerPage = $items_per_page;

                        if ($allItemsPerPage > $allItems) {
                            $allItemsPerPage = $allItems;
                        }

                        $paged_calculator = 1;
                        $percentItems = 0;

                        if ($allItems > $allItemsPerPage) {

                            $percentItems = ($allItems % $allItemsPerPage);

                            if ($percentItems != 0) {
                                $paged_calculator = (($allItems - $percentItems) / $allItemsPerPage) + 1;
                            } else {
                                $paged_calculator = ($allItems / $allItemsPerPage);
                            }
                        }

                        ob_start();
                        ?>
                        <div class="megamenu-blog-items megamenu-blog-items-control active site__row flex-row-control"
                             data-paged="1">
                            <?php
                            while ($query->have_posts()):
                                $query->the_post();
                                include BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/mega-menu/loop.php';
                            endwhile;
                            ?>
                        </div>
                        <div class='loading-container loading-control abslt'>
                            <div class='shape shape-1'></div>
                            <div class='shape shape-2'></div>
                            <div class='shape shape-3'></div>
                            <div class='shape shape-4'></div>
                        </div>
                        <?php
                        $output_string = ob_get_contents();
                        ob_end_clean();

                        $return_query[$tax[0]] = array('total_pages' => $paged_calculator, 'html' => $output_string);

                    endif;
                    wp_reset_postdata();
                }
            }

            wp_send_json($return_query);
            die();
        }

        function megamenu_blog_request()
        {
            $security = isset($_POST['security'])?sanitize_text_field($_POST['security']):'';
            if (!beeteam368_ajax_verify_nonce($security, false)) {
                return;
                die();
            }

            $tax = $_POST['tax'];
            $paged = $_POST['paged'];

            if (!is_array($tax)) {
                return;
                die();
            }

            $items_per_page = apply_filters('beeteam368_megamenu_posts_per_page', 4);

            $args_query = array(
                'post_type' => apply_filters('beeteam368_megamenu_post_types', array('post', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio')),
                'posts_per_page' => $items_per_page,
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => $tax[0],
                        'terms' => array($tax[1]),
                    ),
                ),
                'paged' => $paged,
            );

            $query = new WP_Query($args_query);

            if ($query->have_posts()):
                ob_start();
                ?>
                <div class="megamenu-blog-items megamenu-blog-items-control site__row flex-row-control"
                     data-paged="<?php echo esc_attr($paged) ?>">
                    <?php
                    while ($query->have_posts()):
                        $query->the_post();
                        include BEETEAM368_EXTENSIONS_PRO_PATH . 'inc/mega-menu/loop.php';
                    endwhile;
                    ?>
                </div>
                <?php
                $output_string = ob_get_contents();
                ob_end_clean();
            endif;
            wp_reset_postdata();

            echo $output_string;
            exit;
        }
    }
}

global $beeteam368_mega_menu;
$beeteam368_mega_menu = new beeteam368_mega_menu();

if (!class_exists('beeteam368_walkernav', false)):
    class beeteam368_walkernav extends Walker_Nav_Menu
    {
        public $megaMenuID;
        public $megaMenuHTML;

        public function __construct()
        {
            $this->megaMenuID = 0;
            $this->megaMenuHTML = '';
        }

        public function start_lvl(&$output, $depth = 0, $args = array())
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat($t, $depth);

            $classes = array('sub-menu beeteam368-megamenu-sub');

            $class_names = join(' ', apply_filters('nav_menu_submenu_css_class', $classes, $args, $depth));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            if ($this->megaMenuID != 0 && $depth > 0) {
                $output .= '';
            } else {
                if ($this->megaMenuID != 0 && $depth == 0) {
                    $output .= "{$n}{$indent}<ul$class_names><li class='megamenu-wrapper megamenu-wrapper-control site__container main__container-control'><div class='site__row'><ul class='megamenu-menu site__col'>{$n}";
                } else {
                    $output .= "{$n}{$indent}<ul$class_names>{$n}";
                }
            }
        }

        public function end_lvl(&$output, $depth = 0, $args = array())
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat($t, $depth);

            if ($this->megaMenuID != 0 && $depth > 0) {
                $output .= '';
            } else {
                if ($this->megaMenuID != 0 && $depth == 0) {
                    $post_html = $this->megaMenuHTML;
                    $this->megaMenuHTML = '';
                    $output .= "$indent</ul><ul class='megamenu-content site__col'><li class='loading-container loading-control abslt'><div class='shape shape-1'></div><div class='shape shape-2'></div><div class='shape shape-3'></div><div class='shape shape-4'></div></li><li>" . $post_html . "</li></ul></div></li></ul>{$n}";
                } else {
                    $output .= "$indent</ul>{$n}";
                }
            }
        }

        public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ($depth) ? str_repeat($t, $depth) : '';

            $classes = empty($item->classes) ? array() : (array)$item->classes;
            $classes[] = 'menu-item-' . $item->ID;

            $extraClassName = trim(get_post_meta($item->ID, 'menu-item-beeteam368-megamenu-extra_class', true));

            if ($extraClassName != '') {
                $classes[] = $extraClassName;
            }

            if ($this->megaMenuID != 0 && $this->megaMenuID != intval($item->menu_item_parent) && $depth == 0) {
                $this->megaMenuID = 0;
            }

            $hasMegaMenu = get_post_meta($item->ID, 'menu-item-beeteam368-megamenu-active', true);
            if ($depth == 0 && $hasMegaMenu) {
                $classes[] = 'beeteam368-megamenu beeteam368-megamenu-control';
                $this->megaMenuID = $item->ID;
            }

            $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

            $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
            $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

            $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
            $id = $id ? ' id="' . esc_attr($id) . '"' : '';

            if ($this->megaMenuID != 0 && $depth > 1) {
                $output .= '';
            } else {
                $output .= $indent . '<li' . $id . $class_names . '>';
            }

            $atts = array();
            $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
            $atts['target'] = !empty($item->target) ? $item->target : '';
            $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';
            $atts['href'] = !empty($item->url) ? $item->url : '';

            $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

            $attributes = '';
            foreach ($atts as $attr => $value) {
                if (!empty($value)) {
                    $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            $title = apply_filters('the_title', $item->title, $item->ID);

            if (isset($depth) && is_numeric($depth) && $depth == 0) {
                $title = '<span class="lvl1-counter">' . $title . '</span>';
            }

            $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

            $hasParentMegaMenu = false;
            if ($depth == 1) {
                if (isset($item->menu_item_parent)) {
                    $hasParentMegaMenu = get_post_meta($item->menu_item_parent, 'menu-item-beeteam368-megamenu-active', true);
                }

            }

            if ($hasParentMegaMenu) {
                $id_control = $item->object_id . '-' . rand(1, 99999);
                $item_output = '<a class="megamenu-item-heading megamenu-item-control" data-id="' . esc_attr($id_control) . '" href="' . esc_url($atts['href']) . '">' . esc_html($title) . '</a>';

                if (isset($item->type) && isset($item->object) && isset($item->object_id) && $item->type == 'taxonomy' && $item->object != '' && $item->object_id != 0) {
                    $html = '';

                    $args_query = array(
                        'post_type' => apply_filters('beeteam368_megamenu_post_types', array('post', BEETEAM368_POST_TYPE_PREFIX . '_video', BEETEAM368_POST_TYPE_PREFIX . '_audio')),
                        'posts_per_page' => 1,
                        'post_status' => 'publish',
                        'ignore_sticky_posts' => 1,
                        'tax_query' => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => $item->object,
                                'terms' => array($item->object_id),
                            ),
                        )
                    );

                    $posts = get_posts($args_query);
                    if ($posts) {
                        ob_start();
                        ?>
                        <div class="megamenu-blog-wrapper megamenu-blog-wrapper-control"
                             data-id="<?php echo esc_attr($id_control); ?>"
                             data-tax-object="<?php echo esc_attr($item->object); ?>"
                             data-tax-id="<?php echo esc_attr($item->object_id); ?>"></div>
                        <?php
                        $html = ob_get_contents();
                        ob_end_clean();
                    }

                    $this->megaMenuHTML .= $html;
                }

            } elseif ($this->megaMenuID != 0 && $depth > 1) {
                $item_output = '';
            } else {
                $argsBefore = (is_array($args) && isset($args['before'])) ? $args['before'] : (isset($args->before) ? $args->before : '');
                $argsAfter = (is_array($args) && isset($args['after'])) ? $args['after'] : (isset($args->after) ? $args->after : '');
                $argsLinkBefore = (is_array($args) && isset($args['link_before'])) ? $args['link_before'] : (isset($args->link_before) ? $args->link_before : '');
                $argsLinkAfter = (is_array($args) && isset($args['link_after'])) ? $args['link_after'] : (isset($args->link_after) ? $args->link_after : '');

                $item_output = $argsBefore;
                $item_output .= '<a' . $attributes . '>';
                $item_output .= $argsLinkBefore . $title . $argsLinkAfter;
                $item_output .= '</a>';
                $item_output .= $argsAfter;
            }

            $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
        }

        public function end_el(&$output, $item, $depth = 0, $args = array())
        {
            if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            if ($this->megaMenuID != 0 && $depth > 1) {
                $output .= '';
            } else {
                $output .= "</li>{$n}";
            }
        }
    }
endif;
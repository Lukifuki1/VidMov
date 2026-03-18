<?php
if (!function_exists( 'beeteam368_widget_form' ) ) :
    function beeteam368_widget_form($t, $return, $instance){

        $instance = wp_parse_args( (array) $instance, array( 'fontawesome_icon' => '', 'fontawesome_color' => '', 'extra_classname' => '') );

        if(!isset($instance['fontawesome_icon'])){
            $instance['fontawesome_icon'] = '';
        }
        if(!isset($instance['fontawesome_color'])){
            $instance['fontawesome_color'] = '';
        }
        if(!isset($instance['icon_background_color'])){
            $instance['icon_background_color'] = '';
        }
        if(!isset($instance['title_line_color'])){
            $instance['title_line_color'] = '';
        }
        if(!isset($instance['extra_classname'])){
            $instance['extra_classname'] = '';
        }

        ?>
        <p>
            <label for="<?php echo esc_attr($t->get_field_id('fontawesome_icon'));?>">
                <?php echo esc_html__( 'Widget Icon', 'beeteam368-extensions')?>
                <input class="widefat" id="<?php echo esc_attr($t->get_field_id('fontawesome_icon'));?>" name="<?php echo esc_attr($t->get_field_name('fontawesome_icon'));?>" type="text" value="<?php echo esc_attr($instance['fontawesome_icon']);?>" placeholder="<?php echo esc_attr__('e.g: fas fa-mitten', 'beeteam368-extensions');?>">
            </label>
        </p>

        <p>
            <label for="<?php echo esc_attr($t->get_field_id('fontawesome_color'));?>">
                <?php echo esc_html__( 'Widget Icon Color', 'beeteam368-extensions')?>
                <input class="widefat" id="<?php echo esc_attr($t->get_field_id('fontawesome_color'));?>" name="<?php echo esc_attr($t->get_field_name('fontawesome_color'));?>" type="text" value="<?php echo esc_attr($instance['fontawesome_color']);?>" placeholder="<?php echo esc_attr__('e.g: #FF0000', 'beeteam368-extensions');?>">
            </label>
        </p>

        <p>
            <label for="<?php echo esc_attr($t->get_field_id('icon_background_color'));?>">
                <?php echo esc_html__( 'Widget Icon Background Color', 'beeteam368-extensions')?>
                <input class="widefat" id="<?php echo esc_attr($t->get_field_id('icon_background_color'));?>" name="<?php echo esc_attr($t->get_field_name('icon_background_color'));?>" type="text" value="<?php echo esc_attr($instance['icon_background_color']);?>" placeholder="<?php echo esc_attr__('e.g: #F2F2F2', 'beeteam368-extensions');?>">
            </label>
        </p>

        <p>
            <label for="<?php echo esc_attr($t->get_field_id('title_line_color'));?>">
                <?php echo esc_html__( 'Widget Title Line Color', 'beeteam368-extensions')?>
                <input class="widefat" id="<?php echo esc_attr($t->get_field_id('title_line_color'));?>" name="<?php echo esc_attr($t->get_field_name('title_line_color'));?>" type="text" value="<?php echo esc_attr($instance['title_line_color']);?>" placeholder="<?php echo esc_attr__('e.g: rgba(52, 199, 89, 1.0)', 'beeteam368-extensions');?>">
            </label>
        </p>

        <p>
            <label for="<?php echo esc_attr($t->get_field_id('extra_classname'));?>">
                <?php echo esc_html__( 'Extra Class Name', 'beeteam368-extensions')?>
                <input class="widefat" id="<?php echo esc_attr($t->get_field_id('extra_classname'));?>" name="<?php echo esc_attr($t->get_field_name('extra_classname'));?>" type="text" value="<?php echo esc_attr($instance['extra_classname']);?>">
            </label>
        </p>
        <?php
        return array($t, $return, $instance);
    }
endif;
add_action('in_widget_form', 'beeteam368_widget_form', 5, 3);

if (!function_exists( 'beeteam368_widget_form_update' ) ) :
    function beeteam368_widget_form_update($instance, $new_instance, $old_instance){
        $instance['fontawesome_icon'] = strip_tags(trim($new_instance['fontawesome_icon']));
        $instance['fontawesome_color'] = strip_tags(trim($new_instance['fontawesome_color']));
        $instance['icon_background_color'] = strip_tags(trim($new_instance['icon_background_color']));
        $instance['title_line_color'] = strip_tags(trim($new_instance['title_line_color']));
        $instance['extra_classname'] = strip_tags(trim($new_instance['extra_classname']));
        return $instance;
    }
endif;
add_filter('widget_update_callback', 'beeteam368_widget_form_update', 5, 3);

if (!function_exists('beeteam368_dynamic_sidebar_params')):
    function beeteam368_dynamic_sidebar_params($params){
        global $wp_registered_widgets;
        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option($widget_obj['callback'][0]->option_name);
        $widget_num = $widget_obj['params'][0]['number'];

        $class = '';
        $aweicon = '';
        $awecolor = '';
        $awebgcolor = '';
        $linecolor = '';

        if(isset($widget_opt[$widget_num]['extra_classname']) && $widget_opt[$widget_num]['extra_classname'] != ''){
            $class.= ' '.$widget_opt[$widget_num]['extra_classname'];
        }

        if(isset($widget_opt[$widget_num]['fontawesome_icon']) && $widget_opt[$widget_num]['fontawesome_icon'] != ''){
            $aweicon.= ' '.$widget_opt[$widget_num]['fontawesome_icon'];
        }

        if(isset($widget_opt[$widget_num]['fontawesome_color']) && $widget_opt[$widget_num]['fontawesome_color'] != ''){
            $awecolor.= ' '.$widget_opt[$widget_num]['fontawesome_color'];
        }

        if(isset($widget_opt[$widget_num]['icon_background_color']) && $widget_opt[$widget_num]['icon_background_color'] != ''){
            $awebgcolor.= ' '.$widget_opt[$widget_num]['icon_background_color'];
        }

        if(isset($widget_opt[$widget_num]['title_line_color']) && $widget_opt[$widget_num]['title_line_color'] != ''){
            $linecolor.= ' '.$widget_opt[$widget_num]['title_line_color'];
        }

        $df_icon_replace = array('fas fa-heart', 'fas fa-feather-alt', 'fas fa-folder');

        if($class != ''){
            $params[0] = array_replace($params[0], array('before_widget' => str_replace('r-widget-control', 'r-widget-control'.esc_attr($class), $params[0]['before_widget'])));
        }

        if($aweicon != ''){
            $params[0] = array_replace($params[0], array('before_title' => str_replace(array('fas fa-heart', 'fas fa-feather-alt', 'fas fa-cogs'), esc_attr($aweicon), $params[0]['before_title'])));
        }

        $icon_color = array('color' => '', 'background' => '', 'line' => '');

        if($awecolor != ''){
            $icon_color['color'] = $awecolor;
        }

        if($awebgcolor != ''){
            $icon_color['background'] = $awebgcolor;
        }

        if($linecolor != ''){
            $icon_color['line'] = $linecolor;
        }

        if($icon_color['color'] !='' || $icon_color['background'] !='' ){
            $add_parrr = apply_filters('beeteam368_widget_icon_color', '', $icon_color, $widget_id);
            $params[0] = array_replace($params[0], array('before_title' => str_replace('class="beeteam368-icon-item"', 'class="beeteam368-icon-item"'.apply_filters('beeteam368_add_parrr_widget', $add_parrr), $params[0]['before_title'])));
        }

        if($icon_color['line'] !=''){
            $add_parrl = apply_filters('beeteam368_widget_line_color', '', $icon_color, $widget_id);
            $params[0] = array_replace($params[0], array('after_title' => str_replace('class="wg-line"', 'class="wg-line"'.apply_filters('beeteam368_add_parrl_widget', $add_parrl), $params[0]['after_title'])));
        }

        return $params;
    }
endif;
add_filter('dynamic_sidebar_params', 'beeteam368_dynamic_sidebar_params');

if (!function_exists('beeteam368_widget_icon_color')):
    function beeteam368_widget_icon_color($values, $icon_color, $widget_id){
        if(is_array($icon_color) && ( $icon_color['color'] !='' || $icon_color['background'] != '' )){
            $style = '';
            if($icon_color['color'] != ''){
                $style.='color:'.esc_attr($icon_color['color']).';';
            }
            if($icon_color['background'] != ''){
                $style.='background-color:'.esc_attr($icon_color['background']).';';
            }
            if($style != ''){
                return ' style="'.esc_attr($style).'"';
            }
        }
        return $values;
    }
endif;
add_filter('beeteam368_widget_icon_color', 'beeteam368_widget_icon_color', 10, 3);

if (!function_exists('beeteam368_widget_line_color')):
    function beeteam368_widget_line_color($values, $icon_color, $widget_id){
        if(is_array($icon_color) && $icon_color['line'] !=''){
            $style = '';
            $style.='background-color:'.esc_attr($icon_color['line']).';';
            if($style != ''){
                return ' style="'.esc_attr($style).'"';
            }
        }
        return $values;
    }
endif;
add_filter('beeteam368_widget_line_color', 'beeteam368_widget_line_color', 10, 3);

if(!class_exists('beeteam368_widget_post')):
	class beeteam368_widget_post extends WP_Widget {
		function __construct() {			
			parent::__construct( 'beeteam368_post_extensions', esc_html__('VidMov - Post Extensions', 'beeteam368-extensions'), array('classname' => 'vidmov-post-extensions') );
		}
		
		function widget( $args, $instance ) {
			extract($args);
			
			$title = isset($instance['title'])?trim($instance['title']):'';
			$block_layout = isset($instance['block_layout'])?trim($instance['block_layout']):'classic';
			
			$display_author	= (isset($instance['display_author']) && trim($instance['display_author']) !='') ? trim($instance['display_author']) : 'yes';
			$display_excerpt = (isset($instance['display_excerpt']) && trim($instance['display_excerpt']) !='') ?trim($instance['display_excerpt']) : 'yes';
			$display_post_categories = (isset($instance['display_post_categories']) && trim($instance['display_post_categories']) !='') ? trim($instance['display_post_categories']) : 'yes';
			$display_post_published_date = (isset($instance['display_post_published_date']) && trim($instance['display_post_published_date']) != '') ? trim($instance['display_post_published_date']) : 'yes';
			$display_post_updated_date = (isset($instance['display_post_updated_date']) && trim($instance['display_post_updated_date']) !='') ? trim($instance['display_post_updated_date']) : 'no';
			$display_post_reactions = (isset($instance['display_post_reactions']) && trim($instance['display_post_reactions']) !='') ? trim($instance['display_post_reactions']) : 'yes';
			$display_post_comments = (isset($instance['display_post_comments']) && trim($instance['display_post_comments']) !='') ? trim($instance['display_post_comments']) : 'yes';
			$display_post_views = (isset($instance['display_post_views']) && trim($instance['display_post_views']) !='') ? trim($instance['display_post_views']) : 'yes';			
			$display_duration = (isset($instance['display_duration']) && trim($instance['display_duration']) !='') ? trim($instance['display_duration']) : 'yes';
			$display_tag_label = (isset($instance['display_tag_label']) && trim($instance['display_tag_label']) !='') ? trim($instance['display_tag_label']) : 'yes';			
			$display_post_read_more = (isset($instance['display_post_read_more']) && trim($instance['display_post_read_more']) !='') ? trim($instance['display_post_read_more']) : 'yes';
			$image_ratio = (isset($instance['image_ratio']) && trim($instance['image_ratio']) !='') ? trim($instance['image_ratio']) : '';
			
			$post_type = (isset($instance['post_type']) && trim($instance['post_type'])!='') ? trim($instance['post_type']) : BEETEAM368_POST_TYPE_PREFIX . '_video';
			$post_type = array($post_type);
			
			$filter_items = (isset($instance['filter_items'])&&trim($instance['filter_items'])!='')?trim($instance['filter_items']):'';
			$category = (isset($instance['category'])&&trim($instance['category'])!='')?trim($instance['category']):'';
			$tag = (isset($instance['tag'])&&trim($instance['tag'])!='')?trim($instance['tag']):'';
			$ex_category = (isset($instance['ex_category'])&&trim($instance['ex_category'])!='')?trim($instance['ex_category']):'';
			$ids = (isset($instance['ids'])&&trim($instance['ids'])!='')?trim($instance['ids']):'';
			$offset = (isset($instance['offset'])&&trim($instance['offset'])!=''&&is_numeric(trim($instance['offset'])))?trim($instance['offset']):0;
			$order_by = (isset($instance['order_by'])&&trim($instance['order_by'])!='')?trim($instance['order_by']):'date';
			$order = (isset($instance['order'])&&trim($instance['order'])!='')?trim($instance['order']):'DESC';
			$items_per_page = (isset($instance['items_per_page']) && is_numeric($instance['items_per_page']) ) ? (float)$instance['items_per_page'] : 10;
			$post_count = (isset($instance['post_count']) && is_numeric($instance['post_count']) != '') ? (float)$instance['post_count'] : 20;			
			$pagination = (isset($instance['pagination']) && trim($instance['pagination']) != '') ? trim($instance['pagination']) : 'loadmore-btn';
			
			$params = array();
			
			$params['title'] = $title;
			$params['block_layout'] = $block_layout;
			$params['overwrite_block_layout'] = '/template-parts/archive/item-' . $params['block_layout'] . '.php';
			
			$params['display_author'] = $display_author;
			$params['display_excerpt'] = $display_excerpt;
			$params['display_post_categories'] = $display_post_categories;
			$params['display_post_published_date'] = $display_post_published_date;
			$params['display_post_updated_date'] = $display_post_updated_date;
			$params['display_post_reactions'] = $display_post_reactions;
			$params['display_post_comments'] = $display_post_comments;
			$params['display_post_views'] = $display_post_views;			
			$params['display_duration'] = $display_duration;
			$params['display_tag_label'] = $display_tag_label;			
			$params['display_post_read_more'] = $display_post_read_more;
			
			$params['image_ratio'] = $image_ratio;
			
			$params['post_type'] = $post_type;
			
			$params['filter_mode'] = 'yes';
			$params['filter_items'] = $filter_items;
			
			$params['category'] = $category;
			$params['tag'] = $tag;
			$params['ex_category'] = $ex_category;
			$params['ids'] = $ids;
			$params['offset'] = $offset;
			$params['order_by'] = $order_by;
			$params['order'] = $order;
			$params['items_per_page'] = $items_per_page;
			$params['post_count'] = $post_count;
			$params['pagination'] = $pagination;
			
			$widget_html = '';			
			
			
			ob_start();
			
				global $beeteam368_hide_element_id_tag;
				$beeteam368_hide_element_id_tag = 'hide';
			
				global $beeteam368_overwrite_loop_layout_blocks;
				$beeteam368_overwrite_loop_layout_blocks = $params['overwrite_block_layout'];
				
				global $beeteam368_pag_type_stand_alone;
				$beeteam368_pag_type_stand_alone = $params['pagination'];
				
				Beeteam368_Elementor_Addons_Elements::beeteam368_get_elements_block($params);	
				
				$beeteam368_pag_type_stand_alone = NULL;			
				$beeteam368_overwrite_loop_layout_blocks = NULL;				
				$beeteam368_hide_element_id_tag = NULL;
				
			$output_string = ob_get_contents();
			ob_end_clean();	
			
			if(trim($output_string)!=''){
				$widget_html.= $before_widget . $before_title . $title . $after_title . $output_string . $after_widget;
				wp_enqueue_script('beeteam368-script-block', BEETEAM368_EXTENSIONS_URL . 'elementor/assets/block/block.js', ['jquery'], BEETEAM368_EXTENSIONS_VER, true);
			}
			
			echo apply_filters( 'beeteam368_widget_posts_html', $widget_html );
		}
		
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = esc_attr(trim($new_instance['title']));
			$instance['block_layout'] = esc_attr(trim($new_instance['block_layout']));
			
			$instance['display_author'] = esc_attr(trim($new_instance['display_author']));
			$instance['display_excerpt'] = esc_attr(trim($new_instance['display_excerpt']));
			$instance['display_post_categories'] = esc_attr(trim($new_instance['display_post_categories']));
			$instance['display_post_published_date'] = esc_attr(trim($new_instance['display_post_published_date']));
			$instance['display_post_updated_date'] = esc_attr(trim($new_instance['display_post_updated_date']));
			$instance['display_post_reactions'] = esc_attr(trim($new_instance['display_post_reactions']));
			$instance['display_post_comments'] = esc_attr(trim($new_instance['display_post_comments']));
			$instance['display_post_views'] = esc_attr(trim($new_instance['display_post_views']));
			$instance['display_duration'] = esc_attr(trim($new_instance['display_duration']));
			$instance['display_tag_label'] = esc_attr(trim($new_instance['display_tag_label']));
			$instance['display_post_read_more'] = esc_attr(trim($new_instance['display_post_read_more']));
			
			$instance['image_ratio'] = esc_attr(trim($new_instance['image_ratio']));
			
			$instance['post_type'] = esc_attr(trim($new_instance['post_type']));
			$instance['filter_items'] = esc_attr(trim($new_instance['filter_items']));
			$instance['category'] = esc_attr(trim($new_instance['category']));
			$instance['tag'] = esc_attr(trim($new_instance['tag']));
			$instance['ex_category'] = esc_attr(trim($new_instance['ex_category']));
			$instance['ids'] = esc_attr(trim($new_instance['ids']));
			$instance['offset'] = esc_attr(trim($new_instance['offset']));
			$instance['order_by'] = esc_attr(trim($new_instance['order_by']));
			$instance['order'] = esc_attr(trim($new_instance['order']));
			$instance['items_per_page'] = esc_attr(trim($new_instance['items_per_page']));
			$instance['post_count'] = esc_attr(trim($new_instance['post_count']));
			$instance['pagination'] = esc_attr(trim($new_instance['pagination']));
			
			return $instance;
		}
		
		function form( $instance ) {
			$val = array(
				'title' => esc_html__('Posts', 'beeteam368-extensions'),
				'block_layout' => 'classic',
				
				'display_author' => 'yes',
				'display_excerpt' => 'yes',
				'display_post_categories' => 'yes',			
				'display_post_published_date' => 'yes',
				'display_post_updated_date' => 'no',
				'display_post_reactions' => 'yes',
				'display_post_comments' => 'yes',
				'display_post_views' => 'yes',	
				'display_duration' => 'yes',	
				'display_tag_label' => 'yes',	
				'display_post_read_more' => 'yes',	
				'image_ratio' => '',
					
				'post_type' => BEETEAM368_POST_TYPE_PREFIX . '_video',
				'filter_items' => '',
				'category' => '',
				'tag' => '',
				'ex_category' => '',
				'ids' => '',
				'offset' => 0,
				'order_by' => 'date',
				'order' => 'DESC',
				'items_per_page' => 10,
				'post_count' => 20,
				'pagination' => 'loadmore-btn',			
			);
			
			$instance = wp_parse_args((array) $instance, $val);
			
			$title = esc_attr(trim($instance['title']));
			$block_layout = esc_attr(trim($instance['block_layout']));
			
			$display_author	= esc_attr(trim($instance['display_author']));
			$display_excerpt = esc_attr(trim($instance['display_excerpt']));
			$display_post_categories = esc_attr(trim($instance['display_post_categories']));
			$display_post_published_date = esc_attr(trim($instance['display_post_published_date']));
			$display_post_updated_date = esc_attr(trim($instance['display_post_updated_date']));
			$display_post_reactions = esc_attr(trim($instance['display_post_reactions']));
			$display_post_comments = esc_attr(trim($instance['display_post_comments']));
			$display_post_views = esc_attr(trim($instance['display_post_views']));
			$display_duration = esc_attr(trim($instance['display_duration']));
			$display_tag_label = esc_attr(trim($instance['display_tag_label']));
			$display_post_read_more = esc_attr(trim($instance['display_post_read_more']));
			$image_ratio = esc_attr(trim($instance['image_ratio']));
			
			$post_type = esc_attr($instance['post_type']);
			$filter_items = esc_attr(trim($instance['filter_items']));
			$category = esc_attr(trim($instance['category']));
			$tag = esc_attr(trim($instance['tag']));
			$ex_category = esc_attr(trim($instance['ex_category']));
			$ids = esc_attr(trim($instance['ids']));
			$offset = esc_attr(trim($instance['offset']));
			$order_by = esc_attr(trim($instance['order_by']));
			$order = esc_attr(trim($instance['order']));
			$items_per_page = esc_attr(trim($instance['items_per_page']));
			$post_count = esc_attr(trim($instance['post_count']));			
			$pagination = esc_attr(trim($instance['pagination']));
			
			ob_start();			
			?>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('title'));?>"><?php echo esc_html__('Title', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('title'));?>" name="<?php echo esc_attr($this->get_field_name('title'));?>" value="<?php echo esc_attr($title);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('block_layout'));?>"><?php echo esc_html__('Layout', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('block_layout'));?>" name="<?php echo esc_attr($this->get_field_name('block_layout'));?>">
                        <option value="widget-classic"<?php if($block_layout=='widget-classic'){echo ' selected';}?>><?php echo esc_html__('Classic', 'beeteam368-extensions');?></option>
                        <option value="widget-special"<?php if($block_layout=='widget-special'){echo ' selected';}?>><?php echo esc_html__('Special', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <?php 
				$all_post_types = apply_filters('beeteam368_elementor_block_post_types', [
					BEETEAM368_POST_TYPE_PREFIX . '_video' 		=> esc_html__('Video Posts', 'beeteam368-extensions'),
					BEETEAM368_POST_TYPE_PREFIX . '_audio' 		=> esc_html__('Audio Posts', 'beeteam368-extensions'),                                    
					'post' 			        					=> esc_html__('WordPress Posts', 'beeteam368-extensions'),
				]);
				?>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('post_type'));?>"><?php echo esc_html__('Post Type', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('post_type'));?>" name="<?php echo esc_attr($this->get_field_name('post_type'));?>">
                    	<?php 
						foreach($all_post_types as $key=>$value){
						?>
                        	<option value="<?php echo esc_attr($key);?>"<?php if($key==$post_type){echo ' selected';}?>><?php echo esc_html($value);?></option>
                        <?php
						}
						?>				
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('filter_items'));?>"><?php echo esc_html__('Filter Items', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('filter_items'));?>" name="<?php echo esc_attr($this->get_field_name('filter_items'));?>" placeholder="<?php echo esc_attr__('Enter categories, tags (id or slug) be shown in the filter list.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($filter_items);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('category'));?>"><?php echo esc_html__('Include categories', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('category'));?>" name="<?php echo esc_attr($this->get_field_name('category'));?>" placeholder="<?php echo esc_attr__('Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions');?>" value="<?php echo esc_attr($category);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('tag'));?>"><?php echo esc_html__('Include tags', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('tag'));?>" name="<?php echo esc_attr($this->get_field_name('tag'));?>" placeholder="<?php echo esc_attr__('Enter tag id or slug, eg: 19, 368, ...', 'beeteam368-extensions');?>" value="<?php echo esc_attr($tag);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('ex_category'));?>"><?php echo esc_html__('Exclude categories', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('ex_category'));?>" name="<?php echo esc_attr($this->get_field_name('ex_category'));?>" placeholder="<?php echo esc_attr__('Enter category id or slug, eg: 245, 126, ...', 'beeteam368-extensions');?>" value="<?php echo esc_attr($ex_category);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('ids'));?>"><?php echo esc_html__('Include Posts', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('ids'));?>" name="<?php echo esc_attr($this->get_field_name('ids'));?>" placeholder="<?php echo esc_attr__('Enter post id, eg: 1136, 2251, ...', 'beeteam368-extensions');?>" value="<?php echo esc_attr($ids);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('offset'));?>"><?php echo esc_html__('Offset', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('offset'));?>" name="<?php echo esc_attr($this->get_field_name('offset'));?>" placeholder="<?php echo esc_attr__('Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination. Therefore, the Pagination button will be hidden when you use this parameter.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($offset);?>">
                </p>
                
                <?php 
				$all_order_bys = apply_filters('beeteam368_order_by_custom_query', [
					'date' 			=> esc_html__('Date', 'beeteam368-extensions'),																		
					'ID' 			=> esc_html__('Order by post ID', 'beeteam368-extensions'),
					'author' 		=> esc_html__('Author', 'beeteam368-extensions'),
					'title' 		=> esc_html__('Title', 'beeteam368-extensions'),
					'modified' 		=> esc_html__('Last modified date', 'beeteam368-extensions'),
					'parent' 		=> esc_html__('Post/page parent ID', 'beeteam368-extensions'),
					'comment_count' => esc_html__('Number of comments', 'beeteam368-extensions'),
					'menu_order' 	=> esc_html__('Menu order/Page Order', 'beeteam368-extensions'),
					'rand' 			=> esc_html__('Random order', 'beeteam368-extensions'),																				
					'post__in' 		=> esc_html__('Preserve post ID order', 'beeteam368-extensions'),										
				]);
				?>
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('order_by'));?>"><?php echo esc_html__('Order By', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('order_by'));?>" name="<?php echo esc_attr($this->get_field_name('order_by'));?>">
                    	<?php 
						foreach($all_order_bys as $key=>$value){
						?>
                        	<option value="<?php echo esc_attr($key);?>"<?php if($key==$order_by){echo ' selected';}?>><?php echo esc_html($value);?></option>
                        <?php
						}
						?>				
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('order'));?>"><?php echo esc_html__('Order', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('order'));?>" name="<?php echo esc_attr($this->get_field_name('order'));?>">
                        <option value="DESC"<?php if($order=='DESC'){echo ' selected';}?>><?php echo esc_html__('DESC', 'beeteam368-extensions');?></option>
                        <option value="ASC"<?php if($order=='ASC'){echo ' selected';}?>><?php echo esc_html__('ASC', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('items_per_page'));?>"><?php echo esc_html__('Items Per Page', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('items_per_page'));?>" name="<?php echo esc_attr($this->get_field_name('items_per_page'));?>" placeholder="<?php echo esc_attr__('Number of items to show per page.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($items_per_page);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('post_count'));?>"><?php echo esc_html__('Posts Count', 'beeteam368-extensions');?></label>
                    <input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('post_count'));?>" name="<?php echo esc_attr($this->get_field_name('post_count'));?>" placeholder="<?php echo esc_attr__('Set max limit for items in grid or enter -1 to display all.', 'beeteam368-extensions');?>" value="<?php echo esc_attr($post_count);?>">
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('pagination'));?>"><?php echo esc_html__('Pagination', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('pagination'));?>" name="<?php echo esc_attr($this->get_field_name('pagination'));?>">
                        <option value="loadmore-btn"<?php if($pagination=='loadmore-btn'){echo ' selected';}?>><?php echo esc_html__('Load More Button (Ajax)', 'beeteam368-extensions');?></option>
                        <option value="infinite-scroll"<?php if($pagination=='infinite-scroll'){echo ' selected';}?>><?php echo esc_html__('Infinite Scroll (Ajax)', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_author'));?>"><?php echo esc_html__('Display Post Author', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_author'));?>" name="<?php echo esc_attr($this->get_field_name('display_author'));?>">
                        <option value="yes"<?php if($display_author=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_author=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_excerpt'));?>"><?php echo esc_html__('Display Post Excerpt', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_excerpt'));?>" name="<?php echo esc_attr($this->get_field_name('display_excerpt'));?>">
                        <option value="yes"<?php if($display_excerpt=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_excerpt=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_categories'));?>"><?php echo esc_html__('Display Post Categories', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_categories'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_categories'));?>">
                        <option value="yes"<?php if($display_post_categories=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_categories=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_published_date'));?>"><?php echo esc_html__('Display Post Published Date', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_published_date'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_published_date'));?>">
                        <option value="yes"<?php if($display_post_published_date=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_published_date=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_updated_date'));?>"><?php echo esc_html__('Display Post Last Updated', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_updated_date'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_updated_date'));?>">
                        <option value="yes"<?php if($display_post_updated_date=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_updated_date=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_reactions'));?>"><?php echo esc_html__('Display Post Reactions', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_reactions'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_reactions'));?>">
                        <option value="yes"<?php if($display_post_reactions=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_reactions=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_comments'));?>"><?php echo esc_html__('Display Post Comments Count', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_comments'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_comments'));?>">
                        <option value="yes"<?php if($display_post_comments=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_comments=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_views'));?>"><?php echo esc_html__('Display Post Views Count', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_views'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_views'));?>">
                        <option value="yes"<?php if($display_post_views=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_views=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_duration'));?>"><?php echo esc_html__('Display Duration', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_duration'));?>" name="<?php echo esc_attr($this->get_field_name('display_duration'));?>">
                        <option value="yes"<?php if($display_duration=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_duration=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_tag_label'));?>"><?php echo esc_html__('Display Tag Label', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_tag_label'));?>" name="<?php echo esc_attr($this->get_field_name('display_tag_label'));?>">
                        <option value="yes"<?php if($display_tag_label=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_tag_label=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('display_post_read_more'));?>"><?php echo esc_html__('Display Post Read More ( or: Share)', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('display_post_read_more'));?>" name="<?php echo esc_attr($this->get_field_name('display_post_read_more'));?>">
                        <option value="yes"<?php if($display_post_read_more=='yes'){echo ' selected';}?>><?php echo esc_html__('YES', 'beeteam368-extensions');?></option>
                        <option value="no"<?php if($display_post_read_more=='no'){echo ' selected';}?>><?php echo esc_html__('NO', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
                
                 <p>
                    <label for="<?php echo esc_attr($this->get_field_id('image_ratio'));?>"><?php echo esc_html__('Image Ratio', 'beeteam368-extensions');?></label>
                    <select class="widefat" id="<?php echo esc_attr($this->get_field_id('image_ratio'));?>" name="<?php echo esc_attr($this->get_field_name('image_ratio'));?>">
                        <option value=""<?php if($image_ratio==''){echo ' selected';}?>><?php echo esc_html__('Default', 'beeteam368-extensions');?></option>
                        <option value="16:9"<?php if($image_ratio=='16:9'){echo ' selected';}?>><?php echo esc_html__('Ratio [16:9', 'beeteam368-extensions');?></option>	
                        <option value="4:3"<?php if($image_ratio=='4:3'){echo ' selected';}?>><?php echo esc_html__('Ratio [4:3]', 'beeteam368-extensions');?></option>	
                        <option value="1:1"<?php if($image_ratio=='1:1'){echo ' selected';}?>><?php echo esc_html__('Ratio [1:1]', 'beeteam368-extensions');?></option>	
                        <option value="2:3"<?php if($image_ratio=='2:3'){echo ' selected';}?>><?php echo esc_html__('Ratio [2:3]', 'beeteam368-extensions');?></option>					
                    </select>
                </p>
			
			<?php			
			$output_string = ob_get_contents();
			ob_end_clean();
			
			echo apply_filters( 'beeteam368_admin_widget_posts_html', $output_string );
		}
	}
endif;

if(!function_exists('beeteam368_register_widgets')):
	function beeteam368_register_widgets() {
		register_widget( 'beeteam368_widget_post' );
	}
endif;	
add_action( 'widgets_init', 'beeteam368_register_widgets' );
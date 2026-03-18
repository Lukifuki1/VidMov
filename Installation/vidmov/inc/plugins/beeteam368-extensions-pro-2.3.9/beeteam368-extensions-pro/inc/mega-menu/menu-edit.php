<?php
if (!class_exists('beeteam368_MegaMenu_Walker_Edit', false)):
    class beeteam368_MegaMenu_Walker_Edit extends Walker_Nav_Menu_Edit
    {

        function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
        {
            $item_output = '';
            parent::start_el($item_output, $item, $depth, $args, $id);
            $output .= preg_replace(
                '/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/',
                $this->get_fields($item, $depth, $args),
                $item_output
            );
        }

        protected function get_fields($item, $depth, $args = array(), $id = 0)
        {
            ob_start();
            return ob_get_clean();
        }
    }
endif;
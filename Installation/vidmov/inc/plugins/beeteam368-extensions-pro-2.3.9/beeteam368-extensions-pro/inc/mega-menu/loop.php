<article <?php post_class('post-item site__col'); ?>>
    <div class="post-item-wrap">
        <?php beeteam368_post_thumbnail(get_the_ID(), array('size' => 'beeteam368_thumb_16x9_1x', 'ratio' => 'img-16x9', 'position' => 'mega-menu', 'html' => 'full'));?>

        <?php do_action('beeteam368_post_listing_top_meta', get_the_ID(), apply_filters('beeteam368_post_listing_top_meta_params', array('style' => 'default', 'position' => 'mega-menu', 'show_author' => false,), get_the_ID())); ?>

        <?php do_action('beeteam368_post_listing_title', get_the_ID(), apply_filters('beeteam368_post_listing_title_params', array('style' => 'mega-menu', 'heading' => 'h3', 'heading_class' => 'h5 h6-mobile max-2lines', 'position' => 'mega-menu'), get_the_ID())); ?>
    </div>
</article>
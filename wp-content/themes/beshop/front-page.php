<?php

/**
 * Template Name: Front Page
 */
get_header();


$beshop_blog_container = get_theme_mod('beshop_blog_container', 'container');
$beshop_blog_layout = get_theme_mod('beshop_blog_layout', 'rightside');
$beshop_shopbanner_show = get_theme_mod('beshop_shopbanner_show');
$beshop_banner_subtext = get_theme_mod('beshop_banner_subtext');
$beshop_banner_title = get_theme_mod('beshop_banner_title');
$beshop_banner_desc = get_theme_mod('beshop_banner_desc');
$beshop_banner_btn = get_theme_mod('beshop_banner_btn', esc_html__('Shop Now', 'beshop'));
$beshop_banner_url = get_theme_mod('beshop_banner_url', '#');
$beshop_text_position = get_theme_mod('beshop_text_position', 'left');
$beshop_banner_overlay = get_theme_mod('beshop_banner_overlay');


if (is_active_sidebar('sidebar-1') && $beshop_blog_layout != 'fullwidth') {
    $beshop_column_set = '9';
} else {
    $beshop_column_set = '12';
}

?>
<?php if ($beshop_shopbanner_show) : ?>
    <div class="beshop-banner bg-overlay mb-5">
        <div class="container">
            <div class="bbanner-text text-<?php echo esc_attr($beshop_text_position); ?>">
                <h4><?php echo esc_html($beshop_banner_subtext); ?></h4>
                <h1><?php echo esc_html($beshop_banner_title); ?></h1>
                <?php echo apply_filters('the_content', $beshop_banner_desc); ?>
                <?php if ($beshop_banner_url) : ?>
                    <div class="bsbanner-btn">
                        <a href="<?php echo esc_url($beshop_banner_url); ?>" class="btn bshop-btn"><?php echo esc_html($beshop_banner_btn); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($beshop_banner_overlay) : ?>
            <div class="overlay-banner"></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="<?php echo esc_attr($beshop_blog_container); ?> <?php if ($beshop_shopbanner_show) : ?>mb-5 pb-3<?php else : ?>mt-3 mb-5 pt-5 pb-3<?php endif; ?>">
    <?php
    if (beshop_is_woocommerce_activated() && beshop_has_woocommerce_products()) :
    ?>
        <main id="primary" class="site-main">
            <?php get_template_part('template-parts/content', 'fshop'); ?>
        </main>
    <?php else : ?>
        <div class="row">
            <?php if (is_active_sidebar('sidebar-1') && $beshop_blog_layout == 'leftside') : ?>
                <div class="col-lg-3">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
            <div class="col-lg-<?php echo esc_attr($beshop_column_set); ?>">
                <main id="primary" class="site-main">

                    <?php
                    if (have_posts()) :
                        /* Start the Loop */
                        while (have_posts()) :
                            the_post();
                            get_template_part('template-parts/content', get_post_type());

                        endwhile;

                        the_posts_pagination();

                    else :

                        get_template_part('template-parts/content', 'none');

                    endif;
                    ?>

                </main><!-- #main -->
            </div>
            <?php if (is_active_sidebar('sidebar-1') && $beshop_blog_layout == 'rightside') : ?>
                <div class="col-lg-3">
                    <?php get_sidebar(); ?>
                </div>
            <?php endif; ?>
        </div> <!-- end row -->
    <?php endif; ?>
</div> <!-- end container -->

<?php
get_footer();

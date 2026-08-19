<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BeShop
 */

get_header();


$beshop_blog_container = get_theme_mod('beshop_blog_container', 'container');
$beshop_blog_layout = get_theme_mod('beshop_blog_layout', 'rightside');

if (is_active_sidebar('sidebar-1') && $beshop_blog_layout != 'fullwidth') {
	$beshop_column_set = '9';
} else {
	$beshop_column_set = '12';
}

?>
<div class="<?php echo esc_attr($beshop_blog_container); ?> mt-3 mb-5 pt-5 pb-3">
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

					if (is_home() && !is_front_page()) :
				?>
						<header>
							<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
						</header>
					<?php
					endif;
					$beshopfree_blog_style = get_theme_mod('beshopfree_blog_style', 'style4');

					if ($beshopfree_blog_style == 'style4' && (!is_single())) :
					?>
						<div class="row" data-masonry='{"percentPosition": true }'>

						<?php
					endif;

					/* Start the Loop */
					while (have_posts()) :
						the_post();

						/*
						 * Include the Post-Type-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
						 */
						get_template_part('template-parts/content', get_post_type());

					endwhile;
					if ($beshopfree_blog_style == 'style4' && (!is_single())) :
						?>
						</div>
				<?php
					endif;
					the_posts_navigation();
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
</div> <!-- end container -->

<?php
get_footer();

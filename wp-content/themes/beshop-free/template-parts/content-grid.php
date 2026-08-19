<?php

/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BeShop Free
 */
$beshop_free_blogdate = get_theme_mod('beshop_blogdate', 1);
$beshop_free_blogauthor = get_theme_mod('beshop_blogauthor', 1);

?>
<div class="col-lg-4 bsgrid-item">
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="bshop-single-list">
			<header class="entry-header text-center">
				<?php
				the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');

				if ('post' === get_post_type() && (!empty($beshop_free_blogdate) || !empty($beshop_free_blogauthor))) :
				?>
					<div class="entry-meta">
						<?php
						if ($beshop_free_blogdate) {
							beshop_posted_on();
						}
						if ($beshop_free_blogauthor) {
							beshop_posted_by();
						}
						?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<?php beshop_post_thumbnail(); ?>

			<div class="entry-content">
				<?php
				the_excerpt();
				?>
			</div><!-- .entry-content -->

		</div>
	</article><!-- #post-<?php the_ID(); ?> -->
</div>
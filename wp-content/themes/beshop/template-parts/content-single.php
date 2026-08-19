<?php 
/*
*
* The file for display blog content for beshop theme
*
*/
$beshop_blogdate = get_theme_mod( 'beshop_blogdate', 1);
$beshop_blogauthor = get_theme_mod( 'beshop_blogauthor', 1);
$beshop_postcat = get_theme_mod( 'beshop_postcat', 1);
$beshop_posttag = get_theme_mod( 'beshop_posttag', 1);
?>
<div class="bshop-single-list">
	<header class="entry-header text-center mb-5">
			<?php
			if ( is_singular() ) :
				the_title( '<h2 class="entry-title">', '</h2>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;

			if ( 'post' === get_post_type() && ( !empty($beshop_blogdate) || !empty($beshop_blogauthor) ) ) :
						?>
						<div class="entry-meta">
							<?php
							if($beshop_blogdate){
							beshop_posted_on();
							}
							if($beshop_blogauthor){
							beshop_posted_by();
							}
							?>
						</div><!-- .entry-meta -->
					<?php endif; ?>
		</header><!-- .entry-header -->

		<?php beshop_post_thumbnail(); ?>

		<div class="entry-content">
			<?php
			if(is_single( )){
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'beshop' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'beshop' ),
					'after'  => '</div>',
				)
			);
		}else{
			the_excerpt();
		}


			?>
		</div><!-- .entry-content -->

		<?php if ( !empty($beshop_postcat) || !empty($beshop_posttag)  ) : ?>
		<footer class="entry-footer">
			<?php beshop_entry_footer($beshop_postcat, $beshop_posttag); ?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>

		
	</div>
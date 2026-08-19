<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package BeShop
 */

if ( ! function_exists( 'beshop_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function beshop_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'beshop' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'beshop_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function beshop_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'beshop' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'beshop_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function beshop_entry_footer($cat = '', $tag = '', $comment = 1) {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'beshop' ) );
			if ( $categories_list && $cat ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'beshop' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'beshop' ) );
			if ( $tags_list && $tag ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'beshop' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) && !empty($comment) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'beshop' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'beshop' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'beshop_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function beshop_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;



function beshop_header_logo($padding = ''){
	$beshop_logo_position = get_theme_mod( 'beshop_logo_position','center');
?>
	<div class="headerlogo-text text-<?php echo esc_attr($beshop_logo_position); ?>">
				<div class="container pb-<?php echo esc_attr($padding); ?> pt-<?php echo esc_attr($padding); ?>">
					<?php the_custom_logo(); ?>
				<?php if (display_header_text() == true || (display_header_text() == true && is_customize_preview()) ): ?>
					<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
					<?php
					$beshop_description = get_bloginfo( 'description', 'display' );
					if ( $beshop_description || is_customize_preview() ) :
						?>
						<p class="site-description"><?php echo $beshop_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<?php endif; ?>	
				<?php endif; ?>	
				</div>
	</div>
<?php	

}


/**
 * Add admin bar link for BeShop Pro upgrade
 */
function beshop_admin_bar_upgrade_link($wp_admin_bar) {
	// Only show to users who can install plugins (admins)
	if (!current_user_can('install_plugins')) {
		return;
	}

	// SVG icon for upgrade link
	$svg_icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
	<path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="#00b9eb"/>
	</svg>';

	// Add the menu item
	$wp_admin_bar->add_node(array(
		'id'    => 'beshop-upgrade-pro',
		'title' => $svg_icon . '<span class="ab-label">' . esc_html__('Upgrade to BeShop Pro', 'beshop') . '</span>',
		'href'  => esc_url('https://wpthemespace.com/product/beshop-pro/?add-to-cart=7351'),
		'meta'  => array(
			'title' => esc_attr__('Upgrade to BeShop Pro for more features', 'beshop'),
			'class' => 'beshop-upgrade-pro',
		),
	));
}
add_action('admin_bar_menu', 'beshop_admin_bar_upgrade_link', 999);

/**
 * Menu fallback function
 * Displays Home + 5 pages when no menu is assigned
 */
function beshop_menu_fallback($args = array())
{
	// Start the menu output
	$menu = '<ul id="beshop-menu" class="beshop-menu">';

	// Add Home link as the first item
	$menu .= '<li class="menu-item menu-item-home"><a href="' . esc_url(home_url('/')) . '" aria-current="page">' . esc_html__('Home', 'beshop') . '</a></li>';

	// Get up to 5 published pages
	$pages = get_pages(array(
		'number' => 5,
		'sort_column' => 'menu_order',
		'sort_order' => 'ASC',
		'post_status' => 'publish'
	));

	// Add pages to the menu
	foreach ($pages as $page) {
		$menu .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a></li>';
	}

	// Close the menu
	$menu .= '</ul>';

	// Output the menu
	echo $menu;
}
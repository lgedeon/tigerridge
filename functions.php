<?php
/*
 * Tigeridge Theme
 *
 * A rapid prototype tool. Uses index.php as the only template file which simply gets the content and adds a header if
 * none was provided by the_content. This theme has code to do just about everything, but none of it is active by
 * default. Choose what you want to activate using the settings page or block the settings page and set using a
 * plugin/child theme.
 *
 * Great for prototyping (especially when you want to show different header layouts on different pages), for sites that
 * need a different branding/look on some or all pages, for demo-ing several themes on the same site, and for use on
 * networks where custom themes are not allowed, but maximum flexibility is desired (like WordCamp.org).
 */

/*
 * Tigeridge includes a proper implementation of several popular frameworks including foundations, backbone, twitter bootstrap,
 * 960, _s, and others.
 *
 * It has a css editor for the front-end css (including IE versions) and for the editor.
 *
 * It has code for supporting every add_theme_support() feature WP has to offer.
 *
 * It has code for adding post types, taxonomies, and forms. Not fast enough for production, but works for a prototype.
 *
 * It has shortcodes for <sections> and other html that TinyMCE can't handle.
 *
 * It has options to add things to the theme customizer and activate them.
 *
 * It has an amazing page specific content builder that also does ads and body classes.
 *
 * todo: Check for snippet and then page universal-header. Next check for default-header. Then ob_filter until shutdown
 *       and check for <html> tag. If none, use default-header snippet/page. If that doesn't exist use built in code.
 *

/**
 * For this theme we are going to use posts as templates instead of files. Specifically, we are looking for the
 * sugarfield_snippets CPT or failing that a page with a matching title.
 *
 * This requires us to duplicate functionality from template-loader.php because the core code checks for file existence
 * before going on to the next check. We need to check for a matching snippet/page instead.
 */
function tigerridge_template_redirect() {
	// See template-loader.php for description. We include this here because of where we had to interrupt.
	if ( 'HEAD' === $_SERVER['REQUEST_METHOD'] && apply_filters( 'exit_on_http_head', true ) )
		exit();

	// Pass back processing for feeds and trackbacks.
	if ( is_robots() || is_feed() || is_trackback() ) {
		return;
	}

	$template = false;
	if     ( is_404()              && $template = tigerridge_get_template( '404' ) ) :
	elseif ( is_search()           && $template = tigerridge_get_template( 'search' ) ) :
	elseif ( is_front_page()       && $template = tigerridge_get_template( 'front-page' ) ) :
	elseif ( is_home()             && $template = tigerridge_get_template( 'home' ) ) :
	elseif ( is_post_type_archive() && $template = tigerridge_get_template( 'post_type_archive' ) ) :
	elseif ( is_tax()              && $template = tigerridge_get_template( 'taxonomy' ) ) :
	elseif ( is_attachment()       && $template = tigerridge_get_template( 'attachment' ) ) :
																									//remove_filter('the_content', 'prepend_attachment');
	elseif ( is_single()           && $template = tigerridge_get_template( 'single' ) ) :
	elseif ( is_page()             && $template = tigerridge_get_template( 'page' ) ) :
	elseif ( is_category()         && $template = tigerridge_get_template( 'category' ) ) :
	elseif ( is_tag()              && $template = tigerridge_get_template( 'tag' ) ) :
	elseif ( is_author()           && $template = tigerridge_get_template( 'author' ) ) :
	elseif ( is_date()             && $template = tigerridge_get_template( 'date' ) ) :
	elseif ( is_archive()          && $template = tigerridge_get_template( 'archive' ) ) :
	elseif ( is_comments_popup()   && $template = tigerridge_get_template( 'comments-popup' ) ) :
	elseif ( is_paged()            && $template = tigerridge_get_template( 'paged' ) ) :
	else :
		$template = tigerridge_get_template( 'index' );
	endif;

	// Now we can render the template.
	tigerridge_render_template( $template );

	// Once we have processed our template, we are done. Time to shut down.
	exit;
}
add_action( 'template_redirect', 'tigerridge_template_redirect' );

// return false to keep the search going
function tigerridge_get_template ( $type ) {
	$templates = array();
	switch ( $type ) {
		case 'post_type_archive' :
			$post_type = get_query_var( 'post_type' );
			if ( is_array( $post_type ) ) {
				$post_type = reset( $post_type );
			}
			$obj = get_post_type_object( $post_type );
			if ( ! $obj->has_archive ) {
				return '';
			}
			// fall through and get the archive template
		case 'archive' :
			$post_types = array_filter( (array) get_query_var( 'post_type' ) );
			if ( count( $post_types ) == 1 ) {
				$post_type = reset( $post_types );
				$templates[] = "archive-{$post_type}";
			}
			$templates[] = 'archive';
			break;
		case 'home' :
			$templates = array( 'home', 'index' );
			break;
		case 'category' :
		case 'tag' :
			$term = get_queried_object();
			if ( ! empty( $term->slug ) ) {
				$templates[] = "$type-{$term->slug}";
				$templates[] = "$type-{$term->term_id}";
			}
			$templates[] = $type;
			break;
		case 'taxonomy' :
			$term = get_queried_object();
			if ( ! empty( $term->slug ) ) {
				$taxonomy = $term->taxonomy;
				$templates[] = "taxonomy-$taxonomy-{$term->slug}";
				$templates[] = "taxonomy-$taxonomy";
			}
			$templates[] = 'taxonomy';
			break;
		case 'attachment' :
			global $posts;
			if ( ! empty( $posts ) && isset( $posts[0]->post_mime_type ) ) {
				$type = explode( '/', $posts[0]->post_mime_type );
				if ( ! empty( $type ) ) {
					$templates[] = $type[0];
					if ( ! empty( $type[1] ) ) {
						$templates[] = $type[1];
						$templates[] = "$type[0]_$type[1]";
					}
				}
			}
			$templates[] = 'attachment';
			break;
		case 'single' :
			$object = get_queried_object();
			if ( ! empty( $object->post_type ) )
				$templates[] = "single-{$object->post_type}";
			$templates[] = "single";
			break;
		case 'author' :
			$author = get_queried_object();
			if ( is_a( $author, 'WP_User' ) ) {
				$templates[] = "author-{$author->user_nicename}";
				$templates[] = "author-{$author->ID}";
			}
			$templates[] = 'author';
			break;
		case 'page' :
			// we don't support page specific templates because the page itself can be it's own template
		default :
			$templates = array( $type );
	}

	// now that we know what we are looking for let's see if it exists.

	// todo: cache the ids of the templates when they are found

	// first check for and use sugarfield_snippets
	foreach ( $templates as $template ) {
		$snippet = get_page_by_title( $template, 'OBJECT', 'sugarfield_snippets' );
		if ( isset( $snippet->ID ) ) {
			return $snippet->ID;
		}
	}

	// then check for pages - not recommended, but supported
	foreach ( $templates as $template ) {
		$page = get_page_by_title( $template );
		if ( isset( $page->ID ) ) {
			return $page->ID;
		}
	}

	// If nothing is found, return false, so we can keep looking.
	return false;
}

function tigerridge_render_template ( $template_id ) {
	if ( $template_id && $template = get_post( $template_id ) ) {
		/*
		 * This content is assumed to be pure html with a few short_codes. We are using a custom filter in case another
		 * plug really does not to change the template. Otherwise, we don't need all the filters for normal content.
		 */
		echo apply_filters( 'tigerridge_template_content', $template->post_content );
	} else {
		// if we are not able to find a template we should at least output the content
		while ( have_posts() ) : the_post();
			the_content();
		endwhile;
	}
}

// The only thing we do by default is process shortcodes.
add_filter( 'tigerridge_template_content', 'do_shortcode' );


/*
 * Make sure to handle template parts as needed too.
 */
function tigerridge_get_template_part ( $tag, $args ) {
	if ( strpos( $tag, 'get_template_part_' ) === 0 ) {
		// handle getting the right template part
	}
}
add_action( 'all', 'tigerridge_get_template_part');






function tigerridge_setup() {

	/*
	 * Make Tigeridge available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 */
	load_theme_textdomain( 'tigeridge', get_template_directory() . '/languages' );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );


	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5. Todo: might need to make this optional later.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',
	) );

	// Site-wide custom background. Can be changed per page with css snippets in Sugarfield
	add_theme_support( 'custom-background', array(
		'default-color' => 'f5f5f5',
	) );

}
add_action( 'after_setup_theme', 'tigeridge_setup' );

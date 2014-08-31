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
 */
function tigeridge_setup() {

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

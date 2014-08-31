<?php
/**
 * This is our only template file. It's job is to get the content. The content will be responsible for everything else.
 */

// todo provide a header if none sent by the_content.

if ( have_posts() ) :
	// Start the Loop.
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;
endif;

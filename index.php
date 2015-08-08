<?php
/**
 * This is our only template file. It is only here because every theme is required to have one.
 *
 * Actually, this file will probably not get called much, because we are intercepting template-loader.php with a hook on
 * the template_redirect action.
 *
 * If we miss and this file does get called we can hook this and do something useful.
 */

do_action( 'tigerridge_index' );

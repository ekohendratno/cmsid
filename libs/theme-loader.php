<?php 
/**
 * @fileName: theme-loader.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

if ( defined('use_themes') && use_themes ) :
	$template = false;
	
	if( is_single() ) : $template = get_template_load( 'single' );
	elseif( is_page() ) : $template = get_template_load( 'page' );
	elseif( is_archive() ) : $template = get_template_load( 'archive' );
	elseif( is_author() ) : $template = get_template_load( 'author' );
	elseif( is_category() ) : $template = get_template_load( 'category' );
	elseif( is_tag() ) : $template = get_template_load( 'tag' );
	elseif( is_search() ) : $template = get_template_load( 'search' );
	elseif( is_404() ) : $template = get_template_load( '404' );
	elseif( is_admin() ) : $template = get_template_load( 'admin' );
	elseif( is_signin() ) : $template = get_template_load( 'signin' );
	elseif( is_signup() ) : $template = get_template_load( 'signup' );
	elseif( is_activate() ) : $template = get_template_load( 'activate' );
	elseif( is_request() ) : $template = get_template_load( 'request' );
	elseif( is_attachment() ) : $template = get_template_load( 'attachment' );
	elseif( is_robots() ) : $template = get_template_load( 'robots' );
	elseif( is_home() ) : $template = get_template_load( 'index' );
	else: $template = get_template_load( 'index' ); endif;
	
	if ( $template ):
		include( $template );
		return;
	else: 
	
		include( get_template_load( '404' ) );	
		return;
	
	endif;
	
endif;
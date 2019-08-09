<?php 
/**
 * @fileName: request.php
 * @dir: admin/
 */
if(!defined('_iEXEC')) exit;

the_head_request();

global $user;

/**
 * updateing request dashboard setup
 */
if($user->check() && 'dashboard' == get_request() 
){
	set_dashboard_admin( $_POST["data"] );
}

if( $get_load 	= get_load() 

or( $get_load 	= get_load() 
&&  $get_plg 	= get_plugin() ) )
{

	if( get_load() && get_plugin() ) $file = plugin_path .'/'. $get_load;
	elseif( get_load() ) $file = abs_path . $get_load;	
		
	if( file_exists( $file ) ) include( $file );
		
}
elseif( $get_redirect = get_redirect() )
{

	$base_url = esc_sql( $get_redirect );

	if (!headers_sent()){ 
		//header('HTTP/1.1 404 Not Found');
		header('Location: '.$base_url); exit;
	}else{ 
		echo '<script type="text/javascript">';
		echo 'window.location.href="'.$base_url.'";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$base_url.'" />';
		echo '</noscript>'; exit;
	}

}
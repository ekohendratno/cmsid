<?php 
/**
 * @fileName: theme.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;
/**
 * Load header template.
 *
 * @param string $name
 */
function get_header( $name = null ) {

	do_action( 'get_header', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "header-{$name}.php";

	$templates[] = 'header.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( libs_path . '/theme-compact/header.php');
}

/**
 * Load footer template.
 *
 * @param string $name
 */
function get_footer( $name = null ) {

	do_action( 'get_footer', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "footer-{$name}.php";

	$templates[] = 'footer.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( libs_path . '/theme-compact/footer.php');
}
/**
 * Load sidebar template.
 *
 * @param string $name
 */
function get_sidebar( $name = null ) {
	
	do_action( 'get_sidebar', $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "sidebar-{$name}.php";

	$templates[] = 'sidebar.php';

	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		load_template( libs_path . '/theme-compact/sidebar.php');
}
/**
 * Load a template part into a template
 *
 * @param string $slug
 * @param string $name
 */
function get_template_part( $slug, $name = null ) {

	do_action( "get_template_part_{$slug}", $slug, $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_template($templates, true, false);
}
/**
 * Retrieve path of admin template in current or parent template.
 *
 * @return string
 */
function get_template_load( $parameter = 'index' ) {
	return get_query_template( $parameter );
}
/**
 * Retrieve path to a template
 *
 * @param string $type
 * @param array $templates
 * @return string
 */
function get_query_template( $file_template, $templates = array() ) {
	$file_template = preg_replace( '|[^a-z0-9-]+|', '', $file_template );

	if ( empty( $templates ) )
		$templates = array("{$file_template}.php");

	return locate_template( $templates );
}
/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * @param string|array $template_names
 * @param bool $load
 * @param bool $require_once
 * @return string
 */
function locate_template($template_names, $load = false, $require_once = true ) {	
	$located = '';
	
	if( is_admin() || is_signin() || is_signup() || is_activate() || is_request() ) 
		$template_path = libs_path . '/theme-compact';
	else $template_path = template_path;
	
	
	$template_path_detault = theme_path .'/'. default_theme;
	
	foreach ( (array) $template_names as $template_name ) {
		
		if ( !$template_name )
			continue;
		if ( file_exists($template_path . '/' . $template_name)) {
			$located = $template_path . '/' . $template_name;
			break;
		} else if ( file_exists( $template_path_detault . $template_name) ) {
			$located = $template_path_detault . $template_name;
			break;
		}
	}
	
	if ( $load && '' != $located )
		load_template( $located, $require_once );

	return $located;
}
/**
 * Require the template file with environment.
 *
 * @param string $_template_file
 * @param bool $require_once
 */
function load_template( $_template_file, $require_once = true ) {
	global $user, $db, $version_system;

	if ( $require_once )
		require_once( $_template_file );
	else
		require( $_template_file );
}
/**
 * Retrieve current theme directory.
 *
 * @return string
 */
function get_template_directory() {
	$template = get_template();
	$theme_root = get_theme_root( $template );
	$template_dir = "$theme_root/$template";

	return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
}
/**
 * Retrieve name of the current theme.
 *
 * @return string
 */
function get_template() {		
	$get_template = get_option('template');
		
	//if(!file_exists(get_template_directory().'/index.php') )
		//$get_template = default_theme;
		
	return apply_filters('template', $get_template);
	
}
/**
 * Retrieve path to themes directory.
 *
 * @param string $stylesheet_or_template
 * @return string
 */
function get_theme_root( $template_name = false ) {
	
	if ( $template_name )
		$theme_root = content_path . '/themes';	
		
	return apply_filters( 'theme_root', $theme_root );
}
/**
 * Retrieve template directory URI.
 *
 * @return string
 */
function get_template_directory_uri( $display = false ) {
	$template = get_template();
	$theme_root_uri = get_theme_root_uri( $template );
	$template_dir_uri = "$theme_root_uri/$template";

	$retval = apply_filters( 'template_directory_uri', $template_dir_uri, $template, $theme_root_uri );
	
	if ( $display )
		echo $retval;
	else
		return $retval;
}
/**
 * Retrieve URI for themes directory.
 *
 * @param string $stylesheet_or_template
 * @return string
 */
function get_theme_root_uri( $template_name ) {
	
	if ( $template_name )
	$theme_root_uri = content_url( '/themes' );

	return apply_filters( 'theme_root_uri', $theme_root_uri );
}

/**
 * Mengcek system yang dimasukkan
 *
 * @param $option string
 * @param $included true|false
 * @return include file | name file
 */
function get_system_cheked( $option, $file = 'manage' ){
	
	$file_included = $option .'/'.$file.'.php';
	$file_included = admin_path .'/'. $file_included;
	
	if( file_exists( $file_included ) )
	return true;
}

/**
 * Memanggil system yang dimasukkan
 *
 * @param $option string
 * @param $included true|false
 * @return include file | name file
 */
function get_system_included( $option, $file = 'manage' ){
	
	$file_included = $option .'/'.$file.'.php';
	$file_included = admin_path .'/'. $file_included;
	
	if( file_exists( $file_included ) ) include_once( $file_included );
	return;
}

function is_system_values(){
	return '';
}

/**
 * Menampilkan konten manager
 *
 * @return file
 */
function the_main_manager($li,$il){
	
	do_action('the_main_manager');
	
	if( get_system_cheked( get_admin() ) 
	&& $values = is_system_values() )
	{
		get_system_included( $values, 'functions' );
		get_system_included( $values );
	}
	else 
	{
		if(get_system_cheked( get_admin() ) == false  && get_admin() ) {
			//header("location:?admin=404");
			//exit;
		}else{
			if( get_admin() == '404' ){
				if( file_exists(admin_path . "/404.php") )
				include admin_path . "/404.php";
			}else{
				set_current_screen();
				add_screen_option('layout_columns', array('max' => 4, 'default' => 2) );
				
				dashboard_init();
				dashboard_setup();
				print("$li");
				
				dashboard();
				
				print("$il");
			}
			return;
		}
	}
	
}

function the_menuaction($li,$il){
	global $widget, $sidebar_default;

	if( isset($widget['m']) && count($widget['m']) > 0 && !empty($widget['m']) ) {
		foreach($widget['m'] as $k => $v)	echo $li. $v['l'] . "'>" . $v['t'] . $il;		
	}else{
		
	
	$plugins 	= get_dir_plugins();
	foreach((array) $plugins as $key => $val){
		$name = get_plugins_name($key);
		$key2 = str_replace( $name .'/', '' , $key );
		
	 	if( !empty($name)
		&& file_exists( plugin_path .'/'. $name . '/admin.php' )
		&& get_plugins( $key ) == 1 ){
			
		$plugins_new[$key] = array('t' => $val['Name'], 'l' => '?admin&s=plugins&go=setting&plugin_name='.$name.'&file=/'.$key2);
		}
	}
	
	foreach((array) $sidebar_default as $k => $v){
		/*if( get_sys_cheked( $k ) )*/ $sidebar_menus[$k] = array('t' => $v['t'], 'l' => $v['l']);
	}
	
	$sidebar_menus = parse_args($plugins_new,$sidebar_menus);	
	$sidebar_menus = array_multi_sort($sidebar_menus, array('t' => SORT_ASC));
	foreach((array) $sidebar_menus as $k => $v)	echo $li. $v['l'] . "'>" . $v['t'] . $il;	
	
	}
}

?>
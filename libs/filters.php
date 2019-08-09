<?php 
/**
 * @fileName: filters.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

/**
 * Call the functions added to a filter hook.
 *
 * @param string $tag
 * @param mixed $value
 * @param mixed $var
 * @return mixed
 */
function apply_filters($tag, $value) {
	global $filter, $merged_filters, $current_filter;

	$args = array();

	// Do 'all' actions first
	if ( isset($filter['all']) ) {
		$current_filter[] = $tag;
		$args = func_get_args();
		_call_all_hook($args);
	}

	if ( !isset($filter[$tag]) ) {
		if ( isset($filter['all']) )
			array_pop($current_filter);
		return $value;
	}

	if ( !isset($filter['all']) )
		$current_filter[] = $tag;

	// Sort
	if ( !isset( $merged_filters[ $tag ] ) ) {
		ksort($filter[$tag]);
		$merged_filters[ $tag ] = true;
	}

	reset( $filter[ $tag ] );

	if ( empty($args) )
		$args = func_get_args();

	do {
		foreach( (array) current($filter[$tag]) as $the_ )
			if ( !is_null($the_['function']) ){
				$args[1] = $value;
				$value = call_user_func_array($the_['function'], array_slice($args, 1, (int) $the_['accepted_args']));
			}

	} while ( next($filter[$tag]) !== false );

	array_pop( $current_filter );

	return $value;
}
/**
 * Calls the 'all' hook, which will process the functions hooked into it.
 *
 * @param array $args
 * @param string $hook
 */
function _call_all_hook($args) {
	global $filter;

	reset( $filter['all'] );
	do {
		foreach( (array) current($filter['all']) as $the_ )
			if ( !is_null($the_['function']) )
				call_user_func_array($the_['function'], $args);

	} while ( next($filter['all']) !== false );
}

function my_escape( $string ){
	if( !empty( $string ) ){
		
		if (version_compare(phpversion(),"4.3.0", "<")) mysql_escape_string($string);
		else mysql_real_escape_string($string);
		
		return $string;
	}
}
	
function escape( $data ){
	if ( is_array( $data ) ) {
		foreach ( (array) $data as $k => $v ) {
			if ( is_array( $v ) )
				$data[$k] = escape( $v );
			else
				$data[$k] = my_escape( $v );
		}
	} else {
		$data = my_escape( $data );
	}

	return $data;
}
/**
 * Escapes data query MySQL
 *
 * @param string $sql
 * @return string
 */
function esc_sql( $sql ){
	
	if( !empty( $sql ) )
		return escape( $sql );
	
}
/**
 * Build Unique ID for storage and retrieval.
 *
 * @global array $filter
 * @param string $tag
 * @param callback $function
 * @param int|bool $priority
 * @return string|bool
 */
function _filter_build_unique_id($tag, $function, $priority) {
	global $filter;
	static $filter_id_count = 0;

	if ( is_string($function) )
		return $function;

	if ( is_object($function) ) {
		$function = array( $function, '' );
	} else {
		$function = (array) $function;
	}

	if (is_object($function[0]) ) {
		if ( function_exists('spl_object_hash') ) {
			return spl_object_hash($function[0]) . $function[1];
		} else {
			$obj_idx = get_class($function[0]).$function[1];
			if ( !isset($function[0]->filter_id) ) {
				if ( false === $priority )
					return false;
				$obj_idx .= isset($filter[$tag][$priority]) ? count((array)$filter[$tag][$priority]) : $filter_id_count;
				$function[0]->filter_id = $filter_id_count;
				++$filter_id_count;
			} else {
				$obj_idx .= $function[0]->filter_id;
			}

			return $obj_idx;
		}
	} else if ( is_string($function[0]) ) {
		return $function[0].$function[1];
	}
}


function sanitize( $string ) { 
	//TRANSLIT or //IGNORE or //TRANSLIT//IGNORE	
	$clean = iconv("UTF-8", "ISO-8859-1//IGNORE", $string);
	//$clean = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
	return $clean;
}  

function plugin_basename($file, $path = plugin_path) {
	
	$file = str_replace('\\','/',$file);
	$file = preg_replace('|/+|','/', $file);
	
	$plugin_dir = str_replace('\\','/',$path);
	$plugin_dir = preg_replace('|/+|','/', $plugin_dir);
	
	$mu_plugin_dir = str_replace('\\','/',$path);
	$mu_plugin_dir = preg_replace('|/+|','/', $mu_plugin_dir);
	
	$file = preg_replace('#^' . preg_quote($plugin_dir, '#') . '/|^' . preg_quote($mu_plugin_dir, '#') . '/#','',$file);
	$file = trim($file, '/');
	return $file;
}

function cleanname( $thename ){
	$patternCounter = 0;
	 
	/*
	$patterns[$patternCounter] = '/[\x21-\x2d]/u'; // remove range of shifted characters on keyboard - !"#$%&'()*+,-
	$patternCounter++;
	 
	$patterns[$patternCounter] = '/[\x5b-\x60]/u'; // remove range including brackets - []\^_`
	$patternCounter++;
	*/
	 
	$patterns[$patternCounter] = '/[\x7b-\xff]/u'; // remove all characters above the letter z.  This will eliminate some non-English language letters
	$patternCounter++;
	 
	$replacement = '';
 
     return preg_replace($patterns, $replacement, $thename);
}
/**
 * String to pos clone
 *
 * @return true|false
 */
function stripost($haystack, $needle, $offset=0){
	
	if( !function_exists('stripos') ) 
		$return = strpos(strtoupper($haystack), strtoupper($needle), $offset);
	else 
		$return = stripos($haystack, $needle, $offset=0);
	
	if ($return === false) return false; 
	else return true; 
}
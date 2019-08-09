<?php 
/**
 * @fileName: options.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;
/**
 * Memanggil pengaturan dari table options
 *
 * @param string|int $option
 * @return string|int|false
 */
function get_option( $option, $default = false ){
	global $db;
	
	$option	= esc_sql( $option );
	
	$sql_query = "SELECT * FROM $db->options WHERE option_name='".$option."'";	
						
	$obj = $db->get_results($sql_query);
			
	if( count( $db->get_col( $sql_query ) ) > 0 && $obj = $obj[0] )
		return $obj->option_value;
		
	/*
	if( empty($val) 
	&& $user->check() 
	&& $user->level('admin') 
	){
		echo '<div class="padding"><div id="message">';
		echo "Atention Option '$option' not found in table '$db->options' please check and try again or you can <a href=\"?admin&sys=options&go=fix&name=$option\">fix it</a>";
		echo '</div></div>';
		return false;
	}else{ 	
		return apply_filters( 'option_' . $option, maybe_unserialize( $val ) );
	}*/
}
/**
 * Mengecek options
 *
 * @param string|int $option
 * @return string|int|false
 */
function checked_option( $option ){
	global $db;
	
	$option	= esc_sql( $option );
	
	return count( $db->get_col( "SELECT * FROM $db->options WHERE option_name='".$option."'") );;
}
/**
 * Memperbaharui pengaturan dari table options
 *
 * @param string|int $option
 * @param string|int $value
 */
function set_option($option, $value = ''){
	global $db;		
		
	$option	= esc_sql( $option );
	$value	= esc_sql( $value );
	
	return $db->insert( 'options', array('option_value' => $value),  array('option_name' => $option) );
}
/**
 * Menambahkan pengaturan ke table options
 *
 * @param string|int $option
 * @param string|int $value
 */
function add_option($option, $value = ''){
	global $db;	
		
	$option	= esc_sql( $option );
	$value	= esc_sql( $value );
		
	return $db->update( 'options',  array('option_value' => $value),  array('option_name' => $option) );
}
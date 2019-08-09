<?php 
/**
 * @fileName: rewrite.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;



function the_permalink( $display = true ){
	
	$post = get_post();
	
	$url = '';
	
	if( the_format_type(false) == 'page' ){
		$url.= '?page_id=' . the_ID(false);
	}elseif( the_format_type(false) == 'post' ){
		$url.= '?p=' . the_ID(false);
	}else{
		$url.= '?p=' . the_ID(false);
	}
	
	if ( $display )
		echo $url;
	else
		return $url;
}


function the_permalink_author( $display = true ){
	
	$post = get_post();
	
	$url = '?author=' . the_author_ID(false);
	
	if ( $display )
		echo $url;
	else
		return $url;
}
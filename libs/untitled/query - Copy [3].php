<?php 
/**
 * @fileName: query.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class Query {
	
	public $is_single = false;
	public $is_page = false;
	public $is_archive = false;
	public $is_author = false;
	public $is_category = false;
	public $is_tag = false;
	public $is_search = false;
	public $is_home = false;
	public $is_404 = false;
	public $is_admin = false;
	public $is_attachment = false;
	public $is_robots = false;
	
	public function init(){
		$keys = array(
			'error'
			, 'm'
			, 'p'
			, 'page_id'
			, 'attachment'
			, 'attachment_id'
			, 'cat'
			, 'tag'
			, 'author'
			, 'feed'
			, 's'
		);
	}
 
}

$GLOBALS['query'] = new Query();


function GetInputString(){
	$format_defines = '';
        //order of retrieve default GPCS (get, post, cookie, session); 

        $format_defines = array ( 
        'get'=>'_GET', 
        'post'=>'_POST', 
        'cookie'=>'_COOKIE', 
        'session'=>'_SESSION', 
        'request'=>'_REQUEST', 
        'files'=>'_FILES', 
        ); 
        foreach ($format_defines as $k => $glb ){ 
            return (object) array( $k => (object) $GLOBALS[$format_defines[$k]] ); 
        } 

        return NULL; 
} 

$a = GetInputString();

if( isset($a->get->page_id) ) echo $a->get->page_id;
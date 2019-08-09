<?php 
/**
 * @fileName: query.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class Query{	
	public $query = '';
	public $data = array();
	public $posts = array();
	public $post_count = 0;
	public $current_post;
	
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
	
	
	public function __construct(){		
        $format_defines = array ( 
        'get'=>'_GET', 
        'post'=>'_POST', 
        'cookie'=>'_COOKIE', 
        'session'=>'_SESSION', 
        'request'=>'_REQUEST', 
        'files'=>'_FILES', 
        ); 
		
        foreach ($format_defines as $k => $v ){ 
            $this->$k = (object) $GLOBALS[$format_defines[$k]]; 
        } 
    }	
	
	
    public function __set($name, $value){
        $this->data[$name] = $value;
    }

    public function __get($name){
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    /**  As of PHP 5.1.0  */
    public function __isset($name){
        return isset($this->data[$name]);
    }

    /**  As of PHP 5.1.0  */
    public function __unset($name){
        unset($this->data[$name]);
    }
	
	public function posts(){		
		$this->get_posts();
		
		if( $this->post_count > 0 )
		return $this->posts;
		
		return false;
	}
	
	public function parse_query(){
		global $paging;
		 //$this->query.= " DESC";
		 //$this->query.= " ORDER BY";
		 
		 $this->current_post = ( isset($this->get->page) ) ? $this->get->page : '';
		 $this->query.= " LIMIT $paging->position($this->post_count,$this->current_post),$paging->paging_size";
		 //$this->query.= " WHERE";
	}
	
	public function paging_nav(){
		return $paging->paging($_SERVER['PHP_SELF']);
	}
	
	public function get_posts() {
		global $db, $paging;
		
		$this->parse_query();
		
		echo "SELECT * FROM $db->post $this->query";
		
		$sql_query = $db->query("SELECT * FROM $db->post $this->query");
		while( $object_id = $db->fetch_obj($sql_query) ):
			$this->posts[] = $object_id;
			$this->post_count++;
		endwhile;
	}
	
}

function posts(){
	global $query;
	
	return $query->posts();
}

function get_paging_nav(){
	global $query;
	
	return $query->paging_nav();
}

function is_single(){
	global $query;
	
	if( isset($query->get->page_id) )
	return true;
}

function is_page(){
	global $query;
	
	if( isset($query->get->p) )
	return true;
}

function is_archive(){
	global $query;
	
	if( isset($query->get->m) )
	return true;
}

function is_author(){
	global $query;
	
	if( isset($query->get->author) )
	return true;
}

function is_category(){
	global $query;
	
	if( isset($query->get->cat) )
	return true;
}

function is_tag(){
	global $query;
	
	if( isset($query->get->tag) )
	return true;
}

function is_search(){
	global $query;
	
	if( isset($query->get->s) )
	return true;
}

function is_404(){
	global $query;
	
	if( isset($query->get->error) )
	return true;
	
}

function is_admin(){
	global $query;
	
	if( isset($query->get->admin) )
	return true;
}

function is_signin(){
	global $query;
	
	if( isset($query->get->signin) )
	return true;
	
}

function is_signup(){
	global $query;
	
	if( isset($query->get->signup) )
	return true;
}

function is_activate(){
	global $query;
	
	if( isset($query->get->activate) )
	return true;
}

function is_attachment(){
	global $query;
	
	if( isset($query->get->attachment) )
	return true;
}

function is_robots(){
	global $query;
	
	if( isset($query->get->robot) )
	return true;
}

function is_home(){
	global $query;
	
	if(!isset($query->get) )
	return true;
}
?>
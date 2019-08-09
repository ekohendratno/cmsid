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
	public $posts_id = array();
	public $current_post;
	public $use_paging = false;
	public $type_post;
	
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
		
        foreach ($format_defines as $k => $v ):
            $this->$k = (object) $GLOBALS[$format_defines[$k]]; 
        endforeach;
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
		global $db, $paging;
		
		if(isset($this->get->s))
			$this->query.= " LIKE %$this->get->s%";
			
		if( isset($this->get->p) 
		||  isset($this->get->s) 
		||  isset($this->get->page_id) 
		|| !empty($this->type_post) )
			$this->query.= " WHERE";
		
		if( isset($this->get->p) )
			$this->query.= " post_ID=" . $this->get->p;	
		
		if( isset($this->get->page_id) )
			$this->query.= " post_ID=" . $this->get->page_id;	
		 
		if(!empty($this->type_post) ):
			if( isset($this->get->p) || isset($this->get->page_id) )
			$this->query.= " AND";	
			
			$this->query.= " post_type='$this->type_post'";	
		endif;
			
		if(!empty($this->get->orderby) ):
			$this->query.= " ORDER BY";
			$this->query.= " post_date";
			$this->query.= " DESC";
		endif;			
		
		//echo $this->query;
		
		$this->post_count = $db->num_query("SELECT * FROM $db->posts $this->query");						 
		$this->current_post = ( isset($this->get->paged) ) ? $this->get->paged : '';
		$this->query.= " LIMIT ";
		 
		if( isset($this->use_paging) )
			$this->query.= $paging->position($this->post_count,$this->current_post).",".$paging->paging_size;
		else
			$this->query.= $paging->paging_size;
	}
	
	public function set_paging( $default = false ){
		$this->use_paging = $default;
	}
	
	public function paging_nav( $ul, $lu ){
		global $paging;
		
		echo $paging->paging( site_url('/'), $ul, $lu );
	}
	
	public function get_posts() {
		global $db;
		
		$this->parse_query();
		//echo "SELECT * FROM $db->posts $this->query";
		$sql_query = $db->query("SELECT * FROM $db->posts $this->query");
		
		while( $object_id = $db->fetch_obj($sql_query) ):
		
			$this->posts[] = $object_id;
			$this->posts_id[$object_id->post_ID] = $object_id;
			
		endwhile;
		
		//if(!$this->post_count )
			//header("Location: ".site_url("/?error=1")."");
	}
	
	public function get_category(){
	}
	
	public function get_comments(){
	}
	
	public function get_options( $option ){
		global $db;
		
		if(!is_array($option) )
			return false;
				
		if( $option['id'] == 'add' && !$this->get_options( array('id' => 'get', 'name' => $option) ) )
			return $db->update( 'options',  array('option_value' => $option['value']),  array('option_name' => $option['name']) );
		
		if( $option['id'] == 'set' && $this->get_options( array('id' => 'get', 'name' => $option) ) )
			return $db->insert( 'options', array('option_value' => $option['value']),  array('option_name' => $option['name']) );
		
		if( $option['id'] == 'count' )
			return $db->num_query("SELECT * FROM $db->options WHERE option_name='".$option['name']."'");
		
		if( $option['id'] == 'get' ):
			$sql_query = $db->query("SELECT * FROM $db->options WHERE option_name='".$option['name']."'");	
						
			if( $db->num( $sql_query ) > 0 && $obj = $db->fetch_obj( $sql_query ) )
				return $obj->option_value;
			
		endif;
		
		return false;
	}
	
}

function have_posts( $port_type = 'post' ){
	global $query;
	
	$query->type_post = $port_type;
	
	return $query->posts();
}

function get_paging_nav($prev='',$next=''){
	global $query;
	
	$query->set_paging( true );
	return $query->paging_nav($prev,$next);
}

function get_post_thumbnail( $by_id, $thumb_default = '', $print = '' ){
	global $query;
	
	$thumb = $thumb_default;
	if(!empty($query->posts_id[$by_id]->post_thumb) ) 
		$thumb = $query->posts_id[$by_id]->post_thumb;
	
	if( empty($print) ) return $thumb; else echo $thumb;
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
	
	if(empty($query->data->get) )
		return true;
}
?>
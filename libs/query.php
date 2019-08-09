<?php 
/**
 * @fileName: query.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class Query{	
	public $request;
	public $posts;
	public $post_count = 0;
	public $current_post = -1;
	public $post;
	public $data = array();
	public $query_vars = array();
	
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
	public $is = false;
	
	public $first_query_vars = array();
	public $keys = array(
			'error'
			, 'm'
			, 'p'
			, 'attachment'
			, 'attachment_id'
			, 'page_id'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'category_name'
			, 'tag'
			, 'cat'
			, 'tag_id'
			, 'author'
			, 'author_name'
			, 'feed'
			, 'paged'
			, 's'
	);
	public $keys_internal = array(
			'admin'
			, 'signin'
			, 'signup'
			, 'activate'
			, 'request'
	);
	
	public function __construct() {
		
        $this->query_vars = esc_sql( $GLOBALS['_GET'] ); 	
		
		$this->query();	
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
	
	
	public function get( $query_var, $default = '' ) {
		if ( isset( $this->query_vars[ $query_var ] ) ) {
			return $this->query_vars[ $query_var ];
		}

		return $default;
	}

	public function set($query_var, $value) {
		$this->query_vars[$query_var] = $value;
	}
	
	
	public function fill_query_vars($array) {
		
		if( !(array) $array ) return false;
		
		$new_array = array();
		foreach ( (array) $array as $key => $val ){
			$new_array[$key] = $val;			
			$this->first_query_vars[][$key] = $val;
		}
		
		return $new_array;
		
	}
	
	public function first_query_valid(){	
	
		$this->replace_value($this->first_query_vars[0]);
		
		foreach( (array) $this->first_query_vars[0] as $key => $val ){
			if(!in_array( $key,array_merge($this->keys,$this->keys_internal) ) ) return false;
		}
		
		return $this->first_query_vars[0];
	}
	
	public function replace_value(){
		
		foreach( (array) $this->first_query_vars[0] as $key => $val ){
			if( in_array($key,$this->keys_internal) ){
				$this->first_query_vars[0][$key] = 1;
			}else{
				$this->first_query_vars[0][$key] = $val;
			}
		}
	}
	
	public function have_posts(){
		
		if ( $this->current_post + 1 < $this->post_count ) {
			return true;
		}

		return false;
	}
	
	public function the_post(){
		global $post;
		
		$post = $this->next_post();
		
		$this->setup_postdata( $post );	
	}
	
	public function next_post() {
		$this->current_post++;

		$this->post = $this->posts[$this->current_post];
		return $this->post;
	}
	
	public function get_post(){
		return $this->posts;
	}
	
	public function setup_postdata( $post ) {
		global $id, $authordata, $currentpost, $categorydata, $page, $pages, $multipage, $more, $numpages;

		$id = (int) $post->post_ID;
		

		$authordata = get_userdata( $post->post_user_ID );
		$categorydata = get_categorydata( $id );
		
		$currentpost = $post->post_date;
		
		
		$numpages = 1;
		$multipage = 0;
		$page = $this->get('page');
		if ( ! $page )
			$page = 1;
			
		
		if ( $post->post_ID === $this->get('p') && ( $this->is_page() || $this->is_single() ) ) {
			$more = 1;
		} else {
			$more = 0;
		}
		
		$content = $post->post_content;
		if ( false !== strpos( $content, '<!--nextpage-->' ) ) {
			if ( $page > 1 )
				$more = 1;
			$content = str_replace( "\n<!--nextpage-->\n", '<!--nextpage-->', $content );
			$content = str_replace( "\n<!--nextpage-->", '<!--nextpage-->', $content );
			$content = str_replace( "<!--nextpage-->\n", '<!--nextpage-->', $content );

			// Ignore nextpage at the beginning of the content.
			if ( 0 === strpos( $content, '<!--nextpage-->' ) )
				$content = substr( $content, 15 );

			$pages = explode('<!--nextpage-->', $content);
			$numpages = count($pages);
			if ( $numpages > 1 )
				$multipage = 1;
		} else {
			$pages = array( $post->post_content );
		}
		
		return true;
	}
			
	public function query( $query =  '' ) {
		global $db;
		
		$this->init();
		$this->parse_query();
		
		$this->posts = $db->get_results( $this->request );
		
		if ( $this->posts ) {
			$this->post_count = count( $this->posts );
		} else {
			$this->post_count = 0;
		}
		
		
		if( $this->post_count < 1 )
			$this->is_404 = true;
	}
	
	public function parse_query(){
		global $db;
		
		$q = &$this->query_vars;
		
		$q = $this->fill_query_vars($q);
		
		$y = $this->first_query_valid();
		
		// First let's clear some variables
		$distinct = '';
		$found_rows = '';
		$fields = '';
		$from = "$db->posts";
		$where = '';
		$limits = '';
		$join = '';
		$groupby = '';
		$orderby = '';
				
		$distinct = '*';
		
		if( $this->is_page() ){
			$this->is = true;
			
			$where.= "AND $db->posts.post_type='post' ";
			$where.= "AND $db->posts.post_ID=" . $y['p'] . ' ';
		}
		
		if( $this->is_single() ){
			$this->is = true;
			
			$where.= "AND $db->posts.post_type='page' ";
			$where.= "AND $db->posts.post_ID=" . $y['page_id'] . ' ';
		}
		
		if( $this->is_author() ){
			$this->is = true;
			
			$where.= "AND $db->posts.post_type='post' ";
			$where.= "AND $db->posts.post_user_ID=" . $y['author'] . ' ';
		}
		
		if( $this->is_archive() ){	
			$this->is = true;
					
			$m = str_replace('-',':',$y['m']);
			
			if( preg_match('/\d{4}\:\d{2}/', $m) )
				list($tahun, $bulan) = explode(':',$m);
			
			if( preg_match('/\d{4}\.\d{2}/', $m) )
				list($tahun, $bulan) = explode('.',$m);
			
			$where.= "AND $db->posts.post_type='post' ";			
			$where.= "AND month(`post_date`) = " . $bulan . " ";
			$where.= "AND year(`post_date`) = " . $tahun . " ";
			
		}
		
		if( $this->is_category() ){
			$this->is = true;
			
			$cat_id = (int) $y['cat'];
			
			$from  = "$db->category_relations";
			$join  = "JOIN $db->posts";			
			
			$where.= "AND $db->posts.post_type='post' ";			
			$where.= "AND $db->posts.post_ID = $db->category_relations.relations_post_ID ";
			$where.= "AND $db->category_relations.relations_category_ID=" . $cat_id . " ";
			
		}
		
		if( $this->is_tag() ){	
			$this->is = true;
					
			$from  = "$db->tags";
			$join  = "JOIN $db->posts";			
			
			$where.= "AND $db->posts.post_type='post' ";			
			$where.= "AND $db->posts.post_ID = $db->tags.tag_post_ID ";
			$where.= "AND $db->tags.tag_content LIKE '%" . $y['tag'] . "%' ";
		}
		
		if( $this->is_search() ){
			$this->is = true;
			
			$where.= "AND ( $db->posts.post_title LIKE '%" . $y['s'] . "%' OR $db->posts.post_content LIKE '%" . $y['s'] . "%') ";
		}
		
		if( $this->is_home() ){		
			$this->is = false;
			
			$where = "AND $db->posts.post_type='post' ";
		}
			
		
		$where.= "AND $db->posts.post_status=1 ";
		
		$orderby = "ORDER BY $db->posts.post_date DESC";
		$limits = "LIMIT 10";
		
		$this->request = $old_request = "SELECT $found_rows $distinct $fields FROM $from $join WHERE 1=1 $where $groupby $orderby $limits";
		
		
	}
	
	public function init() {
		unset($this->posts);
		unset($this->query);
		$this->post_count = 0;
		$this->current_post = -1;
		unset( $this->request );
		unset( $this->post );

		$this->init_query();
	}
	
	private function init_query() {		
		$this->is_single = false;
		$this->is_page = false;
		$this->is_archive = false;
		$this->is_author = false;
		$this->is_category = false;
		$this->is_tag = false;
		$this->is_search = false;
		$this->is_home = false;
		$this->is_404 = false;
		$this->is_admin = false;
		$this->is_attachment = false;
		$this->is_robots = false;
		$this->is = false;
	}
	
	public function is(){
		if( $this->is )
			return true;
			
		return false;
	}
	
	public function is_home(){		
	
		$first_query = $this->first_query_valid();
		
		if( (!$first_query || $first_query == null ) && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_page(){	
			
		$first_query = $this->first_query_valid();
		
		if( $first_query['p'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_single(){		
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['page_id'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_archive(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['m'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_author(){
		
		$first_query = $this->first_query_valid();		
		
		if( $first_query['author'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_category(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['cat'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_tag(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['tag'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_tag_id(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['tag_id'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_search(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['s'] && !$this->is_404 )			
			return true;
			
		return false;
	}
	
	public function is_404(){
		
		if( $this->is_404 || $first_query['error'] )			
			return true;
			
		return false;
		
	}
	
	public function is_admin(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['admin'] )			
			return true;
			
		return false;
	}
	
	public function is_signin(){
		
		$first_query = $this->first_query_valid();		
		
		if( $first_query['signin'] )			
			return true;
			
		return false;
		
	}
	
	public function is_signup(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['signup'] )			
			return true;
			
		return false;
	}
	
	public function is_activate(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['activate'] )			
			return true;
			
		return false;
	}
	
	public function is_request(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['request'] )			
			return true;
			
		return false;
	}
	
	public function is_attachment(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['attachment'] )			
			return true;
			
		return false;
	}
	
	public function is_robots(){
		
		$first_query = $this->first_query_valid();
		
		if( $first_query['robot'] )			
			return true;
			
		return false;
	}
	
}

function have_posts() {
	global $query;

	return $query->have_posts();
}

function the_post() {
	global $query;

	$query->the_post();
}

function get_post( $post = null, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $post ) && isset( $GLOBALS['post'] ) )
		$post = $GLOBALS['post'];

	if ( ! $post )
		return null;

	return $post;
}


function is_page(){
	global $query;
	
	return $query->is_page() ;
}

function is_single(){
	global $query;
	
	return $query->is_single() ;
}

function is_archive(){
	global $query;
	
	return $query->is_archive() ;
}

function is_author(){
	global $query;
	
	return $query->is_author() ;
}

function is_category(){
	global $query;
	
	return $query->is_category() ;
}

function is_tag(){
	global $query;
	
	return $query->is_tag() ;
}

function is_tag_id(){
	global $query;
	
	return $query->is_tag_id() ;
}

function is_search(){
	global $query;
	
	return $query->is_search() ;
}

function is_404(){
	global $query;
	
	return $query->is_404();
	
}

function is_admin(){
	global $query;
	
	return $query->is_admin();
}

function is_signin(){
	global $query;
	
	return $query->is_signin();
	
}

function is_signup(){
	global $query;
	
	return $query->is_signup();
}

function is_activate(){
	global $query;
	
	return $query->is_activate();
}

function is_request(){
	global $query;
	
	return $query->is_request();
}

function is_attachment(){
	global $query;
	
	return $query->is_attachment();
}

function is_robots(){
	global $query;
	
	return $query->is_robots();
}

function is_home(){
	global $query;
	
	return $query->is_home();
}

/**
 * Is the query a query value string 
 *
 * @return bool
 */
function is_query_values() {
	if( isset( $_SERVER['QUERY_STRING'] ) ) 
		return $_SERVER['QUERY_STRING'];
}
/**
 * Retrieve variable in the Query class.
 *
 * @param string $var
 * @return mixed
 */
function get_query_var( $var ) {
	//global $query;

	//return $query->get($var);
	return apply_filters('get_query_var',$_GET[$var]);
}
/**
 * Retrieve variable in the Query class.
 *
 * @param string $var
 * @return mixed
 */
function set_query_var( $var, $value ) {
	//global $query;

	//return $query->get($var);
	$_GET[$var] = apply_filters('get_query_var',$value);
}

function get_admin(){ return get_query_var('admin'); }
function get_load(){ return get_query_var('load'); }
function get_plugin(){ return get_query_var('plugin'); }
function get_redirect(){ return get_query_var('redirect'); }
function get_request(){ return get_query_var('request'); }
?>
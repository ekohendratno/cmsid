<?php 
/**
 * @fileName: query.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class Query{
	public $query;
	public $query_vars = array();
	private $query_vars_changed = true;
	
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
	
	/**
	 * Sets up query by parsing query string.
	 *
	 * @param string $query
	 * @return array
	 */
	public function query() {
        $format_defines = array ( 
        'get'=>'_GET', 
        'post'=>'_POST', 
        'cookie'=>'_COOKIE', 
        'session'=>'_SESSION', 
        'request'=>'_REQUEST', 
        'files'=>'_FILES', 
        ); 
		
		$mode = array();
        foreach ($format_defines as $k ){ 
            $mode[$k] = $GLOBALS[$format_defines[$k]]; 
        } 
		
		return $mode;
	}
	
	/**
	 * Constructor.
	 *
	 * @param string $query
	 * @return Query
	 */
	public function __construct() {
		$this->query();
	}

	/**
	 * Make private properties readable for backwards compatibility.
	 *
	 * @param string $name
	 * @return mixed Property.
	 */
	public function __get( $name ) {
		return $this->$name;
	}

	/**
	 * Make private properties settable for backwards compatibility.
	 *
	 * @param string $name
	 * @return bool
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Make private properties settable for backwards compatibility.
	 *
	 * @param string $name
	 */
	public function __unset( $name ) {
		unset( $this->$name );
	}

	/**
	 * Make private/protected methods readable for backwards compatibility.
	 *
	 * @param callable $name
	 * @param array    $arguments
	 * @return mixed|bool
	 */
	public function __call( $name, $arguments ) {
		return call_user_func_array( array( $this, $name ), $arguments );
	}
	
	
	/**
	 * Retrieve query variable.
	 *
	 * @param string $query_var
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get( $query_var, $default = '' ) {
		if ( isset( $this->query_vars[ $query_var ] ) ) {
			return $this->query_vars[ $query_var ];
		}

		return $default;
	}

	/**
	 * Set query variable.
	 *
	 * @param string $query_var
	 * @param mixed $value
	 */
	public function set($query_var, $value) {
		$this->query_vars[$query_var] = $value;
	}
	
	/**
	 * Retrieve the posts based on query variables.
	 *
	 * @return array
	 */
	public function get_posts() {
		global $db;

		$this->parse_query();
	}
	
	public function parse_query( $query =  '' ) {
	}
}

var_dump( new Query() );

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
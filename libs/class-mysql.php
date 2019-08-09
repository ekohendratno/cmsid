<?php 
/**
 * @fileName: mysql.php
 * @dir: libs/
 */
 
if(!defined('_iEXEC')) exit;

define( 'OBJECT', 'OBJECT' );
define( 'object', 'OBJECT' );
define( 'OBJECT_K', 'OBJECT_K' );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'ARRAY_N', 'ARRAY_N' );


class MySQL{
	var $num_queries = 0;
	
	var $num_rows = 0;
	
	var $rows_affected = 0;
	
	var $insert_id = 0;
	
	var $last_query;
	
	var $show_errors = true;
	
	var $real_escape = false;
	
	var $error = true;
	
	var $field_types = array();
	
	var $old_tables = array( 'users', 'sidebar_action', 'sidebar', 'category', 'comments', 'posts', 'plugins', 'options' );
	
	var $new_tables;
	
	var $last_result;
	
	protected $result;
	
	protected $reconnect_retries = 5;
	
	private $use_mysqli = true;
	
	function __construct( $dbuser, $dbpass, $dbname, $dbhost, $pre_table ) {
		register_shutdown_function( array( &$this, '__destruct' ) );
		
		if ( function_exists( 'mysqli_connect' ) ) {
			if ( version_compare( phpversion(), '5.5', '>=' ) || ! function_exists( 'mysql_connect' ) ) {
				$this->use_mysqli = true;
			} elseif ( false !== strpos( $GLOBALS['version_system'], '-' ) ) {
				$this->use_mysqli = true;
			}
		}
		
		$this->dbuser 	= $dbuser;
		$this->dbpass 	= $dbpass;
		$this->dbname 	= $dbname;
		$this->dbhost 	= $dbhost;
		$this->dbpre	= $pre_table;
		
		$this->connect_mysql();
	}
	
	function __destruct() {
		return true;
	}
	
	function __get( $v ) {
		$this->add_prefix($v);
		
		$this->$v = $this->set_prefix($v);
       	return $this->$v;
   	}
	
	function connect_mysql(){
		if ( $this->use_mysqli ) { //if ( debug ) {
			$this->dbh = mysqli_connect( $this->dbhost, $this->dbuser, $this->dbpass );
		} else {
			$this->dbh = @mysql_connect( $this->dbhost, $this->dbuser, $this->dbpass, true );
		}

		if ( !$this->dbh ) {
			$this->bail( sprintf( "<div class=\"padding\"><div class=\"message padding\"><h1>Kesalahan membangun koneksi database</h1>
			<p>Ini berarti bahwa username dan password informasi dalam file <code>config.php</code> Anda tidak benar atau kami tidak dapat menghubungi server database di <code>%s</code>. Ini bisa berarti database server host Anda sedang down</p>
			<ul>
				<li>Apakah Anda yakin Anda memiliki username dan password yang benar?</li>
				<li>Apakah Anda yakin bahwa Anda telah mengetik nama host yang benar?</li>
				<li>Apakah Anda yakin bahwa database server berjalan?</li>
			</ul>
			<p>Jika Anda tidak yakin dengan istilah tersebut Anda mungkin harus menghubungi host Anda.</p></div></div>
			", $this->dbhost ), 'db_connect_fail' );

			return;
		}
		$this->select_db( $this->dbname, $this->dbh );
	}
	
	function select_db( $db, $dbh = null) {
		if ( is_null($dbh) )
			$dbh = $this->dbh;

		$mysql_select_db = false;
		if ( $this->use_mysqli ) {
			$mysql_select_db = @mysqli_select_db( $dbh,$db );
		}else{
			$mysql_select_db = @mysql_select_db( $db, $dbh );
		}
			
		if(!$mysql_select_db){
			$this->bail( sprintf( '<div class=\"padding\"><div class="message padding"><h1>Tidak dapat memilih database</h1>
			<p>Kami dapat terhubung ke server database (yang berarti nama pengguna dan kata sandi Anda oke) namun tidak dapat memilih database <code>%1$s</code> .</p>
			<ul>
			<li>Apakah Anda yakin itu ada?</li>
			<li>Pastikan file <code>config.php</code> dihapus pada saat melakukkan instalasi?</li>
			<li>Apakah <code>%2$s</code> memiliki izin untuk menggunakan database <code>%1$s</code>?</li>
			<li>Pada sebagian sistem nama database Anda diawali dengan nama pengguna Anda, sehingga akan menjadi seperti <code>username_%1$s</code>. Mungkinkah itu masalahnya?</li>
			</ul>
			<p>Jika Anda tidak tahu cara mengatur database <strong>Anda harus menghubungi host Anda</strong>.</p></div></div>', $db, $this->dbuser ), 'db_select_fail' );
			return;
		}
	}
	
	
	function tables( $new_table, $prefix = true ) {
		
		if(!empty($new_table)) $tables = array_merge( (array)$new_table, (array)$this->old_tables );
		else $tables = $this->old_tables;
		
		if ( $prefix ) {
			
			foreach ( $tables as $k => $table ) {
				$tables[ $table ] = $this->dbpre . $table;
				unset( $tables[ $k ] );
			}

		}

		return $tables;
	}
	
	
	function add_table($new){
		if( !empty($new) && (string)$new ) 
			$this->new_tables = $new; 
	}
	
	function add_prefix($table){
		if(!empty($table)){
			if(!in_array($table,$this->old_tables) )			
			return $this->add_table($table);
		}
		
	}
	
	function set_prefix( $load_table ) {

		if ( $load_table ) {
			
			if(!$this->new_tables) $new_table = null;
			else $new_table = $this->new_tables;

			foreach($this->tables($new_table) as $k => $v) {
				if($load_table == $k) $load = $v;
			}
		}
		return $load;
	}
	
	function prepare( $query = null ) {
		if ( is_null( $query ) )
			return;
		$args 	= func_get_args();
		array_shift( $args );
		if ( isset( $args[0] ) && is_array($args[0]) )
		$args 	= $args[0];
		$query 	= str_replace( "'%s'", '%s', $query );
		$query 	= str_replace( '"%s"', '%s', $query );
		$query 	= preg_replace( '|(?<!%)%s|', "'%s'", $query );
		return @vsprintf( $query, $args );
	}
	
	
	private function _do_query( $query ) {

		if ( $this->use_mysqli ) {
			$this->result = @mysqli_query( $this->dbh, $query );
		} else {
			$this->result = @mysql_query( $query, $this->dbh );
		}
		$this->num_queries++;
		
	}

	/*
	* $db->query('select * from table_name');
	*
	*/
	function query( $query ){
		$this->flush();
		$this->last_query = $query;
		$this->_do_query( $query );
		
		$mysql_errno = 0;
		if ( ! empty( $this->dbh ) ) {
			if ( $this->use_mysqli ) {
				$mysql_errno = mysqli_errno( $this->dbh );
			} else {
				$mysql_errno = mysql_errno( $this->dbh );
			}
		}

		if ( empty( $this->dbh ) || 2006 == $mysql_errno ) {
			if ( $this->check_connection() ) {
				$this->_do_query( $query );
			} else {
				$this->insert_id = 0;
				return false;
			}
		}

		// If there is an error then take note of it..
		if ( $this->use_mysqli ) {
			$this->last_error = mysqli_error( $this->dbh );
		} else {
			$this->last_error = mysql_error( $this->dbh );
		}
		
		if ( $this->last_error ) {
			// Clear insert_id on a subsequent failed insert.
			if ( $this->insert_id && preg_match( '/^\s*(insert|replace)\s/i', $query ) )
				$this->insert_id = 0;

			//$this->print_error();
			return false;
		}

		if ( preg_match( '/^\s*(create|alter|truncate|drop)\s/i', $query ) ) {
			$return_val = $this->result;
		} elseif ( preg_match( '/^\s*(insert|delete|update|replace)\s/i', $query ) ) {
			if ( $this->use_mysqli ) {
				$this->rows_affected = mysqli_affected_rows( $this->dbh );
			} else {
				$this->rows_affected = mysql_affected_rows( $this->dbh );
			}
			// Take note of the insert_id
			if ( preg_match( '/^\s*(insert|replace)\s/i', $query ) ) {
				if ( $this->use_mysqli ) {
					$this->insert_id = mysqli_insert_id( $this->dbh );
				} else {
					$this->insert_id = mysql_insert_id( $this->dbh );
				}
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$num_rows = 0;
			if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
				while ( $row = @mysqli_fetch_object( $this->result ) ) {
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}
			} else if ( is_resource( $this->result ) ) {
				while ( $row = @mysql_fetch_object( $this->result ) ) {
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}
			}

			// Log number of rows the query returned
			// and return number of rows selected
			$this->num_rows = $num_rows;
			$return_val     = $num_rows;
		}

		return $return_val;
	}	
	
	/*
	* $user_name='';
	* $user_pass='';
	*
	* $data = compact('user_name','user_pass');
	* $db->replace( 'table_name', $data + compact( 'user_login' ));
	* 
	*/
	
	function replace( $table, $data, $format = null ) {
		return $this->_ir( $table, $data, $format, 'REPLACE' );
	}	
	/*
	* $user_name='';
	* $user_pass='';
	*
	* $data = compact('user_name','user_pass');
	* $db->insert( 'table_name', $data + compact( 'user_login' ));
	* 
	*/
	function insert( $table, $data, $format = null ) {
		return $this->_ir( $table, $data, $format, 'INSERT' );
	}
	
	function _ir( $table, $data, $format = null, $type = 'INSERT' ) {
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		if ( ! in_array( strtoupper( $type ), array( 'REPLACE', 'INSERT' ) ) )
			return false;
		$formats = $format = (array) $format;
		$fields = array_keys( $data );
		$formatted_fields = array();
		foreach ( $fields as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$formatted_fields[] = escape( $form );
		}
		$sql = "{$type} INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES ('" . implode( "','", $formatted_fields ) . "')";
		return $this->query( $this->prepare( $sql, $data ) );
	}
	/*
	* $user_name='';
	* $user_pass='';
	*
	* $data = compact('user_name','user_pass');
	* $db->update( 'table_name', $data, compact( 'user_login' ) );
	* 
	*/
	function update( $table, $data, $where, $format = null, $where_format = null ){
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		if ( ! is_array( $data ) || ! is_array( $where ) )
			return false;

		$formats = $format = (array) $format;
		$bits = $wheres = array();
		foreach ( (array) array_keys( $data ) as $field ) {
			if ( !empty( $format ) )
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			elseif ( isset($this->field_types[$field]) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$values   = escape( $form );
			$bits[]   = "`$field` = {$values}";
		}

		$where_formats = $where_format = (array) $where_format;
		foreach ( (array) array_keys( $where ) as $field ) {
			if ( !empty( $where_format ) )
				$form = ( $form = array_shift( $where_formats ) ) ? $form : $where_format[0];
			elseif ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$values	  = escape( $form );
			$wheres[] = "`$field` = {$values}";
		}
		$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, array_merge( array_values( $data ), array_values( $where ) ) ) );
	}
	
	function select( $table, $where=false, $order=false ){
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		if(!$where){
			if($order){
			//order true
			$sql 	= "SELECT * FROM `$table` ".$order;
			}else{
			//order false & where false
			$sql 	= "SELECT * FROM `$table`";
			}
		}else{
			//where true
			if ( ! is_array( $where ) )
				return false;
				
			$wheres = array();
			foreach ( (array) $where as $field => $value) {
				if ( isset( $this->field_types[$field] ) )
					$form = $this->field_types[$field];
				else
					$form = $value;
				$wheres[] = $field."='".escape( $form )."'";
			}
			//if order true
			if($order)
			$sql 	= "SELECT * FROM `$table` WHERE " . implode( ' AND ', $wheres ) ." ".$order;			
			else
			$sql 	= "SELECT * FROM `$table` WHERE " . implode( ' AND ', $wheres );
			
		}
		return $this->query( $sql );
	}
	
	function delete( $table, $where=false){
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		//where true
		if ( ! is_array( $where ) )
			return false;
				
		$wheres = array();
		foreach ( (array) $where as $field => $value) {
			if ( isset( $this->field_types[$field] ) )
				$form = $this->field_types[$field];
			else
				$form = $value;
			$wheres[] = $field."='".escape( $form )."'";
		}
			
		$sql 	= "DELETE FROM `$table` WHERE " . implode( ' AND ', $wheres );
		return $this->query( $sql );
	}
	
	function truncate( $table ){
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		$sql 	= "TRUNCATE TABLE  `$table`";
		return $this->query( $sql );
	}
	
	function drop( $table ){
		$this->add_prefix($table);
		$table = $this->set_prefix($table);
		
		$sql 	= "DROP TABLE IF EXISTS `$table`";
		return $this->query( $sql );
	}
	
	
	public function get_row( $query = null, $output = OBJECT, $y = 0 ) {
		if ( $query ) {
			$this->query( $query );
		} else {
			return null;
		}

		if ( !isset( $this->last_result[$y] ) )
			return null;

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
		} elseif ( strtoupper( $output ) === OBJECT ) {
			// Back compat for OBJECT being previously case insensitive.
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} else {
			$this->print_error( " Output type must be one of: OBJECT, ARRAY_A, ARRAY_N" );
		}
	}
	
	public function get_var( $query = null, $x = 0, $y = 0 ) {

		if ( $query ) {
			$this->query( $query );
		}

		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) ) {
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
		}

		// If there is a value return it else return null
		return ( isset( $values[$x] ) && $values[$x] !== '' ) ? $values[$x] : null;
	}
	
	public function get_col( $query = null , $x = 0 ) {
		if ( $query ) {
			$this->query( $query );
		}

		$new_array = array();
		// Extract the column values
		for ( $i = 0, $j = count( $this->last_result ); $i < $j; $i++ ) {
			$new_array[$i] = $this->get_var( null, $x, $i );
		}
		return $new_array;
	}
	
	public function get_results( $query = null, $output = OBJECT ) {

		if ( $query ) {
			$this->query( $query );
		} else {
			return null;
		}

		$new_array = array();
		if ( $output == OBJECT ) {
			// Return an integer-keyed array of row objects
			return $this->last_result;
		} elseif ( $output == OBJECT_K ) {
			// Return an array of row objects with keys from column 1
			// (Duplicates are discarded)
			foreach ( $this->last_result as $row ) {
				$var_by_ref = get_object_vars( $row );
				$key = array_shift( $var_by_ref );
				if ( ! isset( $new_array[ $key ] ) )
					$new_array[ $key ] = $row;
			}
			return $new_array;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				foreach( (array) $this->last_result as $row ) {
					if ( $output == ARRAY_N ) {
						// ...integer-keyed row arrays
						$new_array[] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays
						$new_array[] = get_object_vars( $row );
					}
				}
			}
			return $new_array;
		} elseif ( strtoupper( $output ) === OBJECT ) {
			// Back compat for OBJECT being previously case insensitive.
			return $this->last_result;
		}
		return null;
	}
	
	
	public function flush() {
		$this->last_result = array();
		$this->last_query  = null;
		$this->rows_affected = $this->num_rows = 0;
		$this->last_error  = '';

		if ( $this->use_mysqli && $this->result instanceof mysqli_result ) {
			mysqli_free_result( $this->result );
			$this->result = null;

			// Sanity check before using the handle
			if ( empty( $this->dbh ) || !( $this->dbh instanceof mysqli ) ) {
				return;
			}

			// Clear out any results from a multi-query
			while ( mysqli_more_results( $this->dbh ) ) {
				mysqli_next_result( $this->dbh );
			}
		} else if ( is_resource( $this->result ) ) {
			mysql_free_result( $this->result );
		}
	}
	
	
	public function check_connection( $allow_bail = true ) {
		if ( $this->use_mysqli ) {
			if ( @mysqli_ping( $this->dbh ) ) {
				return true;
			}
		} else {
			if ( @mysql_ping( $this->dbh ) ) {
				return true;
			}
		}

		$error_reporting = false;

		// Disable warnings, as we don't want to see a multitude of "unable to connect" messages
		if ( debug ) {
			$error_reporting = error_reporting();
			error_reporting( $error_reporting & ~E_WARNING );
		}

		for ( $tries = 1; $tries <= $this->reconnect_retries; $tries++ ) {
			// On the last try, re-enable warnings. We want to see a single instance of the
			// "unable to connect" message on the bail() screen, if it appears.
			if ( $this->reconnect_retries === $tries && debug ) {
				error_reporting( $error_reporting );
			}

			if ( $this->db_connect( false ) ) {
				if ( $error_reporting ) {
					error_reporting( $error_reporting );
				}

				return true;
			}

			sleep( 1 );
		}

		if ( ! $allow_bail ) {
			return false;
		}

		// We weren't able to reconnect, so we better bail.
		$this->bail( sprintf( ( "
<h1>Kesalahan menghubungkan kembali ke database </h1>
<p>Ini berarti bahwa kita kehilangan kontak dengan server database di <code>% s </code>. Ini bisa berarti server database host Anda sedang down. </p>
<ul>
<li>Apakah Anda yakin bahwa database server berjalan? </li>
<li>Apakah Anda yakin bahwa server database tidak di bawah beban sangat berat? </li>
</ul>
<p> Jika Anda tidak yakin apa istilah tersebut Anda mungkin harus menghubungi host Anda. </p>
" ), htmlspecialchars( $this->dbhost, ENT_QUOTES ) ), 'db_connect_fail' );

	}
	
	public function print_error( $str = '' ) {

		if ( !$str ) {
			if ( $this->use_mysqli ) {
				$str = mysqli_error( $this->dbh );
			} else {
				$str = mysql_error( $this->dbh );
			}
		}

		if ( $caller = $this->get_caller() )
			$error_str = sprintf( 'Database error %1$s for query %2$s made by %3$s', $str, $this->last_query, $caller );
		else
			$error_str = sprintf( 'Database error %1$s for query %2$s', $str, $this->last_query );

		error_log( $error_str );

		// Are we showing errors?
		if ( ! $this->show_errors )
			return false;

		// If there is an error then take note of it
		$str   = htmlspecialchars( $str, ENT_QUOTES );
		$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

		print "<div id='error'>
			<p class='dberror'><strong>Database error:</strong> [$str]<br />
			<code>$query</code></p>
			</div>";
	}
	
	function bail( $message, $error_code = '500' ) {
		if ( !$this->show_errors ) {
			if ( class_exists( 'Error' ) )
				$this->error = new Error($error_code, $message);
			else
				$this->error = $message;
			return false;
		}
		die($message);
	}
	
	function get_caller(){
		return debug_backtrace_summary( __CLASS__ );
	}
	
}
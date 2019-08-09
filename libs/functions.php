<?php 
/**
 * @fileName: functions.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

function redirect( $url = null ){	
	$base_url = site_url( $url );
	
    if (!headers_sent()){ 
        header('Location: '.$base_url); exit;
    }else{ 
        echo '<script type="text/javascript">';
        echo 'window.location.href="'.$base_url.'";';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$base_url.'" />';
        echo '</noscript>'; 
		return;
		exit;
    }
}

function array_merge_simple( $array1, $array2 ){
	$merged = array();
	
	if( !empty($array1) )
	foreach ( $array1 as $key => $value ){
		$merged [$key] = $value;
	}
	  
	if( !empty($array2) )
	foreach ( $array2 as $key => $value ){
		$merged [$key] = $value;
	}
	
	return $merged;
}

function object_merge_simple( $array1, $array2 ){
	$merged = array();
	
	if( !empty($array1) )
	foreach ( $array1 as $key => $value ){
		$merged[$key] = $value;
	}
	  
	if( !empty($array2) )
	foreach ( $array2 as $key => $value ){
		$merged[$key] = $value;
	}
	
	return (object) $merged;
}
/**
 * Mengetahui protokol
 *
 * @return true|false
 */
function is_ssl() {
	if ( isset($_SERVER['HTTPS']) ) {
		if ( 'on' == strtolower($_SERVER['HTTPS']) )
			return true;
		if ( '1' == $_SERVER['HTTPS'] )
			return true;
	} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
		return true;
	}
	return false;
}
/**
 * Menghapus direktori folder
 *
 * @param string $dirname
 * @return true|false
 */
function delete_directory($dir) {
	if (!file_exists($dir)) return true; 
	if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
		foreach (scandir($dir) as $item) { 
			if ($item == '.' || $item == '..') continue; 
			if (!delete_directory($dir . "/" . $item)) { 
				chmod($dir . "/" . $item, 0777); 
				if (!delete_directory($dir . "/" . $item)) return false; 
			}; 
		} 
	return rmdir($dir); 
}
/**
 * Memvalidasi berkas
 *
 * @param string $file
 * @param array $allowed_files
 * @return int
 */
function validate_file( $file, $allowed_files = '' ) {
	if ( false !== strpos( $file, '..' ) )
		return 1;

	if ( false !== strpos( $file, './' ) )
		return 1;

	if ( ! empty( $allowed_files ) && ! in_array( $file, $allowed_files ) )
		return 3;

	if (':' == substr( $file, 1, 1 ) )
		return 2;

	return 0;
}
/**
 * Gets the current locale.
 *
 * @return string
 */
function get_locale() {
	global $locale;
	
	if ( empty( $locale ) )
		$locale = 'en_US';

	return apply_filters( 'locale', $locale );
}

function get_option_array_widget(){
	$get_option_array_widget = get_option('sidebar_widgets');
	$get_option_array_widget = esc_sql( $get_option_array_widget );
	$get_option_array_widget = json_decode( $get_option_array_widget );
	return $get_option_array_widget;
}

function dateformat($str, $format = null){
	$str = strtotime($str);
	return date("Y/m/d",$str);
	/*
	$today = date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
	$today = date("m.d.y");                         // 03.10.01
	$today = date("j, n, Y");                       // 10, 3, 2001
	$today = date("Ymd");                           // 20010310
	$today = date('h-i-s, j-m-y, it is w Day');     // 05-16-18, 10-03-01, 1631 1618 6 Satpm01
	$today = date('\i\t \i\s \t\h\e jS \d\a\y.');   // it is the 10th day.
	$today = date("D M j G:i:s T Y");               // Sat Mar 10 17:16:18 MST 2001
	$today = date('H:m:s \m \i\s\ \m\o\n\t\h');     // 17:03:18 m is month
	$today = date("H:i:s");                         // 17:16:18
	*/
}

function mysql2date( $format, $date ) {
	if ( empty( $date ) )
		return false;

	if ( 'G' == $format )
		return strtotime( $date . ' +0000' );

	$i = strtotime( $date );

	if ( 'U' == $format )
		return $i;

	return date( $format, $i );
}

/**
 * Gets the limit content.
 *
 * @return string limit
 */
function limittxt($nama, $limit){
    if (strlen ($nama) > $limit) {
    	$nama = substr($nama, 0, $limit) . '...';
    }else {
        $nama = $nama;
    }
	return apply_filters( 'limit_txt', $nama );
}
/**
 * Gets the sanitaze and limit content.
 *
 * @return string
 */
function initialized_text( $text, $limit = 120, $tags = false  ){
	$text = htmlentities( strip_tags($text) );
	if( $limit ) $text = limittxt( $text, $limit );
	if( $tags ) $text = empty($text) ? implode(',',explode(' ',$text)) : $text;
	return $text;
}


function check_ip_address( $ip ) {
	$bytes = explode('.', $ip);
	if (count($bytes) == 4 or count($bytes) == 6) {
		$returnValue = true;
		foreach ($bytes as $byte) {
			if (!(is_numeric($byte) && $byte >= 0 && $byte <= 255))
				$returnValue = false;
		}
		return $returnValue;
	}
	return false;
}

function get_ip_address(){
	$banned = array ('127.0.0.1', '192.168', '10');
	$ip_adr = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$bool = false;
	foreach ($banned as $key=>$val){
		if(!empty($ip_adr) && ereg("^$val",$ip_adr) ){
			$bool = true;
			break;
		}
	}
	
	if (empty($ip_adr) or $bool or !check_ip_address($ip_adr) ){
		$ip_adr = @$_SERVER['REMOTE_ADDR'];	
	}
	return $ip_adr; 	
}

function security_posted( $file_name = null, $cek_ip_total = false, $timer = 10 ){
	$ip_total = 0;
	$timed = time();
	$ip = get_ip_address();
	$pip_array1 = $pip_array2 = array();
	
	if( checked_option( 'security_pip' ) &&  get_option('security_pip') != '' ){
		
		$option_pip = get_option('security_pip');
		//echo 'before DELETE:'.$option_pip.'<br>';
		$option_pip = json_decode( $option_pip );
		foreach( $option_pip as $pip ){
			
			if( $pip->time > $timed )
				$pip_array1[] = array('file' => $pip->file,'ip' => $pip->ip,'time' => $pip->time);
		}
		
		$security_pip = json_encode($pip_array1);
		//echo 'after DELETE:'.$security_pip.'<br>';
		if( checked_option( 'security_pip' ) ) set_option( 'security_pip', $security_pip );
		else add_option( 'security_pip', $security_pip );
		
		foreach( $option_pip as $pip ){
			
			if( $pip->file == $file_name && $pip->ip == $ip && $pip->time > $timed )
				$ip_total = $ip_total+1;
		}
	}
	
	if( $cek_ip_total )
		return $ip_total;
	else{
	
		$timer = ($timed * $timer);
		$pip_array2[] = array('file' => $file_name,'ip' => $ip, 'time' => $timer );
		//$security_pip = json_encode($pip_array2);
		//echo 'before INSERT NEW:'.$security_pip.'<br>';
		$pip_array3 = array_merge($pip_array2,$pip_array1);
		//$security_pip = json_encode($pip_array3);
		//echo 'after INSERT NEW MERGE using OLD:'.$security_pip.'<br>';
		$security_pip = json_encode($pip_array3);
			
		if( checked_option( 'security_pip' ) ) set_option( 'security_pip', $security_pip );
		else add_option( 'security_pip', $security_pip );		
	}
		
}

/**
 * Mengecek tanggal dan waktu berdasarkan kata
 *
 * @param int $session_time
 * @return string
 */
function date_stamp($session_time, $language = 'id'){ 
	$date 		= new DateTime($session_time);
	$timestamp 	= $date->format('U');
	$timestamp 	= time_stamp( $timestamp );
	return $timestamp;
}
/**
 * Mengecek tanggal dan waktu berdasarkan kata
 *
 * @param int $session_time
 * @return string
 */
function time_stamp($session_time, $language = 'id'){ 
	 
	$time_difference 	= time() - $session_time ; 
	$seconds 			= $time_difference ; 
	$minutes 			= round($time_difference / 60 );
	$hours 				= round($time_difference / 3600 ); 
	$days 				= round($time_difference / 86400 ); 
	$weeks 				= round($time_difference / 604800 ); 
	$months 			= round($time_difference / 2419200 ); 
	$years 				= round($time_difference / 29030400 ); 
	
	
	if( $language == 'id' ):
		$lang[0] = 'satu';
		$lang[1] = 'detik';
		$lang[2] = 'menit';
		$lang[3] = 'jam';
		$lang[4] = 'hari';
		$lang[5] = 'minggu';
		$lang[6] = 'bulan';
		$lang[7] = 'tahun';
		$lang[8] = 'yg lalu';
	else:
		$lang[0] = 'one';
		$lang[1] = 'seconds';
		$lang[2] = 'minutes';
		$lang[3] = 'hours';
		$lang[4] = 'day';
		$lang[5] = 'week';
		$lang[6] = 'month';
		$lang[7] = 'years';
		$lang[8] = 'ago';
	endif;
	
	if($seconds <= 60){
	$retval = "$seconds $lang[1] $lang[8]"; 
	}else if($minutes <=60){
		if($minutes==1) $retval = "$lang[0] $lang[2] $lang[8]"; 
		else $retval = "$minutes $lang[2] $lang[8]"; 
	}
	else if($hours <=24){
	   if($hours==1) $retval = "$lang[0] $lang[3] $lang[8]";
	   else $retval = "$hours $lang[3] $lang[8]";
	}
	else if($days <=7){
	  if($days==1) $retval = "$lang[0] $lang[4] $lang[8]";
	  else $retval = "$days $lang[4] $lang[8]";	  
	}
	else if($weeks <=4){
	  if($weeks==1) $retval = "$lang[0] $lang[5] $lang[8]";
	  else $retval = "$weeks $lang[5] $lang[8]";
	}
	else if($months <=12){
	   if($months==1) $retval = "$lang[0] $lang[6] $lang[8]";
	   else $retval = "$months $lang[6] $lang[8]";   
	}	
	else{
		if($years==1) $retval = "$lang[0] $lang[7] $lang[8]";
		else $retval = "$years $lang[7] $lang[8]";	
	}
	
	return $retval;	
	
}

function default_date( $datetime, $args, $display = true ){
	

	$defaults = array(
		'jam' => false,
		'hari' => false, 
		'tanggal' => false,
		'bulan' => true,
		'tahun' => true,
	);

	$r = parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );
	
	$datetime = strtotime($datetime);
	$bln_array = array (
		'01'=>'Januari',
		'02'=>'Februari',
		'03'=>'Maret',
		'04'=>'April',
		'05'=>'Mei',
		'06'=>'Juni',
		'07'=>'Juli',
		'08'=>'Augustus',
		'09'=>'September',
		'10'=>'Oktober',
		'11'=>'Nopember',
		'12'=>'Desember'
				);
	$hari_arr = array (	
		'0'=>'Minggu',
		'1'=>'Senin',
		'2'=>'Selasa',
		'3'=>'Rabu',
		'4'=>'Kamis',
		'5'=>'Jum\'at',
		'6'=>'Sabtu'
		);
		
		
	$jam 	= $jam ? date ('H:i',$datetime) : '';
	$hari 	= $hari ? @$hari_arr[date('w',$datetime)] : '';
	$tanggal= $tanggal ? date('d',$datetime) : '';
	$bln 	= $bulan ? @$bln_array[date('m',$datetime)] : '';
	$tahun 	= $tahun ? date('Y',$datetime) : '';
	
	$time = $jam . ' ' . $hari . ' ' . $tanggal . ' ' . $bln . ' ' .$tahun ;
	
	
	if ( $display )
		echo $time;
	else
		return $time;
}

function get_gravatar( $email, $default = '', $s = 50, $d = 'mm', $r = 'g', $img = false, $atts = array()) {
	
	$set_avatar = get_option('avatar_default');
	if($set_avatar != $default && empty($default))
	$default = $set_avatar;
	
	if($default=='blank'):
	$out = site_url('/libs/img/blank.gif');
	else:
	
	$email_hash = md5( strtolower( $email ) );
	if($img){
		$host = 'https://secure.gravatar.com';
	}else{
		if ( !empty($email) ){
			$host = sprintf( "http://%d.gravatar.com", ( hexdec( $email_hash{0} ) % 2 ) );
		}else{
			$host = 'http://0.gravatar.com';
		}
		$out = $host.'/avatar/s='.$s;
	}
	if ( 'mystery' == $default )
		$default = "$host/avatar/ad516503a11cd5ca435acc9bb6523536?s=".$s; 
		// ad516503a11cd5ca435acc9bb6523536 == md5('unknown@gravatar.com')
	elseif ( !empty($email) && 'gravatar_default' == $default )
		$default = '';
	elseif ( 'gravatar_default' == $default )
		$default = $host."/avatar/s=".$s;
	elseif ( empty($email) )
		$default = $host."/avatar/?d=".$default."&amp;s=".$s;
		
	if ( !empty($email) ) {
		$out  = $host."/avatar/";
		$out .= $email_hash;
		$out .= '?s='.$s;
		$out .= '&amp;d='.urlencode($default);
	}
	endif;
	return $out;
}

function avatar_url( $user_login, $w = 120, $h = 120, $zc = 1 ){
	global $user;
	
	
	if( valid_mail($user_login) && $user_email = $user_login ){
		$where = compact('user_email');				
	}
	else
	{			
		$where = compact('user_login');
	}	
	
	$field = $user->data( $where );
	
	if( !checked_option( 'avatar_type' ) ) 
		add_option( 'avatar_type', 'gravatar' );
	
	if( get_option('avatar_type') == 'gravatar' && $field->user_status > 0 ){
		$url_image_profile = get_gravatar($field->user_email);
	}elseif( get_option('avatar_type') == 'computer' ){
		$url_image_profile = '/libs/img/avatar_default.png';
			
		if( file_exists( upload_path . '/avatar_'.$field->user_avatar) && $field->user_status > 0 ): 
			$url_image_profile = '/content/uploads/avatar_' . $field->user_avatar;
		endif;
			
		$url_image_profile = site_url($url_image_profile);
	}else{
		$url_image_profile = includes_url('/img/avatar_default.png');
	}
	
	$retval_url = '?request&load=libs/timthumb.php';
	$retval_url.= '&src='.$url_image_profile;
	$retval_url.= '&w='.$w;
	$retval_url.= '&h='.$h;
	$retval_url.= '&zc='.$zc;
	
	if( get_option('avatar_type') == 'gravatar' && $field->user_status > 0 )
		$retval_url = $url_image_profile;
	
	return $retval_url;
}

function get_file_data( $file, $default_headers, $context = '' ) {
	if(!file_exists($file) ) 
	return false;
	
	$fp 		= fopen( $file, 'r' );
	$file_data 	= fread( $fp, 8192  ); //8kiB
	fclose( $fp );
	
	foreach ( $default_headers as $field => $regex ) {
		preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
		if ( !empty( ${$field} ) )
			${$field} = cleanup_header(${$field}[1]);
		else
			${$field} = '';
	}

	$data = compact( array_keys( $default_headers ) );

	return $data;
}

function save_to_file($file){
	$content =  stripslashes(trim ($_POST['content']));
	// Let's make sure the file exists and is writable first.
		if (is_writable($file)) {
		   if (!$handle = @fopen($file, 'w+')) {
				echo'<div class="info">Can\'t read a file ('.get_file_name($file).')</div>';
				exit;
		   }
		   if (fwrite($handle, $content) === FALSE) {
				$return='<div id="error">Can\'t write a file('.get_file_name($file).')</div>';
			   
			   exit;
		   } 
			   //clearstatcache($handle);
			fflush($handle);
			fclose($handle);
			echo '<div id="success">Success save to file ('.get_file_name($file).')</div>'; 
		} else {
		    echo '<div class="error">File $file can\'t write</div>';		   
		}
}

/**
 * memperbaharui widget
 */
function set_dashboard_admin( $string ){	
	/*
	$sorted = array();
	$sorted['normal'] = 'box1,box2';
	$sorted['side'] = 'box1,box2';
	*/	
	$string = esc_sql($string);
	
	if( checked_option( 'dashboard_widget' ) ) set_option( 'dashboard_widget', $string );
	else add_option( 'dashboard_widget', $string );
}
/**
 * Pengurutan array berdasarkan kolom nama array
 *
 * @param array $array_sort
 * @param array $cols_sort
 * @return array
 */
function array_multi_sort($array_sort, $cols_sort = array() ){
    $colarr = array();
    foreach ($cols_sort as $col => $order) {
        $colarr[$col] = array();
        foreach ($array_sort as $k => $row) { 
			$colarr[$col]['_'.$k] = strtolower($row[$col]); 
		}
    }
    $eval = 'array_multisort(';
    foreach ($cols_sort as $col => $order) {
        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array_sort[$k];
            $ret[$k][$col] = $array_sort[$k][$col];
        }
    }
    return $ret;

}

/**
 * Query security for ilegal operation
 *
 * @return false
 */
function query_security(){
	$attacked = array('ad_click','%20union%20','/*','*/union/*','c2nyaxb0','+union+','cmd=','&cmd','exec','execu','concat');
				
	if( is_query_values() && ( !stripost( is_query_values(), $attacked[0]) ) ) :
		if( stripost( is_query_values(), $attacked[1] ) or 
			stripost( is_query_values(), $attacked[2] ) or 
			stripost( is_query_values(), $attacked[3] ) or 
			stripost( is_query_values(), $attacked[4] ) or 
			stripost( is_query_values(), $attacked[5] ) or (
			stripost( is_query_values(), $attacked[6] ) and !
			stripost( is_query_values(), $attacked[7] )) or (
			stripost( is_query_values(), $attacked[8] ) and !
			stripost( is_query_values(), $attacked[9] )) or 
			stripost( is_query_values(), $attacked[10] ))
			die('Ilegal Operation');
	endif;
	return true;
}

/*
$sidebar_action = array();
$sidebar_action['post'] = array('sidebar-1' => 1,'sidebar-2' => 0 );
$sidebar_action['download'] = array('sidebar-1' => 1,'sidebar-2' => 0 );
//echo json_encode($sidebar_action);


$sidebar_action_op = get_option('sidebar_actions');
$sidebar_action_op = json_decode( $sidebar_action_op );

$i  = 'sidebar-2';
$op = get_query('p');
if( $op && count($sidebar_action_op->$op) > 0 ):
foreach( $sidebar_action_op->$op as $sidebar_id => $status ){
	if( $sidebar_id == $i )
	echo $op.'=>'.$sidebar_id.':'.$status.'<br>';
}
echo count( (array)$sidebar_action_op->$op);
endif;*/

/**
 * mengubah spasi
 *
 * @return string lower
 */
function feed_add_space($string){
	if( empty($string) ) 
		return false;
	
	$string = html_entity_decode($string);
	$string = strtolower(preg_replace("/[^A-Za-z0-9-]/","-",$string));
	return $string;
}
/**
 * membaca xml 
 *
 * @return array
 */
function ul_feed( $feed ){
	$feed_content = '';		
	$feed_content.= '<ul class="ul-box">';
	if (is_array($feed)) {
	foreach($feed as $item)	{
		$feed_content .= '<li>
		<a href="'.$item->link.'" title="'.$item->title.'" target="_blank">'.$item->title.'</a>';
		if( !empty($item->author) || !empty($item->date) ):
			$feed_content .= '<div style="color:#333;">';
			if( !empty($item->author) ) $feed_content.= $item->author.' - ';
			if( !empty($item->date) ) $feed_content.= datetimes($item->date, false);
			$feed_content.= '</div>';
		endif;
		
		if( !empty($item->desc) ) 
			$feed_content.= '<div style="color:#333">'.initialized_text( filter_clean($item->desc) ).'</div>';
		
		$feed_content.= '</li>';	
	}}
	$feed_content.= '</ul>';
	return $feed_content;
}

function doing_feed(){	
	$json = new JSON();

	$news_feeds_default = array(
		'news_feeds' => array( 'cmsid.org Feed' => 'http://cmsid.org/rss.xml'),
		'display' => array('desc' => 0,'author' => 0,'date' => 0,'limit' => 10)
	);
	
	$news_feeds_default = $json->encode( $news_feeds_default );
	
	$news_feeds_old_value = get_option('feed-news');
	
	if( !empty($news_feeds_old_value) ) $feed_obj = $news_feeds_old_value;
	else $feed_obj = $news_feeds_default;
	
	$feed_obj = $json->decode( $feed_obj );
	
	$news_feeds_old = $feed_obj->{'news_feeds'};
	$display = $feed_obj->{'display'};
	
	if ( !class_exists('Rss') )
	require_once( libs_path . '/class-rss.php' );

	$Rss = new Rss;	
	
	$rssfeed_temp = $_temp = array();		
	foreach( $news_feeds_old as $title => $feed_url ):
		/*
			XML way
		 */
		try {
			
			$feed = $Rss->getFeed($feed_url, Rss::XML);
			
		}catch (Exception $e) {
			$error = $e->getMessage();
		}
		
		$rssfeed_temp[] = array( 
			'feed_title' => $title, 
			'feed_url' => $feed_url, 
			'feed_content' => $feed, 
			'error' => $error
		); 
	endforeach;
	
	foreach( $rssfeed_temp as $feed_item ):
		if (is_array($feed_item['feed_content'])) {	
			$i = 0;	
			foreach($feed_item['feed_content'] as $item):
				if( $i <= $display->{'limit'} ){
				
					$feed_content[$i]['title'] = $item['title'];
					$feed_content[$i]['link'] = $item['link'];
					
					if( $display->{'desc'} == 1 )
					$feed_content[$i]['desc'] = limittxt( filter_clean($item['description']),120);
					
					if( $display->{'author'} == 1 || $display->{'date'} == 1 ):
						if( $display->{'author'} == 1 ) $feed_content[$i]['author'] = $item['4'];
						if( $display->{'date'} == 1 ) $feed_content[$i]['date'] = $item['date'];
					endif;
					
				}
			$i++;
			endforeach;
		}
			
			$_temp[] = array( 
					'feed_title' => $feed_item[feed_title], 
					'feed_url' => $feed_item[feed_url], 
					'feed_content' => $feed_content,
					'error' => $feed_item[error]
				);
		//endif;
	endforeach;
	
	//$rssfeed_x = array_merge_simple($rssfeed_x, array( 'display' => $display) ); 	
	return json_encode( $_temp );
}

function try_xml( $url ){   		
	$xml = get_content($url);		
	$val = simplexml_load_string($xml);
	return $val;
}

function add_dialog_popup( $args = '' ){
	global $version_system;
	echo '<div id="error"><strong>Error:</strong> function add_dialog_popup() not valid in your version '.$version_system.',.. please upgrade your plugin or system</div>';
	return;
}

function debug_backtrace_summary( $ignore_class = null, $skip_frames = 0, $pretty = true ) {
	if ( version_compare( PHP_VERSION, '5.2.5', '>=' ) )
		$trace = debug_backtrace( false );
	else
		$trace = debug_backtrace();

	$caller = array();
	$check_class = ! is_null( $ignore_class );
	$skip_frames++; // skip this function

	foreach ( $trace as $call ) {
		if ( $skip_frames > 0 ) {
			$skip_frames--;
		} elseif ( isset( $call['class'] ) ) {
			if ( $check_class && $ignore_class == $call['class'] )
				continue; // Filter out calls

			$caller[] = "{$call['class']}{$call['type']}{$call['function']}";
		} else {
			if ( in_array( $call['function'], array( 'do_action', 'apply_filters' ) ) ) {
				$caller[] = "{$call['function']}('{$call['args'][0]}')";
			} elseif ( in_array( $call['function'], array( 'include', 'include_once', 'require', 'require_once' ) ) ) {
				$caller[] = $call['function'] . "('" . str_replace( array( content_path, abs_path ) , '', $call['args'][0] ) . "')";
			} else {
				$caller[] = $call['function'];
			}
		}
	}
	if ( $pretty )
		return join( ', ', array_reverse( $caller ) );
	else
		return $caller;
}


function mail_send($email, $subject, $message, $v = 2) {
		
	$smail   = get_option('admin_email');
	$email   = esc_sql( $email 	);
	$subject = esc_sql( $subject );
		
	if( file_exists( libs_path . 'class-mail.php' ) && !class_exists('Simple_Mail') && $v == 2 ){
		include libs_path . 'class-mail.php';
		
		$mailer = new Simple_Mail();
		$send	= $mailer->setTo($email, $email)
				 ->setSubject($subject)
				 ->setFrom($smail, $smail)
				 ->addMailHeader('Reply-To', $smail, 'Sender')
				 ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
				 ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
				 ->setMessage($message)
				 ->setWrap(300)
				 ->send();
		return ($send) ? true : false;
	}else{
		$headers = "MIME-Version: 1.0\n"
		."Content-Type: text/html; charset=utf-8\n"
		."Reply-To: \"$smail\" <$smail>\n"
		."From: \"$smail\" <$smail>\n"
		."Return-Path: <$smail>\n"
		."X-Priority: 1\n"
		."X-Mailer: Mailer\n";
		
		if( mail($email, $subject, $message, $headers) ) 
			return true;
	}
}
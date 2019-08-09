<?php 
/**
 * @fileName: class-login.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class User{
	protected $referal_login = '?signin';
	protected $referal_admin = '?admin';
	
	protected $cookie_name = cookie_name;
	protected $cookie_time;
	
	public $message_type;
	public $message_text;
	
	public function __construct(){
		$this->cookie_time = (3600 * 24 * 30); // 30 days
		
		//(!$this->check() )
			//$this->auto();
	}
	
	public function auto(){
		global $db;
		
		if( isset($this->cookie_name) ){
			// Check if the cookie exists
			if( isset($_COOKIE[$this->cookie_name]) ) {
				parse_str($_COOKIE[$this->cookie_name]);
				
				$username = esc_sql( $username );
				$password = esc_sql( $password );
			
				$rows = $db->get_results( "SELECT * FROM `$db->users` WHERE user_login = '$username' AND user_pass = '$password'" );
				
				// Make a verification
				if(($username == $rows->user_login) && ($password == $rows->user_pass)){
					// Register the session
					$_SESSION['username'] = $rows->user_login;
					$_SESSION['password'] = $rows->user_pass;
					$_SESSION['level'] = $rows->user_level;
				}
			}
		}
	}	
	/*
	 * sign in 
	 * @param $data array
	 * return true|false
	 */
	function sign_in( $data ){
		extract($data, EXTR_SKIP);
		
		$user_login = esc_sql( $username );
		$user_pass  = esc_sql( $password );
		$rememberme = esc_sql( $remember );
		
		if( $rememberme == 1 ) $rememberme = 'on'; 
		else $rememberme = 'off';
		
		$data = compact('user_login', 'user_pass','rememberme');		
		$this->_in($data);
	}
	/*
	 * sign up 
	 * @param $data array
	 * return true|false
	 */
	function sign_up( $data ){
		extract($data, EXTR_SKIP);
		
		$user_login 	= esc_sql( $username );		
		$user_email 	= esc_sql( $email );		
		$password 		= esc_sql( $password );	
		$repassword 	= esc_sql( $repassword );		
		$user_sex 		= esc_sql( $sex );		
		$user_country 	= esc_sql( $country );
		$chekterm 		= esc_sql( $chekterm );
		
		$userdata = compact('user_login', 'user_email','password','repassword','user_sex','user_country','chekterm');
		$this->_up($userdata);
	}	
	/*
	 * activation key 
	 * @param $codeaktivasi string
	 * return echo message
	 */
	function activation($codeaktivasi){
		$key = esc_sql( $codeaktivasi );
		
		 
		$this->message_type = 'error';
		$this->message_text = 'Gagal mengirim data ke email';
		if( $this->_activation($key) ){
			$this->message_type = 'message';
			$this->message_text = 'Data telah dikirim ke email anda';
		}
	}
	
	function change_password( $data ){
		global $db;
			
		extract($data, EXTR_SKIP);
		
		
		$user_login	= esc_sql( $this->exist_value('username') );
		$old_pass	= esc_sql( $old_pass );
		$new_pass	= esc_sql( $new_pass );
		$rep_pass	= esc_sql( $rep_pass );
		
		$oldpass	= md5($old_pass);
		$user_pass	= md5($rep_pass);
		
		if( empty($new_pass) || empty($rep_pass) ) $msg[] = 'New or RePassword is empty</a>';		
		if( $new_pass != $rep_pass ) $msg[] = 'Invalid New Password & RePassword not match</a>';
		
		$field = $db->get_results( "SELECT * FROM $db->users WHERE user_login='$user_login'" );
		if ($field->user_pass != $oldpass ) $msg[] = 'Invalid Old Password not match</a>';
		
		if( is_array($msg) ) 
		{
			foreach($msg as $val){
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}
		
		if(empty($msg)){
			$update = $this->_change_password(compact('user_pass'),compact('user_login'));
			if( $update ){
				$this->message_type = 'success';
				$this->message_text = 'The Password success to change';
			}
		}
	}	
	/*
	 * lost password
	 * @param $email string
	 * return echo message
	 */
	function lost_password($email){
		$user_email = esc_sql( $email );		
	
		if( empty( $user_email ) )
			$msg[] = 'The email field is empty.';
		else
		if( !valid_mail( $user_email ) ) 	
			$msg[] = 'The email not valid.';
			
		if( is_array($msg) )	{
			foreach($msg as $val){
				
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}else{
				 
			$this->message_type = 'error';
			$this->message_text = 'Gagal mengirim aktivasi ke email';
			if( $this->_lost_password( $user_email ) ){
			
				$this->message_type = 'message';
				$this->message_text = 'Link aktivasi telah dikirim ke email anda, silahkan melakukan aktivasi';
			}
			
		}
	}	
	/*
	 * update user data
	 * @param $usredata
	 * return filter data for update
	 */
	function update_user($userdata){
		extract($userdata, EXTR_SKIP);
		
		$user_id 		= esc_sql( $user_id );
		$user_login 	= esc_sql( $username );
		$user_email 	= esc_sql( $email );
		$user_sex		= esc_sql( $sex );
		$user_author	= esc_sql( $author );
		$thumb			= esc_sql( $thumb );
		$user_country	= esc_sql( $country );
		$user_province	= esc_sql( $province );
		$user_url		= esc_sql( $website );
		
		$userdata = compact('user_login', 'user_email', 'user_sex','user_author', 'user_id','thumb','user_country','user_province','user_url');
		$this->_update_user($userdata);
	}
	/*
	 * Deactivate account
	 * @param $email string
	 * return echo message
	 */
	function deactivate_account(){
		global $db;
		
		$user_login	= esc_sql( $this->exist_value('username') );
		$result = $db->update( 'users', array('user_status' => 0), array('user_status' => 1,'user_login' => $user_login) );
		
		
		return $result;			
	}
	/*
	 * insert user data
	 * @param $userdata array
	 * return echo message
	 */
	function _in($data){
		global $db;
		
		extract($data, EXTR_SKIP);	
			
		$msg = $get_data_user = null;
		
		if( empty( $user_login ) ) 	
			$msg = 'Kolom username kosong.';
		elseif( empty( $user_pass ) ) 	
			$msg = 'Kolom sandi kosong.';
		elseif( $user_pass = md5($user_pass) ){
			$get_data_user = $this->get_data_user( compact('user_login','user_pass') );
		}
		
		if( empty($msg) && $get_data_user == null ){
			$msg = 'Nama pengguna atau sandi tidak valid. Klik di sini jika <a href="?login=lost">Kehilangan kata sandi Anda?</a>';
		}
		
		if( !empty( $msg ) ){
			$this->message_type = 'error';
			$this->message_text = $msg;
			
		}
		
		if( empty($msg) && $get_data_user ){
			
			
			$data_compile = array_merge_simple( $get_data_user, array('rememberme' => $rememberme) );
			$save_log = $this->_log( $data_compile );
			if( $save_log ): 
			
		
				$redirect = true;
			
				$redirect_url = $this->referal_login;
				if( $get_data_user->user_level == 'admin' )
					$redirect_url = $this->referal_admin;
					
				
			endif;
		}
		
		if( $redirect ){
			$this->message_type = 'success';
			$this->message_text = 'Redirect...';
			
			redirect( $redirect_url );	
		}
		
	}		
	/*
	 * update data user
	 * @param $userdata array
	 * return echo message
	 */
	function _up($userdata){
		global $db;
		
		extract($userdata, EXTR_SKIP);				
		$msg = array();
		
		if( empty( $user_login ) ){
			$msg[] = 'The username field is empty.';
		}else{
			$field = count($db->get_col( "SELECT * FROM $db->users WHERE user_login='$user_login'" ) );
			if($field > 0) $msg[] = 'Username "'.$user_login.'" sudah terpakai, silahkan ganti yg lain</a>';
		}
		
		
		if( empty( $user_email ) ) $msg[] = 'Kolom email kosong.';
		else{
			
			if( !valid_mail( $user_email ) ){	
				$msg[] = 'Email tidak valid.';
			}else{
				$field = count( $db->get_col( "SELECT * FROM $db->users WHERE user_email='$user_email'" ) );
				if( $field > 0) $msg[] = 'Email ini sudah pernah melakukkan registrasi</a>';
			}
		
		}
		
		if( empty( $password ) ){
			$msg[] = 'The password field is empty.';
		}elseif( $password != $repassword ){
			$msg[] = 'The password not match.';			
		}
				
		if( empty( $user_sex ) ) $msg[] = 'Kolom jenis kelamin belum dipilih.';		
		if( empty( $chekterm ) ) $msg[] = 'Peraturan belum dicentang.';
		
		if( $msg != null && is_array($msg))	{
			foreach($msg as $val){
				
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}else{			
			$user_level 			= 'user'; // default
			$user_activation_key 	= random_password(20, false);
			$user_registered 		= date('Y-m-d H:i:s');
			$user_last_update 		= $user_registered;
			$user_pass 				= md5($repassword);
			$user_author 			= $user_login;
			$user_status			= 0;
			
			$data 		= compact('user_login','user_author','user_email','user_pass','user_sex','user_registered','user_last_update','user_status','user_country','user_activation_key');
			
			if( $db->insert( 'users', $data) ){
				
				$user_data = compact('user_email','user_activation_key');
				
				if( $this->message_activation($user_data) ){
					
					$this->message_type = 'success';
					$this->message_text = 'Anda berhasil menambahkan akun, cek email kamu untuk verifikasi';
				}
			}
		}
	}
	/*
	 * change password
	 * @param $data array
	 * @param $where array
	 * return true|false
	 */
	function _change_password( $data, $where ){	
		global $db;	
		
		$data = array_merge_simple( $data, array('user_last_update' => date('Y-m-d H:i:s') ) );
		$result = $db->update( 'users', $data, $where );
		
		return $result;
	}
	/*
	 * filtering activation
	 * @param $key string
	 * return echo message and log in
	 */
	function _activation($key){
		global $db;
		
		$msg = array();
		
		if( empty( $key ) ) 	
			$msg[] = 'The code activation field is empty.';
			
		$field = count($db->get_col( "SELECT * FROM $db->users WHERE 'user_activation_key' = $key" ));
		
		if( $field < 1 ){ 	
			$msg[] = 'The code activation not valid.';
		}else{
			if( empty($msg) ):
			$new_pass			= random_password();
			$user_pass			= has_password($new_pass);
			$user_last_update 	= date('Y-m-d H:i:s');
			$user_status	 	= 1;
			
			$data = compact('user_pass','user_last_update','user_status');			
			$userupdate = $db->update( 'users', $data + array('user_activation_key' => ''), array('user_activation_key' => $key) );
			if( $userupdate ):
				
				$user 			= $field->user_login;
				$email 			= $field->user_email;
				$sex   			= $field->user_sex;
				$user_country   = $field->user_country;
				$user_province  = $field->user_province;
				
				if( $sex == 'l' ) $user_sex = 'Perempuan';
				elseif($sex == 'p') $user_sex = 'Laki - laki';
				else $user_sex = 'Unknow';
				
				if( empty($user_province) ) $user_province = 'Unknow (please change)';
				
				$user_data = compact('login','email','user_sex','new_pass','user_country','user_province');
				
				if( $this->message_reg($user_data) )
					$this->sign_in( array('username' => $user, 'password' => $new_pass, 'remember' => 'off') );
				
			endif;
			endif;
		}
		if( is_array($msg))	{
			foreach($msg as $val){
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}
	}
	/*
	 * user filter dan update data
	 * @param $userdata array
	 * return echo message and redirect url
	 */
	function _update_user( $userdata ){
		global $db;
		extract($userdata, EXTR_SKIP);
		
		$ID = (int) $user_id;
		$user_last_update = date('Y-m-d H:i:s');
		
		$msg = array();
		if( empty($user_author) ) $msg[] = 'Kolom nama kosong';
		if( empty( $user_email ) ) $msg[] = 'Kolom email kosong.';
		elseif( !valid_mail( $user_email ) ) $msg[] = 'Email tidak valid.';		
		
		if( empty($user_sex) )  $msg[] = 'Kolom jenis kelamin belum dipilih';
		if( empty($user_country) ) $msg[] = 'Kolom negara belum dipilih';
		
		if( is_array($msg) ){
			foreach($msg as $val){
				
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}
		
		$field = $db->get_results( "SELECT * FROM $db->users WHERE user_ID='$ID'" );
		
		if(!empty($thumb['name'])):
			$thumb	= hash_image( $thumb );
			$user_avatar = esc_sql($thumb['name']);
			//thumb extract		
			$thumb[name] = 'avatar_' . $thumb[name];
			$thumb[type] = $thumb[type];
			$thumb[tmp_name] = $thumb[tmp_name];
			$thumb[error] = $thumb[error];
			$thumb[size] = $thumb[size];
			
			upload_img_post($thumb,'',650,120);
			
			delete_img_post('avatar_'.$field->user_avatar);
		else:
			$user_avatar = esc_sql($field->user_avatar);
		endif;
		
		$data = compact('user_login','user_email','user_author','user_sex','user_last_update','user_country','user_province','user_url','user_avatar');
			
		if( $msg == null && $db->update( 'users', $data, compact( 'ID' ) ) ):
		
			$this->message_type = 'success';
			$this->message_text = 'Berhasil memperbaharui akun';
			
			redirect( '?' . $_SERVER['QUERY_STRING'] );
		endif;
	}
	
	/*
	 * filtering data lost password
	 * @param $user_mail string
	 * return echo message
	 */
	function _lost_password($user_email){
		global $db;
			
		$field = count( $db->get_col( "SELECT * FROM $db->users WHERE user_email='$user_email'" ) );
		
		if( $field < 1 ):	
			$msg[] = 'The email not registration.';
		else:
			if(empty($msg)):
			$user_activation_key 	= random_password(20, false);
			$user_last_update 		= date('Y-m-d H:i:s');
			
			$data = compact('user_last_update','user_activation_key');			
			$userupdate = $db->update( 'users', $data, compact('user_email') );
			if( $userupdate ):
				
				$user_data = compact('user_email','user_activation_key');
				$this->message_activation($user_data);
				
			endif;
			endif;
		endif;
		
		if( is_array($msg))	{
			foreach($msg as $val){
				
				$this->message_type = 'error';
				$this->message_text = $val;
			}
		}
	}
	/*
	 * get data user
	 * @param $param_data array
	 * return array
	 */
	function get_data_user($param_data){
		global $db;
		
		extract($param_data, EXTR_SKIP);
		
		if( valid_mail($user_login) && $user_email = $user_login ){
			$where 	= compact('user_email','user_pass');
				
		}
		else
		{			
			$where 	= compact('user_login','user_pass');
		}
		$data_merge = array_merge_simple( $where, array('user_status'=>1) );
		
		
		$wheres = array();
		$field_types = array();
		
		foreach ( (array) $data_merge as $field => $value) {
			if ( isset( $field_types[$field] ) )
				$form = $field_types[$field];
			else
				$form = $value;
				
			$wheres[] = $field."='".esc_sql( $form )."'";
		}
		
		$rows = $db->get_results( "SELECT * FROM `$db->users` WHERE " . implode( ' AND ', $wheres ) );
		
		return (object) array( 
			'user_login' 	=> $rows[0]->user_login, 
			'user_level' 	=> $rows[0]->user_level, 
			'user_pass' 	=> $rows[0]->user_pass
		);
			
	}
	
	/*
	 * chekking log $_SESSION or $_COOKIE 
	 * @param $param string
	 * return true|false
	 */
	function exist( $param ){
		//memanggil session
		
		if( isset( $_SESSION[$param] ) )
			return esc_sql( $_SESSION[$param] );	
		
		return false;
	}	
	/*
	 * chekking value log $_SESSION atau $_COOKIE
	 * @param $param string
	 * return true|false
	 */
	function exist_value( $param ){
		//memanggil session
		
		if( isset( $_SESSION[$param] ) )
			return esc_sql( $_SESSION[$param] );
		
		return false;
	}
	/*
	 * function save_log()
	 * untuk menyimpan data user kedalam log baik cookie, session atau database
	 * using: $this->save_log($data)
	 */
	function _log( $data ){				
		extract($data, EXTR_SKIP);
		/*
		 * $session->set($param,$value)
		 * mulai mengeset session
		 */
		$_SESSION['username'] = esc_sql( $user_login );
		$_SESSION['password'] = esc_sql( $user_pass );
		$_SESSION['level'] = esc_sql( $user_level );
		
		if( $rememberme == 'on' ):
			
			setcookie (
				$this->cookie_name, 
				'username='.esc_sql( $user_login ).
				'&password='.esc_sql( $user_pass ).
				'&level='.esc_sql( $user_level ),
				time() + $this->cookie_time
			);	
			
		endif;			
		
		//memperbaharui data log pd database
		$this->_log_update($data);
		return true;
	}
	/*
	 * save log update data
	 * @param $data array
	 * return redirect url
	 */
	function _log_update($data){
		global $db;
		
		extract($data, EXTR_SKIP);
		$user_last_update = date('Y-m-d H:i:s');
			
		$db->update( 'users', compact( 'user_last_update' ), compact( 'user_login','user_level' ) );
	}
	/*
	 * log out
	 * 
	 * return string
	 */
	function login_out( $return = false ){	
		
		
		if( $return ){
			if( isset( $_SESSION['username'] ) )
				return $this->_clear_log();
			
		}else{
			
			if( isset( $_SESSION['username'] ) ){
				if( $this->_clear_log() ){
					
					$this->message_type = 'success';
					$this->message_text = 'Anda telah keluar dari website';
				}
			}
			
			redirect( $this->referal_login );
		}
	}	
	function _clear_log(){
		unset( $_SESSION['username'] );
		unset( $_SESSION["password"] );
		unset( $_SESSION["level"] );
		unset( $_SESSION["lw"] );
			
		delete_directory( abs_path . 'cache' );
			
		if(isset($_COOKIE[$this->cookie_name])){
			// remove 'site_auth' cookie
			setcookie ($this->cookie_name, '', time() - $this->cookie_time);
		}
	}
	/*
	 * chekking username
	 * return true|false
	 */
	function check(){	
		global $db;
		
		$user_login = $this->exist( "username" );		
		$query = "SELECT * FROM $db->users WHERE user_login = '$user_login'";
		$total_columb = count( $db->get_col($query) );		
		$row = $db->get_results( $query );	
		
		if( $total_columb > 0 && $row->user_status > 0 )
			return true;
			
		return false;
		//else 
			//$this->_clear_log();
	}
	/*
	 * chekking lever user
	 * @param $param string
	 * return true|false
	 */
	function level( $param ){
		if( $param == $this->exist_value('level') )
			return true;
	}
	/*
	 * send message activation
	 * @param $data array
	 * return echo message
	 */
	function message_activation($data){	
		extract($data, EXTR_SKIP);	
				
		$head  = 'Activation Registration<br><br>';
		$send .= '<strong>'.$head.'</strong><br>';
		$send .= 'Seseorang telah mendaftarkan akun email anda di <a href="'.site_url().'">'.site_url().'</a><br><br>';
		$send .= sprintf('Your Email: %s', $user_email) . "<br>";
		$send .= sprintf('Activation Code: %s', $user_activation_key) . "<br><br>";
		$send .= 'Masukkan code diatas melalui tautan ini : <a target="_blank" href="'.site_url('index.php?login=activation').'">'.site_url('index.php?login=activation').'"</a><br><br>';
		$send .= 'Atau<br><br>';
		$send .= 'Tautan aktivasi ini : <a target="_blank" href="'.site_url('index.php?login=activation&keys='.$user_activation_key).'">'.site_url('index.php?login=activation&keys='.$user_activation_key).'"</a><br><br>';
		$send .= 'Ini adalah email otomatis, diharapkan tidak membalas email ini<br>';
		
		
		$this->message_type = 'error';
		$this->message_text = 'Gagal mengirim aktivasi ke email';
		if( mail_send($user_email, $head, $send) ){
			$this->message_type = 'message';
			$this->message_text = 'Link aktivasi telah dikirim ke email anda, silahkan melakukan aktivasi';
		}
			
		return true;
	}	
	/*
	 * send message registration member
	 * @param $data array
	 * return echo message
	 */
	function message_reg($data){	
		global $class_country;
		
		extract($data, EXTR_SKIP);	
		
		$head  = 'Login Data Registration<br><br>';
		$send .= '<strong>'.$head.'</strong><br>';
		$send .= 'Akun anda sudah diaktifkan berikut data datanya<br><br>';
		$send .= sprintf('Email: %s', $email) . "<br>";
		$send .= sprintf('User Name: %s', $user) . "<br>";
		$send .= sprintf('Password: %s', $new_pass) . "<br>";
		$send .= sprintf('Jenis Kelamin: %s', $user_sex) . "<br>";
		
		$user_country = $class_country->country_name($user_country);
		$send .= sprintf('Negara: %s', $user_country ) . "<br>";
		$send .= sprintf('Provinsi: %s', $user_province ) . "<br><br>";
		$send .= 'Silahkan kunjungi website : <a target="_blank" href="'.site_url('/?login=profile').'">'.site_url('/?login=profile').'"</a><br>dan ubah profile kamu<br><br>' ;
		$send .= 'Ini adalah email otomatis, diharapkan tidak membalas email ini<br>';
		
		$this->message_type = 'error';
		$this->message_text = 'Gagal mengirim data akun ke email.';
		if( mail_send($user_email, $head, $send) ){
			$this->message_type = 'message';
			$this->message_text = 'Akun dan Sandi telah dikirim ke email anda, silahkan login';
		}
		
		return true;
	}
}



function get_userdata( $user_ID ){
	global $db;
	
	$where = "WHERE user_ID='$user_ID'";
	$users = $db->get_row( "SELECT * FROM $db->users $where" );
	
	return $users;
}

function get_the_author_meta( $field = '', $user_id = false ) {
	global $query;
	
	if ( ! $user_id && $query->get('author') ) $user_id = (int) $query->get('author');
	
	if ( ! $user_id ) {
		global $authordata;
		$user_id = isset( $authordata->user_ID ) ? $authordata->user_ID : 0;
	} else {
		$authordata = get_userdata( $user_id );
	}

	if ( in_array( $field, array( 'ID', 'login', 'author', 'pass', 'email', 'sex', 'registered', 'activation_key', 'level', 'province', 'avatar', 'status' ) ) )
		$field = 'user_' . $field;

	$value = isset( $authordata->$field ) ? $authordata->$field : '';

	return apply_filters( 'get_the_author_' . $field, $value, $user_id );
}
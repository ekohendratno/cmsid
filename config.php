<?php
/**
 * @fileName: config.php
 * @setting ketentuan website
 * 
 * isi sesuai dengan hak akses ke mysql server anda
 */
 
//not direct access
if(!defined('_iEXEC')) exit;


/*
 *************** Basic Setting *************
 */

//nama host mysql db
define('DB_HOST', 'localhost');
//nama pengguna mysql db
define('DB_USER', 'root');
//kata sandi mysql db	
define('DB_PASS', '');
//nama database mysql	
define('DB_NAME', 'cmsid_2016');
//nama awal table
define('DB_PRE', 'cmsid_');

/*
 * untuk setting alamat url website manual jika anda pindah host/alamat domain
 * silahkan atur di database anda pd table _options cari siteurl di field option_name
 */
?>
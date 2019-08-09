<?php

function theme_widgets_init() {
	/*** 
	* function: theme_widgets_init()
	*
	* Dapat digunakan untuk menginisialisasi widget dari themes
	*/
	//echo "function: theme_widgets_init()<br>";
	//echo "exec: add_action('widgets_init','theme_widgets_init');<br>";
	if (function_exists('register_sidebar')){
		
	  register_sidebar(array(
		  'name'			=> 'Home/Page Left',
		  'before_widget'	=> '<div class="well">',
		  'after_widget'	=> '</ul></div></div></div>',
		  'before_title'	=> '<h4>',
		  'after_title'		=> '</h4><div class="row"><div class="col-lg-6"><ul class="list-unstyled">',
	  ));		
	  
	  register_sidebar(array(
		  'name'			=> 'Footer',
		  'before_widget'	=> '<div class="footerwidget left">',
		  'after_widget'	=> '</div>',
		  'before_title'	=> '<h3>',
		  'after_title'	=> '</h3>',
	  ));	
  	}
}
add_action( 'widgets_init', 'theme_widgets_init' );

function to_monthly( $monthly ){
	$bln_array = array (
		'1' =>'01',
		'2' =>'02',
		'3' =>'03',
		'4' =>'04',
		'5' =>'05',
		'6' =>'06',
		'7' =>'07',
		'8' =>'08',
		'9' =>'09',
		'10'=>'10',
		'11'=>'11',
		'12'=>'12');
	$bln = @$bln_array[$monthly];
	return $bln;
}	

function datetimes_first( $tgl, $jam = true, $display = true ){
	$value = date_times( $tgl, $jam );
	$time = "$value[hari], $value[tggl] $value[bln] $value[thn] $value[jam]";
	
	if ( $display )
		echo $time;
	else
		return $time;
}

function startbootstrap_date( $datetime, $display = true ){
	
	return default_date($datetime,'hari=1&tanggal=1&bulan=1;tahun=1', $display);
}
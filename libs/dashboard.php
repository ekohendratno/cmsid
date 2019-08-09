<?php 
/**
 * @fileName: dashboard.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

function dashboard() {
	global $screen_layout_columns;
	/*

	$hide2 = $hide3 = $hide4 = '';
	switch ( $screen_layout_columns ) {
		case 4:
			$width = 'width:25%;';
			break;
		case 3:
			$width = 'width:33.333333%;';
			$hide4 = 'display:none;';
			break;
		case 2:
			$width = 'width:50%;';
			$hide3 = $hide4 = 'display:none;';
			break;
		default:
			$width = 'width:100%;';
			$hide2 = $hide3 = $hide4 = 'display:none;';
	}*/
	do_action('the_notif');
	
	echo '<div id="dashboard-widgets" class="metabox-holder row">';
	echo "\t<div class='column column0' id='column0'>\n";
	do_meta_boxes( 'normal', '' );

	echo "\t</div><div class='column column1' id='column1'>\n";
	do_meta_boxes( 'side', '' );
	
	echo '</div>';
	echo '<div style="clear:both;"></div>';
	echo '</div>';
}

function the_notif(){
	global $db;
	
	if( get_query_var('x') == 'splash' ){
		if( checked_option( 'splash' ) ) set_option( 'splash', 1 );
		else add_option( 'splash', 1 );
		
		redirect('?admin');
	}
?>

			
				<!--ROW COLUMN FIRST END-->
				<!--QUICK PANEL END-->
            	<div class="row">	
				
                	<div class="col-md-12">
                    	<div class="panel panel-default">
						
  <div class="panel-body panel-quick">

	<div class="col-md-3 panel-quick-start">
        <div class="cols">
        	<strong>Memulai</strong>
            <div class="clearfix"></div>
            <a href="?admin=single&amp;sys=appearance&amp;go=theme-editor&amp;theme=portal" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Custom situs</a>
            <div class="clearfix clr2"></div>or <a href="?admin&amp;sys=appearance">ubah tampilan</a>
        </div>
	</div>
	<div class="col-md-2 panel-quick-second">
        <div class="cols">
        	<strong>Versions</strong>
            <div class="clearfix"></div>
			<span>Current Version 3.00 build 100</span>
            <a title="" data-original-title="Information Pembaruan" href="#" data-url="?request&amp;apps=yes&amp;load=libs/ajax/latest.php" data-type="show" class="btn btn-info btn-xs modal-show"><span class="glyphicon glyphicon-refresh"></span> Check Updates</a>
            <div class="clearfix"></div>
        </div>
	</div>
	<div class="col-md-3 panel-quick-three">
        <div class="cols">
        	<strong>Langkah berikutnya</strong>
            <div class="clearfix"></div>
            <ul>
            <li><a href="?admin=single&amp;apps=post&amp;go=add&amp;type=post">Tulis sebuah posting</a></li>
            <li><a href="?admin=single&amp;apps=post&amp;go=add&amp;type=page">Tulis sebuah halaman</a></li>
            <li><a href="http://localhost/cmsid/build/1.4.sample">Lihat situs</a></li>
            </ul>
        </div>
	</div>
	<div class="col-md-4 panel-quick-four">
        <div class="cols">
        	<strong>Lainnya</strong>
            <div class="clearfix"></div>
            <ul>
            <li>Atur <a href="?admin&amp;sys=options">option</a> atau <a href="?admin&amp;sys=appearance&amp;go=widgets">widget</a> atau <a href="?admin&amp;sys=appearance&amp;go=menus">menu</a></li>
            <li><a id="popup" data-type="edit" href="?request&amp;apps=yes&amp;load=post/setting.php" title="Pengaturan Post">Mati atau hidupkan komentar</a></li>
            <li><a href="http://cmsid.org/page-langkah-pertama-menggunakan-cmsid.html">Pelajari lebih lanjut untuk memulai</a></li>
            </ul>
    	</div>
	</div>
    
                        
                        </div>
                        </div>
                        
                        
                    </div>
					
                </div>
				<!--QUICK PANEL END-->
				<!--ROW COLUMN FIRST END-->
<?php
}
add_action('the_notif','the_notif');

function dashboard_feed_news(){ 
?>
<div class="panel-content" data-url="/?request&apps=yes&load=libs/ajax/feed.php"></div>
<?php
}

function dashboard_quick_post(){ 

if(isset($_POST['post_publish']) || isset($_POST['post_draf'])){
	
	$title 		= filter_txt($_POST['title']);
	$category 	= filter_int($_POST['category']);
	
	if(get_option('text_editor')=='classic') $isi = nl2br2($_POST['isi']);
	else $isi = $_POST['isi'];
	
	$tags 		= filter_txt($_POST['tags']);
	$date 		= date('Y-m-d H:i:s');
	
	if(isset($_POST['post_draf'])) $status = 0;
	else $status = 1;
	
	$type 		= 'post';
	$approved	= 1;
	
	$data = compact('title','category','type','isi','tags','date','status');
	add_quick_post($data);
}
?>
<form class="form-horizontal">
							<input class="form-control" id="focusedInput" value="Judul artikel..." type="text">
							<textarea class="form-control" rows="3" id="textArea"></textarea>
							<div class="row form-group">
								<div class="col-xs-8">
								
									 <select class="form-control" id="select">
									  <option>Kategori</option>
									</select>
								
								</div>
								<div class="col-xs-4">
									<input class="form-control" id="inputEmail" placeholder="Tags etc,dll" type="text">
								</div>
							</div>
							<div class="row form-group">
								<div class="col-xs-8">
									<button type="submit" class="btn btn-info">Save a Draf</button>
									<button type="reset" class="btn btn-default">Cancel</button>
								</div>
								<div class="col-xs-4">
									<button type="submit" class="btn btn-primary pull-right">Publish</button>
								</div>
							</div>
						</form>						
<?php
}

function dashboard_recent_registration(){ 
?>                  
                        <ul class="nav nav-tabs">
                          <li class="active"><a href="#rr-new" data-toggle="tab">Terbaru</a></li>
                          <li><a href="#rr-country" data-toggle="tab">Negara</a></li>
                        </ul>
                        <div id="recentRegistration" class="tab-content">
                          <div class="tab-pane fade active in" id="rr-new">
                            
							<div class="list-group">
							<a data-original-title="Profile Akun" title="" href="#" data-url="?request&apps=yes&load=libs/ajax/user.php" data-type="show" class="list-group-item modal-show">
								<div class="row">
									<div class="col-xs-1">
										<img src="libs/img/cinqueterre.jpg" class="img-circle" alt="Cinque Terre" height="23" width="23">
									</div>
									<div class="col-xs-11">
										<div class="pull-left">
										<span class="label label-info"><i class="glyphicon glyphicon-briefcase"></i> Mr.Eko</span>
										</div>												
										<div class="pull-right">
										<span class="label label-primary">Male</span><span class="label label-default">2015/05/19</span><span class="label label-success"><i class="glyphicon glyphicon-ok"></i></span>
										</div>
									</div>	
								</div>
							</a>
							<a class="list-group-item modal-show" title="Profile Akun" href="#" data-url="/ajax/user.php" data-type="show" >
								<div class="row">
									<div class="col-xs-1">
										<img src="libs/img/cinqueterre.jpg" class="img-circle" alt="Cinque Terre" height="23" width="23">
									</div>
									<div class="col-xs-11">
										<div class="pull-left">
										<span class="label label-success"><i class="glyphicon glyphicon-user"></i> Joko Susilo</span>
										</div>													
										<div class="pull-right">
										<span class="label label-primary">Male</span><span class="label label-default">2015/05/19</span><span class="label label-success"><i class="glyphicon glyphicon-ok"></i></span>
										</div>
									</div>	
								</div>
							</a>
							<a class="list-group-item modal-show" title="Profile Akun" href="#" data-url="/ajax/user.php" data-type="show" >
								<div class="row">
									<div class="col-xs-1">
										<img src="libs/img/cinqueterre.jpg" class="img-circle" alt="Cinque Terre" height="23" width="23">
									</div>
									<div class="col-xs-11">
										<div class="pull-left">
										<span class="label label-success"><i class="glyphicon glyphicon-user"></i> Sisilia Parter</span>
										</div>													
										<div class="pull-right">
										<span class="label label-primary">Female</span><span class="label label-default">2015/05/19</span><span class="label label-success"><i class="glyphicon glyphicon-ok"></i></span>
										</div>
									</div>	
								</div>
							</a>
							</div>							
							
                          </div>
                          <div class="tab-pane fade" id="rr-country">
                          b
                          </div>
                         </div>
						 					 
<?php

}

function dashboard_init() {
do_action('dashboard_init');
	
?>
<script type="text/javascript">

	var base_url = '<?php echo site_url();?>';	
	
	
	function updateWidgetData(){
		var sortorder = new Array();
		$('#dashboard-widgets').each(function(){
			var dwa = $(this);	
			$('.column .meta-box-sortables').each(function(i){		
				var sortorder_by = $(this).attr('id').replace(/-sortables/i,'');
				$('.dragbox', this).each(function(i){
					
					if( 'normal' == sortorder_by )
						sortorder.push( {normal:$(this).attr('id')} );				
					else if( 'side' == sortorder_by )
						sortorder.push( {side:$(this).attr('id')} );
					
				});
			});	
		});
		
		var normal_array = new Array();
		var side_array = new Array();
		for(i=0; i < sortorder.length; i++){
			if( sortorder[i].normal ) normal_array.push( sortorder[i].normal );
			else if( sortorder[i].side ) side_array.push( sortorder[i].side );
		}
		
		var normal_string = '';
		var side_string = '';
		for(i=0; i < normal_array.length; i++){
			normal_string+= normal_array[i]+',';
		}
		
		for(i=0; i < side_array.length; i++){
			side_string+= side_array[i]+',';
		}
		
		var set_sortorder = {normal:normal_string,side:side_string};
		//console.log(set_sortorder);
				
		//Pass sortorder variable to server using ajax to save state
		//$.post('irequest.php?auto', 'sort='+$.toJSON(sortorder));
		//autosave(sortorder);
		$.post( base_url +'/?request=dashboard', 'data='+$.toJSON( set_sortorder ), function(response){});
			/*	   
			var winHeight = $(window).height();
			var winWidth = $(window).width();
			$('#redactor_modal_console').css({
				top: '15%',
				left: winWidth / 2 - $('#redactor_modal_console').width() / 2
			});
			$('#redactor_modal_overlay_loading,#redactor_modal_console').show().fadeOut('slow');*/
		
	}

	function show_empty_container(){
		$(".column .meta-box-sortables").each(function(index, element) {
			var t = $(this);
			if ( !t.children('.panel:visible').length )
				t.addClass('empty-container');
			else
				t.removeClass('empty-container');
		});
	}
	
</script>
<?php
}

function add_meta_box( $id, $title, $callback, $context = 'advanced', $priority = 'default', $setting = null ) {
	global $meta_boxes;
	//call do_meta_boxes in screen.php

	if ( !isset($meta_boxes) )
		$meta_boxes = array();
	if ( !isset($meta_boxes[$context]) )
		$meta_boxes[$context] = array();

	foreach ( array_keys($meta_boxes) as $a_context ) {
		foreach ( array('high', 'core', 'default', 'low') as $a_priority ) {
			if ( !isset($meta_boxes[$a_context][$a_priority][$id]) )
				continue;

			if ( 'core' == $priority ) {
				if ( false === $meta_boxes[$a_context][$a_priority][$id] )
					return;
				if ( 'default' == $a_priority ) {
					$meta_boxes[$a_context]['core'][$id] = $meta_boxes[$a_context]['default'][$id];
					unset($meta_boxes[$a_context]['default'][$id]);
				}
				return;
			}
			
			if ( empty($priority) ) {
				$priority = $a_priority;
			} elseif ( 'sorted' == $priority ) {
				$title = $meta_boxes[$a_context][$a_priority][$id]['title'];
				$callback = $meta_boxes[$a_context][$a_priority][$id]['callback'];
				$setting = $meta_boxes[$a_context][$a_priority][$id]['setting'];
			}
			
			if ( $priority != $a_priority || $context != $a_context )
				unset($meta_boxes[$a_context][$a_priority][$id]);
		}
	}

	if ( empty($priority) )
		$priority = 'low';

	if ( !isset($meta_boxes[$context][$priority]) )
		$meta_boxes[$context][$priority] = array();

	$meta_boxes[$context][$priority][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'setting' => $setting);
}



function add_dashboard_widget( $widget_id, $widget_name, $callback, $setting = null ) {

	$side_widgets = array('dashboard_quick_post', 'dashboard_recent_registration');

	$location = 'normal';
	if ( in_array($widget_id, $side_widgets) )
		$location = 'side';

	add_meta_box( $widget_id, $widget_name, $callback, $location, $priority, $setting );
}

function dashboard_setup() {
	global $current_screen;
	$current_screen->render_screen_meta();
	
	add_dashboard_widget( 'dashboard_quick_post', 'Quick Post', 'dashboard_quick_post' );
	add_dashboard_widget( 'dashboard_recent_registration', 'Recent Registration', 'dashboard_recent_registration', array('data-url'=>'?request&load=libs/ajax/recent.php&aksi=edit', 'data-type'=>'edit') );
	add_dashboard_widget( 'dashboard_feed_news', 'Feed News', 'dashboard_feed_news', array('data-url'=>'?request&load=libs/ajax/feed.php&aksi=edit', 'data-type'=>'edit') );
}
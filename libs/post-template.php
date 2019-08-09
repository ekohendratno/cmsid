<?php 
/**
 * @fileName: post-template.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

/**
 * Display or retrieve the HTML list of categories.
 *
 * @param string|array $args Optional. Override default arguments.
 * @return string HTML content only if 'echo' argument is 0.
 */
function list_categories( $args = '' ) {
	$defaults = array(
		'show_option_none' => 'No categories',
		'orderby' => 'category_ID', 
		'order' => 'ASC',
		'style' => 'list',
		'title_li' => 'Categories',
		'echo' => 1,
		'taxonomy' => 'category', 
		'no_categories' => 5 
	);
	$r = parse_args( $args, $defaults );
	
	if ( !isset( $r['class'] ) )
		$r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];
		
	extract( $r );
	
	$output = '';
	if ( $title_li && 'list' == $style )
			$output = '<li class="' . esc_attr( $class ) . '">' . $title_li . '<ul>';
		
	$order = "ORDER BY $orderby $order  LIMIT $no_categories";

	global $db;
	
	
	$query_request = "SELECT * FROM $db->category $order";
	$categories = $db->get_results( $query_request );
	$categories_count = $db->get_col( $query_request );	
	
	if ( empty( $categories_count ) ) {
		$output .= $show_option_none;
	} else {
		foreach ( (array) $categories as $categori ) {
			$permalinks = "?cat=" . $categori->category_ID;
			if ( 'list' == $style )
				$output .= "<li><a href='$permalinks'>$categori->category_name</a></li>";
			else
				$output .= "<a href='$permalinks'>$categori->category_name</a>";
		}
	}
	if ( $title_li && 'list' == $style )
		$output .= '</ul></li>';

	$output = apply_filters( 'list_categories', $output, $args );

	if ( $echo )
		echo $output;
	else
		return $output;
}
/**
 * Retrieve or display list of pages in list (li) format.
 *
 * @since 1.5.0
 *
 * @param array|string $args Optional. Override default arguments.
 * @return string HTML content, if not displaying.
 */
function list_pages($args = '') {
	
	$defaults = array(
		'title_li' => 'Pages', 
		'echo' => 0,
		'sort_column' => 'menu_order, post_title',
		'link_before' => '', 
		'link_after' => ''
	);

	$r = parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$output = '';
	$current_page = 0;
	
	global $db;
	
	$sql_query = "SELECT * FROM $db->posts WHERE post_type='page' AND post_status=1";
	$total_pages = count( $db->get_col( $sql_query ) );

	if ( $total_pages >  0 ) {
		if ( $r['title_li'] )
			$output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
		
		$pages = $db->get_results( $sql_query );
		foreach ( (array) $pages as $page ) :
		$output .= '<li class="page_item page-item-2"><a href="?page_id='.$page->post_ID.'" title="About">'.$page->post_title.'</a></li>';	
		endforeach;

		if ( $r['title_li'] )
			$output .= '</ul></li>';
	}

	$output = apply_filters('list_pages', $output, $r);
	
	if ( $r['echo'] )
		echo $output;
	else
		return $output;
}
/**
 * Display archive links based on type and format.
 *
 * @param string|array $args Optional. Override defaults.
 * @return string|null String when retrieving, null when displaying.
 */
function get_archives($args = '') {
	global $db;

	$defaults = array(
		'type' => 'monthly', 'limit' => '',
		'format' => 'html', 
		'before' => '',
		'after' => '', 
		'show_post_count' => false,
		'echo' => 1, 
		'order' => 'DESC',
	);

	$r = parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	if ( '' == $type )
		$type = 'monthly';

	if ( '' != $limit ) {
		$limit = (int) $limit;
		$limit = ' LIMIT '.$limit;
	}

	$order = strtoupper( $order );
	if ( $order !== 'ASC' )
		$order = 'DESC';

	$archive_week_separator = '&#8211;';

	$archive_date_format_over_ride = 0;

	$archive_day_date_format = 'Y/m/d';

	$archive_week_start_date_format = 'Y/m/d';
	$archive_week_end_date_format	= 'Y/m/d';

	if ( !$archive_date_format_over_ride ) {
		$archive_day_date_format = get_option('date_format');
		$archive_week_start_date_format = get_option('date_format');
		$archive_week_end_date_format = get_option('date_format');
	}

	//filters
	$where = apply_filters( 'getarchives_where', "WHERE post_type = 'post' AND post_status = '1'", $r );
	$join = apply_filters( 'getarchives_join', '', $r );

	$output = '';
	if ( 'monthly' == $type ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(post_ID) as posts , post_date FROM $db->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date $order $limit";
		
		$afterafter = $after;
		
		$arcresults = $db->get_results($query);
		foreach ( (array) $arcresults as $arcresult ) {
			$url = '?m=' . $arcresult->year.'-'.to_monthly( $arcresult->month );
			/* translators: 1: month name, 2: 4-digit year */
			$text = default_date($arcresult->post_date,false,false,false);
			if ( $show_post_count )
				$after = '&nbsp;('.$arcresult->posts.')' . $afterafter;
				
			$output.= get_archives_link($url, $text, $format, $before, $after);
		}
	} elseif ( 'yearly' == $type ) {
		$query = "SELECT YEAR(post_date) AS `year`, count(post_ID) as posts FROM $db->posts $join $where GROUP BY YEAR(post_date) ORDER BY post_date $order $limit";
		//desc your function output
	} elseif ( 'daily' == $type ) {
		$query = "SELECT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, DAYOFMONTH(post_date) AS `dayofmonth`, count(post_ID) as posts FROM $db->posts $join $where GROUP BY YEAR(post_date), MONTH(post_date), DAYOFMONTH(post_date) ORDER BY post_date $order $limit";
		//desc your function output
	} elseif ( 'weekly' == $type ) {
		$query = "SELECT DISTINCT $week AS `week`, YEAR( `post_date` ) AS `yr`, DATE_FORMAT( `post_date`, '%Y-%m-%d' ) AS `yyyymmdd`, count( `post_ID` ) AS `posts` FROM `$db->posts` $join $where GROUP BY `post_date`, YEAR( `post_date` ) ORDER BY `post_date` $order $limit";
		//desc your function output
	} elseif ( ( 'postbypost' == $type ) || ('alpha' == $type) ) {
		$orderby = ('alpha' == $type) ? 'title ASC ' : 'post_date DESC ';
		$query = "SELECT * FROM $db->posts $join $where ORDER BY $orderby $limit";
		//desc your function output
	}
	if ( $echo )
		echo $output;
	else
		return $output;
}

/**
 * Retrieve archive link content based on predefined or custom code.
 *
 * @param string $url URL to archive.
 * @param string $text Archive text description.
 * @param string $format Optional, default is 'html'. Can be 'link', 'option', 'html', or custom.
 * @param string $before Optional.
 * @param string $after Optional.
 * @return string HTML link content for archive.
 */
function get_archives_link($url, $text, $format = 'html', $before = '', $after = '') {

	if ('link' == $format)
		$link_html = "\t<link rel='archives' title='$text' href='$url' />\n";
	elseif ('option' == $format)
		$link_html = "\t<option value='$url'>$before $text $after</option>\n";
	elseif ('html' == $format)
		$link_html = "\t<li>$before<a href='$url' title='$text'>$text</a>$after</li>\n";
	else // custom
		$link_html = "\t$before<a href='$url' title='$text'>$text</a>$after\n";

	$link_html = apply_filters( 'get_archives_link', $link_html );

	return $link_html;
}

/**
 * Display tag cloud.
 *
 * @param array|string $args Optional. Override default arguments.
 * @return array Generated tag cloud, only if no failures and 'array' is set for the 'format' argument.
 */
function tag_cloud( $args = '' ) {
	$defaults = array(
		'smallest' => 11, 
		'largest' => 22, 
		'orderby' => 'name', 
		'order' => 'ASC',
		'taxonomy' => 'post_tag', 
		'echo' => true,
		'show_option_none' => 'No Tag Clouds'
	);
		
	$args = parse_args( $args, $defaults );
	
	
	global $db, $query;
	
	$add_query = '';
	
	if( is_tag_id() ):	
		$id = esc_sql( $query->get('tag_id') );
	
		$add_query.= ' AND tag_ID='.$id.' ';
	endif;
		
	if( $args['status'] = '' )
		$add_query.= " AND post_status='1' AND post_approved='1'";
	
	$tagresults = $db->get_results( "SELECT * FROM cmsid_tags JOIN cmsid_posts WHERE 1=1 AND cmsid_posts.post_type='post' AND cmsid_posts.post_ID = cmsid_tags.tag_post_ID AND cmsid_tags.tag_content LIKE '%cmsid%' AND cmsid_posts.post_status=1 ORDER BY cmsid_posts.post_date DESC LIMIT 10" );
	$tag_count = count( $tagresults );
	
	if ( empty( $tag_count ) ) {
		$output = $show_option_none;
	} else {
		$tags = array();
		
		foreach( (array) $tagresults as $tagresult ){
			$tags_x = explode(',',strtolower( trim($tagresult->tags) ) );
			
			foreach($tags_x as $key => $val)	{
				$tags[] = $val;	
			}
		}
	
		$output = generate_tag_cloud( $tags, $args );
		$output = apply_filters( 'tag_cloud', $output, $args );
	}

	if ( 'array' == $args['format'] || empty($args['echo']) )
		return $output;

	echo $output;
}

/**
 * Generates a tag cloud (heatmap) from provided data.
 *
 * @param array $tags List of tags.
 * @param string|array $args Optional, override default arguments.
 * @return string
 */
function generate_tag_cloud( $tags, $args = '' ) {	
	$defaults = array(
		'smallest' => 11, 
		'largest' => 22,  
		'orderby' => 'name', 
		'order' => 'ASC'
	);
	
	$args = parse_args( $args, $defaults );
	extract( $args );
	
	$output = '';
	$totalTags = count($tags);
	$jumlah_tag = array_count_values($tags);
	ksort($jumlah_tag);
	if ($totalTags > 0) {
		$tag_mod = array();
		$tag_mod['fontsize']['max'] = $largest;
		$tag_mod['fontsize']['min'] = $smallest;
		
		$min_count = min($jumlah_tag);
		$spread = max($jumlah_tag) - $min_count;
			
		if ( $spread <= 0 )
			$spread = 1;
			
		$font_spread = $tag_mod['fontsize']['max'] - $tag_mod['fontsize']['min'];
			
		if ( $font_spread <= 0 )
			$font_spread = 1;
				
		$font_step = $font_spread / $spread;
			
		foreach($jumlah_tag as $key=>$val) {
				
			$font_size = ( $tag_mod['fontsize']['min'] + ( ( $val - $min_count ) * $font_step ) );
			$datas  = array('id'=>urlencode($key));
				
			$style = '';
			if( empty($id) ) $style = "style='font-size:".$font_size."px'";
				
			$output.= '<a href="#" '.$style.'>'.$key .'</a>, ';
		}
	}
	
	return $output;
}
/**
 * Retrieve the tags for a post.
 *
 * @since 2.3.0
 *
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return string
 */
function the_tags( $sep = ', ' ) {
	echo tag_cloud( array('echo' => false,'sep' => $sep) );
}
/**
 * Loads the comment template specified in $file.
 *
 * @param string $file Optional, default '/comments.php'. The file to load
 * @param bool $separate_comments Optional, whether to separate the comments by comment type. Default is false.
 * @return null Returns null if no comments appear
 */
function comments_template( $file = '/comments.php', $separate_comments = false ) {
	global $db, $id, $comment, $user_login;

	if ( empty($file) )
		$file = '/comments.php';

	if ( file_exists( template_path . $file ) )
		require( template_path . $file );
	else
		require( libs_path . '/comments.php');
}

function get_comments(){
	global $query, $db, $id;
	
	$post_id = (int) esc_sql( $id );
	
	$where = "AND $db->comments.comment_parent='0' ";
	$where.= "AND $db->comments.comment_post_ID='$post_id'";
	
	$request = "SELECT * FROM $db->comments WHERE 1=1 $where";
	
	return $db->get_results( $request );
}

final class Post {

	public $ID;
	public $post_author = 0;
	public $post_date = '0000-00-00 00:00:00';
	public $post_date_gmt = '0000-00-00 00:00:00';
	public $post_content = '';
	public $post_title = '';
	public $post_excerpt = '';
	public $post_status = 'publish';
	public $comment_status = 'open';
	public $ping_status = 'open';
	public $post_password = '';
	public $post_name = '';
	public $to_ping = '';
	public $pinged = '';
	public $post_modified = '0000-00-00 00:00:00';
	public $post_modified_gmt = '0000-00-00 00:00:00';
	public $post_content_filtered = '';
	public $post_parent = 0;
	public $guid = '';
	public $menu_order = 0;
	public $post_type = 'post';
	public $post_mime_type = '';
	public $comment_count = 0;
	public $filter;
	
	public static function get_instance( $post_id ) {
		global $db;

		$post_id = (int) $post_id;
		if ( ! $post_id )
			return false;

		$post = $db->get_row( "SELECT * FROM $db->posts WHERE post_ID = '$post_id' LIMIT 1" );
		
		return new Post( $post );
	}

	/**
	 * Constructor.
	 *
	 * @param WP_Post $post Post object.
	 */
	public function __construct( $post ) {
		foreach ( get_object_vars( $post ) as $key => $value )
			$this->$key = $value;
	}
}

class Comments{
	public $request;
	public $comments;
	public $comment_count = 0;
	public $current_comment = -1;
	public $comment;
	public $data = array();
	public $query_vars = array();
	
	public function __construct(){
	}
	
	public function perse_query(){
	}
	
	public function query( $query =  '' ) {
		global $db;
		
		$this->init();
		$this->parse_query();
		
		$this->comments = $db->get_results( $this->request );
		
		if ( $this->comments ) {
			$this->comment_count = count( $this->comments );
		} else {
			$this->comment_count = 0;
		}
	}
	
	
	public function have_comments(){
		
		if ( $this->current_comment + 1 < $this->comment_count ) {
			return true;
		}

		return false;
	}
	
	public function the_current(){
		global $comment;
		
		$comment = $this->next_post();
		$this->setup_postdata( $comment );	
	}
	
	public function next_post() {
		$this->current_comment++;

		$this->comment = $this->comments[$this->current_comment];
		return $this->comment;
	}
	
	
	public function setup_postdata( $post ) {
		global $id, $authordata, $currentpost, $categorydata, $page, $pages, $multipage, $more, $numpages;

		$id = (int) $post->post_ID;
	}
	
}

function the_ID( $display = true ){
	global $id;	
	
	if ( $display )
		echo $id;
	else
		return $id;
}
/*
function the_title( $display = true ) {	
	global $query;
	
	do_action('the_title');
	
	if( is_single() ) : $template = $query->aaa( 'single' );
	elseif( is_page() ) : $template = $query->aaa( 'page' );
	elseif( is_archive() ) : $template = $query->aaa( 'archive' );
	elseif( is_author() ) : $template = $query->aaa( 'author' );
	elseif( is_category() ) : $template = $query->aaa( 'category' );
	elseif( is_tag() ) : $template = $query->aaa( 'tag' );
	elseif( is_search() ) : $template = $query->aaa( 'search' );
	elseif( is_404() ) : $template = $query->aaa( '404' );
	elseif( is_admin() ) : $template = $query->aaa( 'admin' );
	elseif( is_signin() ) : $template = $query->aaa( 'signin' );
	elseif( is_signup() ) : $template = $query->aaa( 'signup' );
	elseif( is_activate() ) : $template = $query->aaa( 'activate' );
	elseif( is_attachment() ) : $template = $query->aaa( 'attachment' );
	elseif( is_robots() ) : $template = $query->aaa( 'robots' );
	elseif( is_home() ) : $template = $query->aaa( 'index' );
	else: $template = $query->aaa( 'index' ); endif;
	
	$post = get_post();
	
	$title = (isset($post->post_title)) ? $post->post_title : get_info( 'name' );
	
	$title = apply_filters( 'the_title', $title );
	
	if ( $display )
		echo $title;
	else
		return $title;
}*/

function title( $arg = '', $echo = true ){	
	global $query;
	
	if( $arg == 'welcome' || ! $query->is() ){
		$title = get_info( 'name' );
		
	}else{
	
		//$post = $query->get_post();
	
		if( is_category() ):
			$title = the_category();
				
		elseif( is_author() ):
			$title = the_author_name();
				
		elseif( is_archive() ):
			$title = default_date( the_archive(false), 'bulan=1&tahun=1' );
				
		elseif( is_search() ):
			$title = the_search();
				
		elseif( is_page() || is_single() ):
		
			$post = $query->get_post();
			$title = $post[0]->post_title;
		
		endif;
	
		//$title = ( ! empty($post[0]->post_title) ) ? $post[0]->post_title : get_info( 'name' );
	}
	
	$title = apply_filters( 'title', $title );
	
	if ( $echo )
		echo $title;
	else
		return $title;
}

function the_title($before = '', $after = '', $echo = true) {
	$title = get_the_title();

	if ( strlen($title) == 0 )
		return;

	$title = $before . $title . $after;

	if ( $echo )
		echo $title;
	else
		return $title;
}

function get_the_title( $post = 0 ) {	
	$post = get_post( $post );

	$title = isset( $post->post_title ) ? $post->post_title : '';
	$id = isset( $post->ID ) ? $post->ID : 0;

	return apply_filters( 'the_title', $title, $id );
}

function the_content() {
	global $page, $more, $preview, $pages, $multipage;
	
	$output = '';
	$output.= $pages[0];

	echo $output;
}

function the_format_type( $display = true ){
	
	$post = get_post();
	
	if ( $display )
		echo $post->post_type;
	else
		return $post->post_type;
}

function the_posted( $display = true ){
	global $currentpost;
	
	if ( $display )
		echo $currentpost;
	else
		return $currentpost;
}

function the_author_ID( $display = true ){
	
	$ID = get_the_author_meta('ID');
	
	if ( $display )
		echo $ID;
	else
		return $ID;
}

function the_author_name( $display = true ){
		
	$author = get_the_author_meta('author');
	
	if ( $display )
		echo $author;
	else
		return $author;
}


function the_thumbnail( $default = false, $display = true ){
	
	$post = get_post();
		
	$thumb = $post->post_thumb;		
	
	$thumb = (!empty($thumb)) ? (upload_url . date("/Y/m/") . $thumb) : $default;
		
	if ( $display )
		echo $thumb;
	else
		return $thumb;
		
}

function the_category( $display = true ){
	global $query, $db;
	
	$output = '';
	
	if( $query->get('cat') ){
		$cat_id = (int) $query->get('cat');
		
		$results = $db->get_results("SELECT * FROM $db->category WHERE 1=1 AND $db->category.category_ID=" . $cat_id . " ");
		$no = 0;
		foreach( (array) $results as $result ){
			$output.= $result->category_name;
			$no++;
		}
		
		if( $no > 1 ){ $output.= ", "; $no = 0; }
	}
	
	if ( $display )
		echo $output;
	else
		return $output;
}

function the_archive( $display = true ){
	global $query;
	
	$output = $query->get('m');
	
	if ( $display )
		echo $output;
	else
		return $output;
}

function the_search( $display = true ){
	global $query;
	
	$output = $query->get('s');
	
	if ( $display )
		echo $output;
	else
		return $output;
}


function get_categorydata( $post_id, $display = true ){
	global $db;
	
	$post_id = (int) $post_id;
			
	$where = "AND $db->category.category_ID=$db->category_relations.relations_category_ID "; 
	$where.= "AND $db->category_relations.relations_post_ID=" .$post_id. " ";
	
	$orderby = "ORDER BY $db->posts.post_date DESC";
	$limits = "LIMIT 10";
		
	$results = $db->get_results("SELECT * FROM $db->category_relations JOIN $db->category WHERE 1=1 $where $orderby $limits");
	$no = 0;
	foreach( (array) $results as $result ){
		$output.= $result->category_name;
		$no++;
	}
	
	if ( $display )
		echo $output;
	else
		return $output;
}
<?php 
/**
 * @fileName: query.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

class Query{
	public $query;
	public $query_vars = array();
	public $tax_query;
	public $meta_query = false;
	public $date_query = false;
	public $queried_object;
	public $queried_object_id;
	public $request;
	public $posts;
	public $post_count = 0;
	public $current_post = -1;
	public $in_the_loop = false;
	public $post;
	public $comments;
	public $comment_count = 0;
	public $current_comment = -1;
	public $comment;
	public $found_posts = 0;
	public $max_num_pages = 0;
	public $max_num_comment_pages = 0;
	
	public $is_single = false;
	public $is_preview = false;
	public $is_page = false;
	public $is_archive = false;
	public $is_date = false;
	public $is_year = false;
	public $is_month = false;
	public $is_day = false;
	public $is_time = false;
	public $is_author = false;
	public $is_category = false;
	public $is_tag = false;
	public $is_tax = false;
	public $is_search = false;
	public $is_feed = false;
	public $is_comment_feed = false;
	public $is_trackback = false;
	public $is_home = false;
	public $is_404 = false;
	public $is_comments_popup = false;
	public $is_paged = false;
	public $is_admin = false;
	public $is_attachment = false;
	public $is_singular = false;
	public $is_robots = false;
	public $is_posts_page = false;
	public $is_post_type_archive = false;
	
	private $query_vars_hash = false;
	private $query_vars_changed = true;
	
	public $thumbnails_cached = false;
	
	private $stopwords;
	
	private function init_query_flags() {
		$this->is_single = false;
		$this->is_preview = false;
		$this->is_page = false;
		$this->is_archive = false;
		$this->is_date = false;
		$this->is_year = false;
		$this->is_month = false;
		$this->is_day = false;
		$this->is_time = false;
		$this->is_author = false;
		$this->is_category = false;
		$this->is_tag = false;
		$this->is_tax = false;
		$this->is_search = false;
		$this->is_feed = false;
		$this->is_comment_feed = false;
		$this->is_trackback = false;
		$this->is_home = false;
		$this->is_404 = false;
		$this->is_comments_popup = false;
		$this->is_paged = false;
		$this->is_admin = false;
		$this->is_attachment = false;
		$this->is_singular = false;
		$this->is_robots = false;
		$this->is_posts_page = false;
		$this->is_post_type_archive = false;
	}
	
	public function init() {
		unset($this->posts);
		unset($this->query);
		$this->query_vars = array();
		unset($this->queried_object);
		unset($this->queried_object_id);
		$this->post_count = 0;
		$this->current_post = -1;
		$this->in_the_loop = false;
		unset( $this->request );
		unset( $this->post );
		unset( $this->comments );
		unset( $this->comment );
		$this->comment_count = 0;
		$this->current_comment = -1;
		$this->found_posts = 0;
		$this->max_num_pages = 0;
		$this->max_num_comment_pages = 0;

		$this->init_query_flags();
	}
	
	public function parse_query_vars() {
		$this->parse_query();
	}
	
	public function fill_query_vars($array) {
		$keys = array(
			'error'
			, 'm'
			, 'p'
			, 'post_parent'
			, 'subpost'
			, 'subpost_id'
			, 'attachment'
			, 'attachment_id'
			, 'name'
			, 'static'
			, 'pagename'
			, 'page_id'
			, 'second'
			, 'minute'
			, 'hour'
			, 'day'
			, 'monthnum'
			, 'year'
			, 'w'
			, 'category_name'
			, 'tag'
			, 'cat'
			, 'tag_id'
			, 'author'
			, 'author_name'
			, 'feed'
			, 'tb'
			, 'paged'
			, 'comments_popup'
			, 'meta_key'
			, 'meta_value'
			, 'preview'
			, 's'
			, 'sentence'
			, 'fields'
			, 'menu_order'
		);

		foreach ( $keys as $key ) {
			if ( !isset($array[$key]) )
				$array[$key] = '';
		}

		$array_keys = array( 'category__in', 'category__not_in', 'category__and', 'post__in', 'post__not_in',
			'tag__in', 'tag__not_in', 'tag__and', 'tag_slug__in', 'tag_slug__and', 'post_parent__in', 'post_parent__not_in',
			'author__in', 'author__not_in' );

		foreach ( $array_keys as $key ) {
			if ( !isset($array[$key]) )
				$array[$key] = array();
		}
		return $array;
	}
	
	public function parse_query( $query =  '' ) {
		if ( ! empty( $query ) ) {
			$this->init();
			$this->query = $this->query_vars = parse_args( $query );
		} elseif ( ! isset( $this->query ) ) {
			$this->query = $this->query_vars;
		}

		$this->query_vars = $this->fill_query_vars($this->query_vars);
		$qv = &$this->query_vars;
		$this->query_vars_changed = true;

		if ( ! empty($qv['robots']) )
			$this->is_robots = true;
			
	}
	
}
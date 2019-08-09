<?php 
/**
 * @fileName: class-pagination.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;
/********************************************************

 *
 *	Create by Ivo Idham Perdameian
 *
 *	if you using this code or bad for you
 *	please tell me, i'm very glad regarding you
 *	for any qustion and any opinion and all ^_^
 *
 *	Thank God. You are the way....
 *
 ********************************************************
 */
class Pagination {
    public $paging_size = 2; // default 5
    public $page_interval = 1; //interval for panging 1...10 11...20 etc, default 10
    protected $get_page;
    protected $max_page;
	protected $current_page;
    protected $startpaging;
    protected $endpaging;
    protected $total;

    function __construct($config = array()){
		if(count($config) > 0)
		foreach($config as $key=>$value){
			if(is_int($value) && $value > 0) $this->$key = $value;
		}
    }

    function position($num_query,$get_page){ // for page number
		$num_query = (!is_numeric($num_query) || $num_query == '') ? 0 : $num_query;
		$this->total = $num_query;
		$this->max_page = ceil($num_query/$this->paging_size);

		$this->get_page=$get_page;
			if($this->get_page == "" || $this->get_page < 1) {
				$currentPage=0;
				$this->get_page=1;
		} elseif($this->get_page > $this->max_page) {
			$currentPage=($this->max_page-1)*$this->paging_size;
		} else $currentPage=($this->get_page-1)*$this->paging_size;
		return $currentPage;
    }

    protected function set_link(){
		if($this->get_page <= $this->page_interval){
			$this->startpaging = 1;
			$this->endpaging = ($this->max_page > $this->page_interval) ? $this->page_interval : $this->max_page;
		} else {
			if($this->get_page > $this->max_page || $this->get_page == $this->max_page){
			$size = ceil($this->max_page/$this->page_interval);
			$this->startpaging = ($this->max_page > $this->page_interval) ? ($size - 1) * $this->page_interval + 1 : 1;
			$this->endpaging = $this->max_page;
			} elseif($this->get_page > $this->page_interval && $this->get_page < $this->max_page){
			$size = ceil($this->get_page/$this->page_interval);
			$this->startpaging = ($size - 1) * $this->page_interval + 1;
			$this->endpaging = (($var = $size * $this->page_interval) > $this->max_page) ? $this->max_page : $var;
			}
		}
    }

    function paging($page = '', $ul = '', $lu = ''){

		if(empty($page)) return false;

		$limiter = (strpos($page, "=") === false) ? "?" : "&";
		
		$link = '';
		
		if($this->max_page > 1){
			$this->set_link();
			
			if(!empty($ul) ) $link.= $ul;
			else $link.= '<div class="pagination">';
			
			if($this->max_page > $this->page_interval){
				if ($this->get_page > 1){
					$previous = $this->get_page-1;
					
					if(!empty($ul) ) $link.= "<li class=\"previous\"><a href=\"$page$limiter"."paged=$previous\">&larr; Older</a></li>";
					else $link.= "<a href=\"$page$limiter"."paged=$previous\"> Previous</a>";
					
				}
			}
			
			if($this->max_page > $this->page_interval){
				if ($this->get_page < $this->max_page){
					$next=$this->get_page+1;
					
					if(!empty($ul) ) $link.= "<li class=\"next\"><a href=\"$page$limiter"."paged=$next\">Newer  &rarr;</a></li>";
					else $link.= "<a href=\"$page$limiter"."paged=$next\" >Next</a>";
			
				}
			}
			
			
			if(!empty($ul) ) $link.= $lu;
			else $link.= '</div>';
			
			echo $link;
		}
    }

	function __destruct(){
		// Jika mau di isi silahkan.
		// jikalaupun tidak diisi, biarkan fungsi ini tetap ada, PHP otomatis mengoptimalkan
		// objek yang akan dihancurkan ketika proses selesai
	}
}
?>
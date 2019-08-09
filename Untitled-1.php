<?php


function count_comment($id){
	global $db;
		
	$comment_results = $db->query("SELECT COUNT(comment_ID) AS comment_total FROM `$db->comments` WHERE comment_approved='1' AND `post_ID`='$id'"); 
	$comment_result = $db->fetch_obj($comment_results);
	return $comment_result->comment_total;	
		
}

function get_status_comment($id){
	global $db;
	$id = esc_sql( filter_int($id) );
	$post_results = $db->query("SELECT * FROM $db->post WHERE post_ID='$id'");
	$post_result = $db->fetch_obj($post_results);
	return $post_result->status_comment;
}

function get_comment_login(){
	global $authentication;
	return $authentication->check();
}
	
function get_current_comment(){	
	global $authentication;
	
	$user_login = $authentication->exist_value('username');
	$field 		= $authentication->data( compact('user_login') );

	return $field;
}
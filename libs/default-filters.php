<?php 
/**
 * @fileName: default-filters.php
 * @dir: libs/
 */
if(!defined('_iEXEC')) exit;

add_action( 'the_head', 'noindex', 1 );
add_action( 'the_head', 'base_js',1);

add_action( 'the_head_admin', 'loaded_component' );
add_action( 'the_head_admin', 'sidebar_default' );

add_action( 'the_head_request', 'loaded_component');


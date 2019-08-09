<?php 
/**
 * @fileName: admin.php
 * @dir: admin/
 */
if(!defined('_iEXEC')) exit;

the_head_admin();

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Dashboard @admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="libs/theme-compact/css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="libs/theme-compact/css/bootswatch.css" media="screen">
    <link rel="stylesheet" href="libs/theme-compact/css/styled-custom.css" media="screen">
	
	<script type='text/javascript' src="libs/theme-compact/js/jquery-1.10.2.js"></script>
	<script type='text/javascript' src="libs/theme-compact/js/jquery-1.10.1.ui.js"></script>
	<script type='text/javascript' src="libs/theme-compact/js/bootstrap.min.js"></script>
		
	<script src="libs/theme-compact/js/jquery.json.min.js?v=2.2"></script>
  </head>
  <body>
    
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav">
            <li>
              <a href="?admin"><span class="glyphicon glyphicon-home"></span> Dashboard</a>
            </li>
            <li><a href="?admin=plugin"><span class="glyphicon glyphicon-flash"></span> Plugin <span class="badge">3</span></a>
            </li>
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li><a class="img-name" href="?login=profile"> <img src="libs/theme-compact/img/cinqueterre.jpg" class="img-circle" width="23" height="23"> Eko Azza</a></li>
            <li class="dropdown">
              <a href="#"><span class="glyphicon glyphicon-search"></span> View Web</a>
            </li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="dropsetting"><span class="caret"></span></a>
              <ul class="dropdown-menu" aria-labelledby="download">
                <li><a href="?admin&amp;s=options">Pengaturan</a></li>
                <li><a href="?login=logout">Logout</a></li>
                <li class="divider"></li>
                <li><a id="link-modal" href="#">Help?</a></li>
              </ul>
            </li>
          </ul>

        </div>
      </div>
    </div>


    <div class="container">
    	<div class="row">
        	
			<?php 
			$s = false;
			$col_md_number =  12;
			if( !$s ){
				$col_md_number = 10;
			?>
			<div class="col-md-2">       
			<ul class="nav nav-pills nav-stacked">
				<li class="active"><a href="?admin">Dashboard</a></li>
				<li class="nav-header">Actions</li>
				<li class="nav-divider"></li>
				<?php the_menuaction("<li><a href='","</a></li>");?>
				<li class="nav-header">Copyright</li>
				<li><span class="help-block">@CMS.id - 2015</span></li>
			</ul>
            </div>
            <?php }?>
            
            <?php 
			
			the_main_manager("<div class=\"col-md-$col_md_number\">","</div>");?>
			
			
        </div>
    </div>
	
	  
	<script src="libs/theme-compact/js/widget-home.dev.js"></script>
    <script src="libs/theme-compact/js/bootswatch.js"></script>
</body>
</html>
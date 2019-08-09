<?php 
/**
 * @fileName: login.php
 * @dir: admin/
 */
if(!defined('_iEXEC')) exit;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="libs/theme-compact/css/bootstrap.css" rel="stylesheet" id="bootstrap-css">
    <script src="libs/theme-compact/js/jquery-1.10.2.js"></script>
    <script src="libs/theme-compact/js/bootstrap.min.js"></script>
</head>
<body>   

<div class="container">   

<?php 
global $user, $db;

if( $user->check() ):
 
	$result  = $db->get_results( "SELECT * FROM users WHERE user_login=".$user->exist_value('username') );
						
	$image_url = content_url('/uploads/avatar_'.$result->user_avatar);
	if( get_option('avatar_type') == 'gravatar' )
		$image_url = 'http://www.gravatar.com/avatar/?d=mm';
?>
        <div id="loginbox" style="margin-top:20px;" class="mainbox col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">                    
            <div class="panel panel-default" >
                    <div class="panel-heading">
					<a href="?login=profile"><i class="glyphicon glyphicon-chevron-left"></i> Status</a>
					<div class="pull-right"> 
						<a href="?admin" class="pull-right"><i class="glyphicon glyphicon-th-list"></i> Manage Dashboard</a> 
					</div>
					</div>
                    <div class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                            
                                    
							<center>
							<img src="?request&load=libs/timthumb.php&src=<?php echo $image_url;?>&w=100&h=100&zc=1" class="img-circle" alt="<?php echo $result->user_author;?>" height="100" width="100" style="margin-bottom:20px;" >
                            <h4><?php echo $result->user_author;?></h4>
							<p class="text-muted">@<?php echo $result->user_login;?><br>Login Date/Time: <?php echo $result->user_last_update;?></p>
							<a href="?login=logout" id="btn-login" class="btn btn-primary btn-danger btn-lg btn-block">Logout</a>
							
							</center>
				</div>
            </div>    
        </div>
 <?php else:?>
        <div id="loginbox" style="margin-top:20px;" class="mainbox col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">                    
            <div class="panel panel-default" >
                    <div class="panel-body" >
                            
                        <form id="loginform" class="form-horizontal" role="form" action="" method="post">
                                    
                            
						<?php
						if( isset($_POST['login']) ){
							$username 		= esc_sql($_POST['username']);
							$password 		= esc_sql($_POST['password']);	
							$remember 		= esc_sql($_POST['remember']);
							
							$user->sign_in( compact('username','password','remember') );
							
							if( $user->message_text ){
							
								if( $user->message_type == 'error' ){
								?>		
                        <div style="display:visible" id="login-alert" class="alert alert-danger col-sm-12"><?php echo $user->message_text;?></div>
						<?php	
								}else
								if( $user->message_type == 'success' ){
								?>		
                        <div style="display:visible" id="login-alert" class="alert alert-success col-sm-12"><?php echo $user->message_text;?></div>
						<?php	
								}else
								if( $user->message_type == 'message' ){
								?>		
                        <div style="display:visible" id="login-alert" class="alert alert-info col-sm-12"><?php echo $user->message_text;?></div>
						<?php	
								}
							}
						}
								
						?>
                            
                            
                           <div class="clearfix"></div>
							<center>
                            <div class="form-group-lg input-group">
							<input id="login-username" type="text" class="form-control" name="username" value="" placeholder="Masukkan pengguna / email" autofocus>
							<input id="login-password" type="password" class="form-control" name="password" placeholder="Masukkan sandi">
							<div class="checkbox pull-left">
							<label>
								<input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                            </label>
                            </div>
							<button id="btn-login" class="btn btn-primary btn-success btn-lg btn-block" type="submit" name="login">Login</button>
							<!--
							<center>OR</center>
							<button id="btn-fblogin" class="btn btn-primary btn-lg btn-block">Login with Facebook</button>
							-->
							</div>
							<div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="padding-top:15px;" >
                                            Don't have an account! 
                                        <a href="?signup">
                                            Sign Up Here
                                        </a>
										or
                                        <a href="?signin=lost">
                                            Lost
                                        </a>
                                        </div>
                                    </div>
                                </div>   
							</center> 
                        </form>   



                    </div>                     
            </div>  
        </div>
<?php
endif;

?>

</div>
    
</body>
</html>

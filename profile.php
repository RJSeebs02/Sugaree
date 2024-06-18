<?php
include_once 'config/config.php';
include_once 'class/class.user.php';

/*Define Object*/
$user = new User();

/*Checks if the user is logged in */
if(!$user->get_session()){
	header("location: login_register.php");
}
    /*Get user logged in details */
    $user_identifier = $_SESSION['user_identifier'];
    $user_id = $user->get_user_id($user_identifier);
    $user_name = $user->get_user_name($user_id);
    $user_email = $user->get_user_email($user_id);
    $user_firstname = $user->get_user_fname($user_id);
    $user_lastname = $user->get_user_lname($user_id);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Profile</title>
	</head>
	<header>
	<div id="navbar">
			<div id="navbar-contents">	
				
			</div>
	</div>
	</header>
	<body>

    <div class='user-details'>
        <p>Welcome <?php echo $user_firstname.' '.$user_lastname?></p>
        <p> Username: <?php echo $user_name?><br>Email: <?php echo $user_email?></p>

        <a href='logout.php'><button class='btn'>Logout</button></a>
        <a href='index.php'><button class='btn'>Home</button></a>
        </div>
	</body>
</html>
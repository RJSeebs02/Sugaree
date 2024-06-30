<?php
/*Include User Class File */
include '../class/class.user.php';

/*Parameters for switch case*/
$action = isset($_GET['action']) ? $_GET['action'] : '';

/*Switch case for actions in the process */
switch($action){
	case 'new':
        create_new_user();
	break;
    case 'update':
        update_user();
	break;
    case 'update_image':
        update_user_image();
	break;
    case 'delete':
        delete_user();
    break;
}

/*Main Function Process for creating an user */
function create_new_user(){
    $user = new User();
    /*Receives the parameters passed from the creation page form */
    $user_firstname = ucfirst($_POST['firstname']);
    $user_lastname = ucfirst($_POST['lastname']);
    $user_name = $_POST['username'];
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];
    $user_status = $_POST['status'];

    $password = md5($user_password);

    /*$verify_query = mysqli_query($con, "SELECT user_email FROM tbl_users WHERE user_email='$user_email'");*/

    /* Check if the username or email already exists */
    if ($user->user_exists($user_name, $user_email)) {
        echo "<div id='error_box'><div id='error_notif'>Username or email already exists.</div></div>";
        header("location: ../login_register.php");
    } else {
        /* Pass the parameters to the class function */
        $result = $user->new_user($user_firstname,$user_lastname,$user_name,$user_email,$password,$user_status);
        if($result){
            header("location: ../login_register.php");
        }
    }
}

/*Main Function Process for updating an admin */
function update_user(){  
    $user = new User();
    /*Receives the parameters passed from the profile updating page form */
    $user_id = $_POST['userid'];
    $user_firstname = ucfirst($_POST['firstname']);
    $user_lastname = ucfirst($_POST['lastname']);
    
    /*Passes the parameters to the class function */
    $result = $user->update_user($user_id,$user_firstname,$user_lastname);
    if($result){
        header("location: ../profile.php");
    }
}

/*Main Function Process for deleting an admin */
function delete_admin(){
    if (isset($_POST['adm_username'])) {
        $admin = new Admin();
        $id = $_POST['adm_username'];
        $result = $admin->delete_admin($id);
        if ($result) {
            header("location: ../index.php?page=admins");
        } 
    }
}

/// Function to update user image
function update_user_image() {
    // Initialize User object
    $user = new User();

    // Check if user_id and profile_image are set in POST
    if (isset($_POST['user_id']) && isset($_FILES['profile_image'])) {
        $user_id = $_POST['user_id'];
        $image = $_FILES['profile_image'];

        // Directory where images will be uploaded
        $targetDir = "../uploads/";

        // Handle file upload
        $fileName = basename($image['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');

        if (!empty($fileName)) {
            // Check file type
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($image['tmp_name'], $targetFilePath)) {
                    // Update user image in database
                    $result = $user->update_user($user_id, $fileName);
                    if ($result) {
                        // Redirect to profile page
                        header("location: ../profile.php?subpage=about");
                        exit();
                    } else {
                        echo "Error updating user image in database.";
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo 'Invalid file format. Allowed types: jpg, jpeg, png, gif';
            }
        } else {
            echo 'No file selected.';
        }
    } else {
        echo 'Missing user_id or profile_image in POST.';
    }
}
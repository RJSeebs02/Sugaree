<?php
include_once 'config/config.php';
include_once 'class/class.user.php';

/* Define Object */
$user = new User();

/* Checks if the user is logged in */
if(!$user->get_session()){
    header("location: login_register.php");
    exit();
}

/*Parameter variables for the navbar*/
$subpage = (isset($_GET['subpage']) && $_GET['subpage'] != '') ? $_GET['subpage'] : '';

/* Get user logged in details */
$user_identifier = $_SESSION['user_identifier'];
$user_id = $user->get_user_id($user_identifier);
$user_name = $user->get_user_name($user_id);
$user_email = $user->get_user_email($user_id);
$user_firstname = $user->get_user_fname($user_id);
$user_lastname = $user->get_user_lname($user_id);
$user_review = $user->get_user_review($user_id);
$user_rating = $user->get_user_rating($user_id);
$user_status = $user->get_user_status($user_id);

/* Process form submission */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $review_rating = $_POST['Rating'];
    $review_content = $_POST['content'];

    // Basic validation
    if (empty($review_rating) || empty($review_content)) {
        echo "Please fill out all fields.";
    } else {
        // Use prepared statements to prevent SQL injection
        $con = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Prepare and execute the insert statement
        $stmt = $con->prepare("INSERT INTO tbl_review (user_id, review_rating, review_content) VALUES (?, ?, ?)");
        $stmt->bind_param("ids", $user_id, $review_rating, $review_content);

        if ($stmt->execute()) {
            // Prepare and execute the update statement
            $stmt2 = $con->prepare("UPDATE tbl_users SET user_status = ? WHERE user_id = ?");
            $user_status = "Reviewed";
            $stmt2->bind_param("si", $user_status, $user_id);

            if ($stmt2->execute()) {
                echo "Review added and user status updated successfully";
                header("Location: profile.php"); // Redirect after successful submission
                exit();
            } else {
                echo "Error updating user status: " . $stmt2->error;
            }

            $stmt2->close();
        } else {
            echo "Error adding review: " . $stmt->error;
        }

        $stmt->close();
        $con->close();

        // Handle image upload
        if (!empty($_FILES['profile_image']['name'])) {
            $targetDir = "uploads/";
            $fileName = basename($_FILES['profile_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Allow certain file formats
            $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array($fileType, $allowTypes)) {
                // Upload file to server
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
                    // Update user image in database
                    $stmt3 = $con->prepare("UPDATE tbl_users SET user_image = ? WHERE user_id = ?");
                    $stmt3->bind_param("si", $fileName, $user_id);

                    if ($stmt3->execute()) {
                        echo "Profile photo updated successfully";
                        // Update $user_image variable if needed
                        $user_image = $fileName;
                    } else {
                        echo "Error updating profile photo: " . $stmt3->error;
                    }

                    $stmt3->close();
                } else {
                    echo "Error uploading photo.";
                }
            } else {
                echo 'Invalid file format. Allowed types: jpg, jpeg, png, gif';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
    <link rel="stylesheet" href="css/style4.css"> <!-- Ensure this path is correct -->
</head>
<body>

<div class="container emp-profile">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-img">
                    <img src="<?php echo !empty($user_image) ? 'uploads/' . $user_image : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS52y5aInsxSm31CvHOFHWujqUx_wWTS9iM6s7BAm21oEN_RiGoog'; ?>" alt=""/>

                    <div class="file btn btn-lg btn-primary">
                        Change Photo
                        <input type="file" name="profile_image"/>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="profile-head">
                    <h5><?php echo $user_firstname . ' ' . $user_lastname; ?></h5>
                    <h6>Web Developer and Designer</h6>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="profile.php?subpage=about" role="tab" aria-controls="home" aria-selected="true">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="review-tab" data-toggle="tab" href="profile.php?subpage=review" role="tab" aria-controls="review" aria-selected="false">Review</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-2">
                <a href='index.php' class="btn btn-secondary">Home</a> 
                <a href='logout.php' class="btn btn-secondary">Logout</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="profile-work">
                    <p>Social Media Links</p>
                    <a href="">Facebook</a><br/>
                    <a href="">Twitter</a><br/>
                    <a href="">Instagram</a>
                
                </div>
            </div>
            <div class="col-md-8">
            <?php
                /*Switch case for the subpage of the Admins Page */
                switch($subpage){
                    case 'about':
                        require_once 'profile-about.php';
                    break; 
                    case 'review':
                        require_once 'review.php';
                    break; 
                    default:
                        require_once 'profile-about.php';
                    break;
                }
            ?>
            </div>
        </div>
</div>



</body>
</html>

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
    $review_rating = $_POST['rating'];
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
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css" integrity="sha384-SI27wrMjH3ZZ89r4o+fGIJtnzkAnFs3E4qz9DIYioCQ5l9Rd/7UAa8DHcaL8jkWt" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
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
    <form method="POST" action="process/process.user.php?action=update"> <!---Executes process after clicking the update/submit button-->
        <label for="userid"></label>
        <input type="text" id="userid" class="text" name="userid" value="<?php echo $user_id?>" hidden><br>

        <label for="username">Username: </label>
        <input type="text" id="username" class="text" name="username" value="<?php echo $user_name?>" disabled><br>

        <label for="email">Email Address: </label>
        <input type="text" id="email" class="text" name="email" value="<?php echo $user_email?>" disabled><br>

        <label for="firstname">First Name: </label>
        <input type="text" id="firstname" class="text" name="firstname" value="<?php echo $user_firstname?>" placeholder="Enter First Name" required><br>

        <label for="lastname">Last Name: </label>
        <input type="text" id="lastname" class="text" name="lastname" value="<?php echo $user_lastname?>" placeholder="Enter Last Name" required><br>

        <input type="submit" value="Update"> <!--Button that passes parameters input to the process file--->
    </form>

    <a href='logout.php'><button class='btn'>Logout</button></a>
    <a href='index.php'><button class='btn'>Home</button></a>
</div>

<?php if($user_status == 'Not yet reviewed'){
?>

<div class="container">
    <div class="row">
        <form action="profile.php" method="post" id="reviewForm">
            <div>
                <h3>Leave a review!</h3>
            </div>
            <div>
                <label>Comment</label>  
                <input type="text" name="content" value="<?php echo $user_review?>" required>
            </div>
            <div class="rateyo" id="rating" data-rateyo-rating="4" data-rateyo-num-stars="5" data-rateyo-score="3">
            </div>
            <span class='result'>rating: 0</span>
            <input type="hidden" name="rating">
            <div><input type="submit" name="add_review" value="Submit Review"></div>
        </form>
    </div>
</div>

<?php
}else{?>

<div class="container">
    <div class="row">
        <form action="profile.php" method="post" id="reviewForm">
            <div>
                <h3>Leave a review!</h3>
            </div>
            <div>
                <label>Comment</label>  
                <input type="text" name="content" value="<?php echo $user_review?>" required>
            </div>
            <div class="rateyo" id="rating" value="<?php $user_rating?>" data-rateyo-rating="4" data-rateyo-num-stars="5" data-rateyo-score="3">
            </div>
            <span class='result'>rating: <?php echo $user_rating?></span>
            <input type="hidden" name="rating">
            <div><input type="submit" name="update_review" value="Update Review"></div>
        </form>
    </div>
</div>

<?php
}
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script>
$(function () {
    $(".rateyo").rateYo().on("rateyo.change", function (e, data) {
        var rating = data.rating;
        $(this).parent().find('.score').text('score :' + $(this).attr('data-rateyo-score'));
        $(this).parent().find('.result').text('rating :' + rating);
        $(this).parent().find('input[name=rating]').val(rating); // add rating value to input field
    });
});
</script>

</body>
</html>
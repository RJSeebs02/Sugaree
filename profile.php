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
$user_image = $user->get_user_image($user_id);

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
    <style>
        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content/Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            max-width: 600px; /* Maximum width */
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Image inside modal */
        #modalImage {
            max-width: 100%;
            max-height: 400px; /* Maximum height */
            display: block;
            margin: 0 auto 10px auto; /* Center the image */
        }
    </style>
</head>
<body>

<div class="container emp-profile">
    <div class="row">
        <div class="col-md-4">
            <div class="profile-img">
                <img src="<?php echo !empty($user_image) ? 'uploads/' . $user_image : 'https://raw.githubusercontent.com/RJSeebs02/sugaree_img/main/defaultpic.jpg'; ?>" alt="User Image"/>
                
                <button id="openPopupBtn" class="btn btn-primary">Change Image</button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="profile-head">
                <h5><?php echo $user_firstname . ' ' . $user_lastname; ?></h5>
                <h6>Customer Profile</h6>
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

<!-- The Modal -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImage" src="<?php echo !empty($user_image) ? 'uploads/' . $user_image : 'https://raw.githubusercontent.com/RJSeebs02/sugaree_img/main/defaultpic.jpg'; ?>" alt="User Image"/>
        <button id="changeImageBtn" class="btn btn-primary">Change Image</button>
        <input type="file" id="imageUpload" style="display: none;" accept="image/*"/>
        <button id="saveImageBtn" class="btn btn-success">Save</button>
    </div>
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("openPopupBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // Get the change image button and file input
    var changeImageBtn = document.getElementById("changeImageBtn");
    var imageUpload = document.getElementById("imageUpload");
    var saveImageBtn = document.getElementById("saveImageBtn");

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // When the user clicks the change image button, trigger the file input click
    changeImageBtn.onclick = function() {
        imageUpload.click();
    }

    // When a file is selected, display it in the modal image
    imageUpload.onchange = function(event) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById("modalImage").src = e.target.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    // Handle the save button click
    saveImageBtn.onclick = function() {
        // Assuming the form handling is done via PHP
        var formData = new FormData();
        formData.append('profile_image', imageUpload.files[0]);
        formData.append('user_id', '<?php echo $user_id; ?>');


        $.ajax({
            url: 'profile.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert(response); // Display success message (optional)
                modal.style.display = "none"; // Close the modal
                // Redirect to another page
                window.location.href = 'process/process.user.php?action=update_image&user_id=<?php echo $user_id; ?>';
            }
        });
    }
</script>

</body>
</html>

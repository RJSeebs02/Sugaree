<?php if($user_status == 'Not yet reviewed'){
?>

<div class="container">
    <div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="profile.php?subpage=review" method="post" id="reviewForm">
        <div class="review-box">
                <h3>Leave a review!</h3>
            <div>
                <label>Comment</label>  
                <textarea class="form-control review-textarea" name="content" value="<?php echo $user_review?>" required></textarea>
            </div>
            <span class='result'>Rating: <?php echo $user_rating?></span>
            <div class="rateyo" id="Rating" value="<?php $user_rating?>" data-rateyo-rating="4" data-rateyo-num-stars="5" data-rateyo-score="3"></div>
            <input type="hidden" name="Rating">
            <div><input type="submit" name="update_review" class="btn btn-primary btn-lg" value="Update Review"></div>
        </form>
        </div>
    </div>
    </div>
</div>

<?php
}else{?>

<div class="container">
    <div class="row">
    <div class="col-md-8 offset-md-2">
        <form action="profile.php?subpage=review" method="post" id="reviewForm">
        <div class="review-box">
                <h3>Leave a review!</h3>
            <div>
                <label>Comment</label>  
                <textarea class="form-control review-textarea" name="content" value="<?php echo $user_review?>" required></textarea>
            </div>
            <span class='result'>Rating: <?php echo $user_rating?></span>
            <div class="rateyo" id="Rating" value="<?php $user_rating?>" data-rateyo-rating="4" data-rateyo-num-stars="5" data-rateyo-score="3"></div>
            <input type="hidden" name="Rating">
            <div><input type="submit" name="update_review" class="btn btn-primary btn-lg" value="Update Review"></div>
        </form>
        </div>
    </div>
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
        $(this).parent().find('.result').text('Rating :' + rating);
        $(this).parent().find('input[name=Rating]').val(rating); // add rating value to input field
    });
});
</script>
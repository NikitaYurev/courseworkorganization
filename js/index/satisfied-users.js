$(document).ready(function() {
    $.ajax({
        url: 'fetch_satisfied_users.php',
        type: 'GET',
        dataType: 'json',  // Expect JSON response
        success: function(data) {
            if (Array.isArray(data) && data.length) {
                // Directly join the array of HTML strings
                var reviewsHTML = data.join('');
                $('#satisfied-users').html(reviewsHTML);

                // Set up the carousel
                var reviews = $('#satisfied-users .review-box');
                var currentReviewIndex = 0;
                var reviewInterval = 15000;  // 15 seconds

                function showNextReview() {
                    reviews.hide();  // Hide all reviews
                    reviews.eq(currentReviewIndex).fadeIn();  // Show the current review
                    currentReviewIndex = (currentReviewIndex + 1) % reviews.length;
                }

                // Show the first review initially
                showNextReview();
                // Set up the interval to switch reviews
                setInterval(showNextReview, reviewInterval);
            } else {
                $('#satisfied-users').html('<p>No positive reviews found.</p>'); // Display a message if the array is empty
            }
        },
        error: function(xhr) {
            $('#satisfied-users').html('<p>Error loading reviews. Details: ' + xhr.responseText + '</p>');
            console.log("AJAX Error:", xhr.status, xhr.responseText);
        }
    });
});

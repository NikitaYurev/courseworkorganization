// $(document).ready(function() {
//     $('#rating-form').on('submit', function(event) {
//         event.preventDefault(); // Prevent the default form submission
//         $.ajax({
//             type: "POST",
//             url: "save_review.php",
//             data: $(this).serialize(), // Serialize the form data
//             success: function(data) {
//                 var response = JSON.parse(data); // Parse the JSON response
//                 $('#messageContent').html(response.message); // Set the message text
//                 $('#messageModal').css('background-color', response.alertType === 'success' ? '#ccffcc' : '#ffcccc'); // Set background color based on alert type
//                 $('#messageModal').show(); // Show the modal
//             },
//             error: function(xhr, status, error) {
//                 console.error("AJAX Error: " + status + error);
//             }
//         });
//     });

//     $('.close-button').click(function() {
//         $('#messageModal').hide();
//     });

//     $(window).click(function(event) {
//         if ($(event.target).is('#messageModal')) {
//             $('#messageModal').hide();
//         }
//     });
// });


$(document).ready(function() {
    $('#rating-form').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: "save_review.php",
            data: formData,
            dataType: "json", // Expecting JSON response
            success: function(response) {
                $('#messageModal').find('.modal-body').html(response.message).removeClass('danger success').addClass(response.alertType);
                $('#messageModal').fadeIn(); // Use jQuery fadeIn for effect
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", status, error);
            }
        });
    });

    $('.close-button').click(function() {
        $('#messageModal').fadeOut(); // Use fadeOut to hide
    });

    // Close modal on outside click
    $(window).click(function(event) {
        if ($(event.target).is('#messageModal')) {
            $('#messageModal').fadeOut();
        }
    });
});
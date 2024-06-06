$(document).ready(function() {
    $('#review-form').on('submit', function(event) {
        event.preventDefault();
        var rating = document.getElementById('review-form').getAttribute('data-rating');
        var formData = $(this).serialize() + '&user-rating=' + rating;

        var isUpdate = $('#is_update').val() === '1' ? 'update_review.php' : 'save_review.php';

        $.ajax({
            type: "POST",
            url: isUpdate,
            data: formData,
            dataType: "json",
            success: function(response) {
                $('#messageContent').html(response.message);
                $('#messageContent').addClass(response.alertType === 'success' ? 'success' : 'danger');
                $('#messageModal').show(); // Show the modal
            },
            error: function(xhr, status, error) {
                $('#messageContent').html("Error: " + error);
                $('#messageContent').addClass('danger');
                $('#messageModal').show(); // Show the modal
            }
        });
    });

    $('.close-button').click(function() {
        $('#messageModal').hide();
    });

    $(window).click(function(event) {
        if ($(event.target).is('#messageModal')) {
            $('#messageModal').hide();
        }
    });
});

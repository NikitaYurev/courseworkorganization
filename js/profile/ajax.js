$(document).ready(function() {
    $('#review-form').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize() + '&user-rating=' + $('#review-form').data('rating');
        var isUpdate = $('#is_update').val() === '1' ? 'update_review.php' : 'save_review.php';

        $.ajax({
            type: "POST",
            url: isUpdate,
            data: formData,
            dataType: "json",
            success: function(response) {
                $('#messageContent').html(response.message).removeClass('success danger').addClass(response.alertType);
                $('#messageModal').css('display', 'flex');
            },
            error: function(xhr, status, error) {
                $('#messageContent').html("Error: " + error).removeClass('success').addClass('danger');
                $('#messageModal').css('display', 'flex');
            }
        });
    });

    $('.close-button').on('click', function() {
        $('#messageModal').css('display', 'none');
    });

    $(window).on('click', function(event) {
        if ($(event.target).is('#messageModal')) {
            $('#messageModal').css('display', 'none');
        }
    });
});

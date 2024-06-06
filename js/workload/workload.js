$(document).ready(function() {
    // Load chat for a project
    $('.project-item').click(function() {
        var projectId = $(this).data('project-id');
        $('.project-item').removeClass('selected');
        $(this).addClass('selected');

        $.ajax({
            url: 'fetch_chat.php',
            method: 'POST',
            data: { project_id: projectId },
            success: function(data) {
                $('.chat-box').html(data);
            }
        });
    });

    // Load chat for a coworker
    $('.worker-item').click(function() {
        var coworkerId = $(this).data('coworker-id');
        $('.worker-item').removeClass('selected');
        $(this).addClass('selected');

        $.ajax({
            url: 'fetch_chat.php',
            method: 'POST',
            data: { coworker_id: coworkerId },
            success: function(data) {
                $('.chat-box').html(data);
            }
        });
    });
});

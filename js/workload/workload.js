$(document).ready(function() {
    $('.project-item').click(function() {
        var projectId = $(this).data('project-id');

        // Highlight the selected project
        $('.project-item').removeClass('selected');
        $(this).addClass('selected');

        // Fetch and display chat for the selected project
        $.ajax({
            url: 'fetch_chat.php',
            method: 'GET',
            data: { project_id: projectId },
            success: function(response) {
                $('#chat-section .chat-box').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching chat:", error);
            }
        });
    });

    $('.worker-item').click(function() {
        var coworkerId = $(this).data('coworker-id');

        // Highlight the selected coworker
        $('.worker-item').removeClass('selected');
        $(this).addClass('selected');

        // Fetch and display chat with the selected coworker (you can modify this part as needed)
        // Example: $.ajax({ url: 'fetch_coworker_chat.php', method: 'GET', data: { coworker_id: coworkerId }, ... });
    });
});

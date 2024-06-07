$(document).ready(function () {
    function clearSelections() {
        $('.project-item').removeClass('selected');
        $('.worker-item').removeClass('selected');
    }

    // Fetch chat messages for projects
    $('.project-item').on('click', function () {
        const projectId = $(this).data('project-id');
        clearSelections();
        $(this).addClass('selected');

        $.ajax({
            url: 'fetch_chat.php',
            method: 'POST',
            data: { project_id: projectId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let chatBox = $('#chat-section .chat-box');
                    chatBox.empty();
                    response.messages.forEach(function (message) {
                        chatBox.append('<div class="chat-message"><strong>' + message.username + ':</strong> ' + message.message + '<br><small>' + message.created_at + '</small></div>');
                    });
                } else {
                    $('#chat-section .chat-box').html('<p>' + response.error + '</p>');
                }
            },
            error: function () {
                $('#chat-section .chat-box').html('<p>Error fetching chat messages.</p>');
            }
        });
    });

    // Fetch chat messages for coworkers
    $('.worker-item').on('click', function () {
        const coworkerId = $(this).data('coworker-id');
        clearSelections();
        $(this).addClass('selected');

        $.ajax({
            url: 'fetch_chat.php',
            method: 'POST',
            data: { coworker_id: coworkerId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    let chatBox = $('#chat-section .chat-box');
                    chatBox.empty();
                    response.messages.forEach(function (message) {
                        chatBox.append('<div class="chat-message"><strong>' + message.username + ':</strong> ' + message.message + '<br><small>' + message.created_at + '</small></div>');
                    });
                } else {
                    $('#chat-section .chat-box').html('<p>' + response.error + '</p>');
                }
            },
            error: function () {
                $('#chat-section .chat-box').html('<p>Error fetching chat messages.</p>');
            }
        });
    });
});

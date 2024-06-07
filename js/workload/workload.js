$(document).ready(function() {
    var selectedProjectId = null;
    var selectedCoworkerId = null;
    var reloadInterval = null;

    function fetchProjectMessages(projectId) {
        $.ajax({
            url: 'fetch_chat.php',
            type: 'POST',
            data: { project_id: projectId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayMessages(response.messages);
                } else {
                    $('.chat-box').html('<p>' + response.error + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('.chat-box').html('<p>Error fetching chat messages: ' + error + '</p>');
            }
        });
    }

    function fetchCoworkerMessages(coworkerId) {
        $.ajax({
            url: 'fetch_chat.php',
            type: 'POST',
            data: { coworker_id: coworkerId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayMessages(response.messages);
                } else {
                    $('.chat-box').html('<p>' + response.error + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('.chat-box').html('<p>Error fetching chat messages: ' + error + '</p>');
            }
        });
    }

    function displayMessages(messages) {
        var chatBox = $('.chat-box');
        chatBox.empty();
        messages.forEach(function(message) {
            var messageElement = $('<div class="chat-message"></div>');
            messageElement.html('<strong>' + message.username + ':</strong> ' + message.message + '<br><small>' + message.created_at + '</small>');
            chatBox.append(messageElement);
        });
    }

    function autoReloadMessages() {
        if (selectedProjectId) {
            fetchProjectMessages(selectedProjectId);
        } else if (selectedCoworkerId) {
            fetchCoworkerMessages(selectedCoworkerId);
        }
    }

    $('.project-item').click(function() {
        selectedProjectId = $(this).data('project-id');
        selectedCoworkerId = null;
        $('.project-item, .worker-item').removeClass('selected');
        $(this).addClass('selected');
        fetchProjectMessages(selectedProjectId);
        clearInterval(reloadInterval);
        reloadInterval = setInterval(autoReloadMessages, 5000);
    });

    $('.worker-item').click(function() {
        selectedCoworkerId = $(this).data('coworker-id');
        selectedProjectId = null;
        $('.project-item, .worker-item').removeClass('selected');
        $(this).addClass('selected');
        fetchCoworkerMessages(selectedCoworkerId);
        clearInterval(reloadInterval);
        reloadInterval = setInterval(autoReloadMessages, 5000);
    });

    $('#send-message-btn').click(function() {
        var message = $('#message-input').val().trim();
        if (message === '') {
            alert('Message cannot be empty.');
            return;
        }

        var data = {
            message: message
        };

        if (selectedProjectId) {
            data.project_id = selectedProjectId;
        } else if (selectedCoworkerId) {
            data.coworker_id = selectedCoworkerId;
        } else {
            alert('Please select a project or coworker to send a message.');
            return;
        }

        $.ajax({
            url: 'send_message.php',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (selectedProjectId) {
                        fetchProjectMessages(selectedProjectId);
                    } else if (selectedCoworkerId) {
                        fetchCoworkerMessages(selectedCoworkerId);
                    }
                    $('#message-input').val('');
                } else {
                    alert(response.error || 'Failed to send message.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error sending message: ' + error);
            }
        });
    });
});

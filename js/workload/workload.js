// workload.js
$(document).ready(function() {
    $('.project-item').on('click', function() {
        const projectName = $(this).text();
        $('#chat-section .chat-box').html('<p>Chat for ' + projectName + ' will be displayed here.</p>');
    });

    $('.worker-item').on('click', function() {
        const workerName = $(this).text();
        $('#chat-section .chat-box').html('<p>Chat with ' + workerName + ' will be displayed here.</p>');
    });
});

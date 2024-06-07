$(document).ready(function() {
    $('.btn-success').click(function() {
        var row = $(this).closest('tr');
        var requestId = row.find('td:first').text();
        
        // Handle accept action here
        $.post('accept_project.php', { id: requestId }, function(response) {
            if (response.success) {
                row.remove();
            } else {
                alert('Failed to accept project.');
            }
        }, 'json');
    });

    $('.btn-danger').click(function() {
        var row = $(this).closest('tr');
        var requestId = row.find('td:first').text();
        
        // Handle reject action here
        $.post('reject_project.php', { id: requestId }, function(response) {
            if (response.success) {
                row.remove();
            } else {
                alert('Failed to reject project.');
            }
        }, 'json');
    });
});

document.addEventListener('DOMContentLoaded', function () {
    let selectedProjectId = null;

    // Event listener for project selection
    document.querySelectorAll('.project-list-container tr').forEach(row => {
        row.addEventListener('click', function () {
            selectedProjectId = this.getAttribute('data-project-id');
            document.querySelectorAll('.project-list-container tr').forEach(r => r.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    // Event listener for accept button
    document.getElementById('accept-button').addEventListener('click', function () {
        if (selectedProjectId) {
            fetch('accept_project.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: selectedProjectId }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Project accepted successfully');
                        location.reload();
                    } else {
                        alert('Failed to accept project: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            alert('Please select a project to accept');
        }
    });

    // Event listener for deny button
    document.getElementById('deny-button').addEventListener('click', function () {
        if (selectedProjectId) {
            fetch('deny_project.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: selectedProjectId }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Project denied successfully');
                        location.reload();
                    } else {
                        alert('Failed to deny project: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        } else {
            alert('Please select a project to deny');
        }
    });
});

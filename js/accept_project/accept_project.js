document.addEventListener('DOMContentLoaded', function() {
    const projectForm = document.getElementById('project-form');
    const acceptBtn = document.getElementById('accept-project-btn');
    const denyBtn = document.getElementById('deny-project-btn');
    const projectList = document.querySelector('.project-list-container tbody');

    projectList.addEventListener('click', function(e) {
        if (e.target && e.target.nodeName == 'TR') {
            const projectId = e.target.getAttribute('data-project-id');
            fetchProjectDetails(projectId);
        }
    });

    acceptBtn.addEventListener('click', function() {
        const projectId = projectForm.id.value;
        if (projectId) {
            acceptProject(projectId);
        }
    });

    denyBtn.addEventListener('click', function() {
        const projectId = projectForm.id.value;
        if (projectId) {
            denyProject(projectId);
        }
    });

    function fetchProjectDetails(projectId) {
        fetch(`get_project_details.php?id=${projectId}`)
            .then(response => response.json())
            .then(data => {
                projectForm.id.value = data.id;
                projectForm.name.value = data.name;
                projectForm.email.value = data.email;
            });
    }

    function acceptProject(projectId) {
        fetch('accept_project.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${projectId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Project accepted successfully');
                location.reload();
            } else {
                alert('Failed to accept project: ' + data.message);
            }
        });
    }

    function denyProject(projectId) {
        fetch('deny_project.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${projectId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Project denied successfully');
                location.reload();
            } else {
                alert('Failed to deny project: ' + data.message);
            }
        });
    }
});

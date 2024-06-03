document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('search-button').addEventListener('click', function() {
        searchUsers();
    });

    document.getElementById('search-input').addEventListener('input', function() {
        searchUsers();
    });
});

function searchUsers() {
    var searchInput = document.getElementById('search-input').value.toLowerCase();
    var userRows = document.querySelectorAll('.user-list-container tbody tr');

    userRows.forEach(function(row) {
        var username = row.cells[0].textContent.toLowerCase();
        var email = row.cells[1].textContent.toLowerCase();

        if (username.includes(searchInput) || email.includes(searchInput)) {
            row.style.display='';
        } else {
            row.style.display = 'none';
        }
    });
}
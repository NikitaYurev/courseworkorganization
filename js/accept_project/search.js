document.getElementById('search-button').addEventListener('click', function () {
    const searchInput = document.getElementById('search-input').value;
    window.location.href = `accept_project.php?search=${searchInput}`;
});

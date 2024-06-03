document.addEventListener("DOMContentLoaded", function() {
    // Get all table rows
    let rows = document.querySelectorAll("tbody tr");

    // Add event listeners to each row
    rows.forEach(function(row) {
        row.addEventListener("mouseenter", function() {
            this.style.backgroundColor = "#f0f0f0"; // Change background color on hover
        });

        row.addEventListener("mouseleave", function() {
            this.style.backgroundColor = ""; // Revert to default background color when mouse leaves
        });
    });
});

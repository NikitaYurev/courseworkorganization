document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("user-form");
    

    form.addEventListener("submit", function(event) {
        event.preventDefault();
        const username = document.getElementById("username");
        const email = document.getElementById("email");
        let valid = true;

        // Check if fields are empty
        if (username.value.trim() === "") {
            username.classList.add("is-invalid");
            valid = false;
        } else {
            username.classList.remove("is-invalid");
        }

        if (email.value.trim() === "") {
            email.classList.add("is-invalid");
            valid = false;
        } else {
            email.classList.remove("is-invalid");
        }

        // Check email format
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        if (!emailPattern.test(email.value.trim())) {
            email.classList.add("is-invalid");
            document.getElementById("email-invalid-feedback").style.display = "block";
            valid = false;
        } else {
            email.classList.remove("is-invalid");
            document.getElementById("email-invalid-feedback").style.display = "none";
        }
    });

    // Add hover effect to change cursor to pointer when hovering over a row
    const rows_hover = document.querySelectorAll("tbody tr");
    rows_hover.forEach(function(row) {
        row.addEventListener("mouseenter", function() {
            this.style.cursor = "pointer";
        });

        // Reset cursor when mouse leaves the row
        row.addEventListener("mouseleave", function() {
            this.style.cursor = "default";
        });
    });

    
});
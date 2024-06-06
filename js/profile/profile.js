document.addEventListener("DOMContentLoaded", function() {
    let stars = document.querySelectorAll(".star");
    let reviewForm = document.getElementById("review-form");
    let existingRating = parseInt(reviewForm.getAttribute("data-rating"));

    // Initialize the stars based on the existing rating
    updateStars(existingRating);

    stars.forEach(star => {
        star.addEventListener("click", function() {
            let index = this.getAttribute('data-value');
            updateStars(index);
        });
    });

    function updateStars(selectedIndex) {
        stars.forEach((star, index) => {
            star.classList.toggle('active', index < selectedIndex);
        });
        reviewForm.setAttribute("data-rating", selectedIndex);
    }
});

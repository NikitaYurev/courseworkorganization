document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll(".star");
    const clicked = false;

    stars.forEach(star => {
        star.addEventListener("click", function() {
            const index = this.getAttribute('data-value'); // Get the index of the clicked star
            updateStars(index); // Call the update function with the index of the clicked star
            clicked = true;
        });
    });

    stars.forEach(star => {
        star.addEventListener("mouseover", function() {
            const index = this.getAttribute('data-value'); // Get the index of the hovered star
            updateStars(index); // Call the update function with the index of the hovered star
        });
    });

    function updateStars(selectedIndex) {
        // Loop over each star, and toggle the 'orange' class as needed
        stars.forEach((star, index) => {
            if (index < selectedIndex) {
                star.classList.add('orange'); // Add orange class if the star's index is less than the selected index
            } else {
                star.classList.remove('orange'); // Remove orange class otherwise
            }
        });
        const ratingInput = document.getElementById("user-rating");
        ratingInput.value = selectedIndex; // Update the hidden input with the selected rating
    }
});
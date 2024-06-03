// document.addEventListener("DOMContentLoaded", function() {
//     const stars = document.querySelectorAll(".star");
//     const ratingInput = document.getElementById("user-rating");

//     stars.forEach(star => {
//         star.addEventListener("click", function(e) {
//             const selectedRating = e.target.getAttribute("data-value");
//             ratingInput.value = selectedRating;
//             highlightStars(selectedRating);
//         });
//     });

//     function highlightStars(selectedRating) {
//         stars.forEach(star => {
//             const starValue = star.getAttribute("data-value");
//             if (starValue <= selectedRating) {
//                 star.classList.add("orange");
//             } else {
//                 star.classList.remove("orange");
//             }
//         });
//     }
// });

document.addEventListener("DOMContentLoaded", function() {
    const stars = document.querySelectorAll(".star");
    const ratingInput = document.getElementById("user-rating");

    stars.forEach(star => {
        star.addEventListener("click", function() {
            const value = this.getAttribute('data-value');
            ratingInput.value = value; // Set the hidden input's value
            stars.forEach(s => {
                s.classList.remove('orange'); // Remove class from all stars
            });
            this.classList.add('orange'); // Add class to the clicked star
            this.previousElementSibling ? this.previousElementSibling.classList.add('orange') : null; // Add class to all previous stars
        });
    });
});

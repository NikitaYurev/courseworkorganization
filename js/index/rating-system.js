document.addEventListener("DOMContentLoaded", function() {
    let stars = document.querySelectorAll(".star");

    stars.forEach(star => {
        star.addEventListener("click", function() {
            let index = this.getAttribute('data-value');
            console.log("Star clicked, index: ", index); // Log index on click
            updateStars(index);
        });
    });

    stars.forEach(star => {
        star.addEventListener("mouseover", function() {
            let index = this.getAttribute('data-value');
            console.log("Star hovered, index: ", index); // Log index on hover
            updateStars(index);
        });
    });

    function updateStars(selectedIndex) {
        console.log("Updating stars up to index: ", selectedIndex); // Log the updating process
        stars.forEach((star, index) => {
            if (index < selectedIndex) {
                star.classList.add('orange');
            } else {
                star.classList.remove('orange');
            }
        });
        let ratingInput = document.getElementById("user-rating");
        ratingInput.value = selectedIndex;
        console.log("Rating set to: ", ratingInput.value); // Confirm the value set
    }
});

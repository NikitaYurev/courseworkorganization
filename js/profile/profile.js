document.addEventListener("DOMContentLoaded", function() {
    let stars = document.querySelectorAll(".star");

    // Function to update star ratings
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
        document.getElementById("review-form").setAttribute("data-rating", selectedIndex);
    }

    // Modal functionality
    var closeButton = document.querySelector('.close-button');
    var modal = document.getElementById('messageModal');

    closeButton.onclick = function() {
        modal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
});

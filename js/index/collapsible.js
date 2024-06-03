// document.addEventListener("DOMContentLoaded", function() {
//     const slides = document.querySelectorAll(".slide");
//     const textContainer = document.querySelector(".text-container");
//     let currentSlide = 0;

//     function showSlide(index) {
//         textContainer.style.transform = `translateX(-${index * 100}%)`;
//     }

//     document.getElementById("prev").addEventListener("click", function() {
//         currentSlide = (currentSlide > 0) ? currentSlide - 1 : slides.length - 1;
//         showSlide(currentSlide);
//     });

//     document.getElementById("next").addEventListener("click", function() {
//         currentSlide = (currentSlide < slides.length - 1) ? currentSlide + 1 : 0;
//         showSlide(currentSlide);
//     });

//     showSlide(currentSlide); // Initially show the first slide
// });

// document.addEventListener("DOMContentLoaded", function() {
//     const slides = document.querySelectorAll(".slide");
//     let currentSlide = 0;

//     function showSlide(index) {
//         slides.forEach((slide, i) => {
//             if (i === index) {
//                 slide.classList.add('active');
//                 slide.style.display = 'block';
//             } else {
//                 slide.classList.remove('active');
//                 slide.style.display = 'none';
//             }
//         });
//     }

//     document.getElementById("prev").addEventListener("click", function() {
//         currentSlide = (currentSlide > 0) ? currentSlide - 1 : slides.length - 1;
//         showSlide(currentSlide);
//     });

//     document.getElementById("next").addEventListener("click", function() {
//         currentSlide = (currentSlide < slides.length - 1) ? currentSlide + 1 : 0;
//         showSlide(currentSlide);
//     });

//     showSlide(currentSlide); // Initially show the first slide
// });

document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll(".slide");
    let currentSlide = 0;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
        });

        slides[index].classList.add('active');
    }

    document.getElementById("prev").addEventListener("click", function() {
        currentSlide = (currentSlide > 0) ? currentSlide - 1 : slides.length - 1;
        showSlide(currentSlide);
    });

    document.getElementById("next").addEventListener("click", function() {
        currentSlide = (currentSlide < slides.length - 1) ? currentSlide + 1 : 0;
        showSlide(currentSlide);
    });

    showSlide(currentSlide); // Initially show the first slide
});

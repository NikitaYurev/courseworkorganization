// document.addEventListener('DOMContentLoaded', function() {
//     const slides = document.querySelectorAll('.slide');
//     const textContainer = document.querySelector('.text-container');
//     let currentSlide = 0;

//     function showSlide(index) {
//         slides.forEach((slide, i) => {
//             if (i === index) {
//                 slide.classList.add('active');
//             } else {
//                 slide.classList.remove('active');
//             }
//         });

//         const offset = -index * 100;
//         textContainer.style.transition = 'transform 0.5s ease-in-out';
//         textContainer.style.transform = `translateX(${offset}%)`;
//     }

//     document.getElementById('next').addEventListener('click', () => {
//         currentSlide = (currentSlide + 1) % slides.length;
//         showSlide(currentSlide);
//     });

//     document.getElementById('prev').addEventListener('click', () => {
//         currentSlide = (currentSlide - 1 + slides.length) % slides.length;
//         showSlide(currentSlide);
//     });

//     // Initialize the first slide
//     showSlide(currentSlide);
// });



// // code which working but transition is not working totaly.
//     // function showSlide(index) {
//     //     slides.forEach(slide => slide.style.display = 'none');
//     //     slides[index].style.display = 'block';
//     // }
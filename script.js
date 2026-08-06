// Hero Section Carousel
let heroCurrentIndex = 0;
const heroSlides = document.querySelectorAll('.hero-slide');
const totalHeroSlides = heroSlides.length;
const heroIndicators = document.querySelectorAll('.indicator');

// Function to change slide based on direction (next/previous)
function changeHeroSlide(direction) {
    heroSlides[heroCurrentIndex].classList.remove('active');
    heroCurrentIndex = (heroCurrentIndex + direction + totalHeroSlides) % totalHeroSlides;
    heroSlides[heroCurrentIndex].classList.add('active');
    updateHeroIndicators();
}

// Function to go to a specific slide in Hero Section
function goToHeroSlide(index) {
    heroSlides[heroCurrentIndex].classList.remove('active');
    heroCurrentIndex = index;
    heroSlides[heroCurrentIndex].classList.add('active');
    updateHeroIndicators();
}

// Update the active indicator based on the current slide in Hero Section
function updateHeroIndicators() {
    heroIndicators.forEach((indicator, index) => {
        indicator.classList.remove('active');
        if (index === heroCurrentIndex) {
            indicator.classList.add('active');
        }
    });
}

// Testimonial Section Carousel
document.addEventListener('DOMContentLoaded', () => {
    let testimonialCurrentIndex = 0; // Using a separate variable for testimonial carousel
    const testimonialItems = document.querySelectorAll('.testimonial-item');
    
    // Function to show the current testimonial
    function showTestimonial() {
        // Remove 'active' class from all testimonials
        testimonialItems.forEach(item => item.classList.remove('active'));

        // Add 'active' class to the current testimonial
        testimonialItems[testimonialCurrentIndex].classList.add('active');

        // Update the index for the next testimonial
        testimonialCurrentIndex = (testimonialCurrentIndex + 1) % testimonialItems.length; // Loop back to the first item
    }

    // Set interval for the automatic carousel transition (every 5 seconds)
    setInterval(showTestimonial, 5000);  // Change the testimonial every 5 seconds

    // Show the first testimonial immediately
    showTestimonial();

    // Manual navigation (optional): Add event listeners for previous/next buttons in the testimonial section
    const testimonialPrevButton = document.querySelector('.testimonial-prev');
    const testimonialNextButton = document.querySelector('.testimonial-next');

    if (testimonialPrevButton) {
        testimonialPrevButton.addEventListener('click', () => {
            testimonialCurrentIndex = (testimonialCurrentIndex - 1 + testimonialItems.length) % testimonialItems.length;
            showTestimonial();
        });
    }

    if (testimonialNextButton) {
        testimonialNextButton.addEventListener('click', () => {
            testimonialCurrentIndex = (testimonialCurrentIndex + 1) % testimonialItems.length;
            showTestimonial();
        });
    }
});

// Event listeners for Hero Carousel Controls (optional: if you have prev/next buttons)
document.addEventListener('DOMContentLoaded', () => {
    const heroPrevButton = document.querySelector('.hero-prev');
    const heroNextButton = document.querySelector('.hero-next');
    
    if (heroPrevButton) {
        heroPrevButton.addEventListener('click', () => changeHeroSlide(-1));
    }
    
    if (heroNextButton) {
        heroNextButton.addEventListener('click', () => changeHeroSlide(1));
    }

    // Optional: Event listeners for hero slide indicators
    heroIndicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => goToHeroSlide(index));
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const benefitCards = document.querySelectorAll('.benefit-card');
    
    benefitCards.forEach(card => {
        card.addEventListener('click', () => {
            // Remove 'active' class from all cards
            benefitCards.forEach(c => c.classList.remove('active'));
            
            // Add 'active' class to clicked card
            card.classList.add('active');
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const stats = document.querySelectorAll('.stat-number');

    stats.forEach(stat => {
        const target = parseFloat(stat.getAttribute('data-count'));
        let current = 0;
        const increment = target / 100;

        function updateNumber() {
            if (current < target) {
                current += increment;
                stat.textContent = Math.round(current);
                requestAnimationFrame(updateNumber);
            } else {
                stat.textContent = target;
            }
        }

        // Start animation when stats come into view
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                updateNumber();
                observer.disconnect(); // Stop observer once animation starts
            }
        });

        observer.observe(stat);
    });
});

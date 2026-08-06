   AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Header scroll effect
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // Animated counter for stats
    function animateNumber(element, target) {
        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            if (target === 4.8) {
                element.textContent = current.toFixed(1);
            } else if (target >= 1000) {
                element.textContent = Math.floor(current).toLocaleString() + '+';
            } else {
                element.textContent = Math.floor(current) + '+';
            }
        }, 30);
    }

    // Start counters when element is visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numberElement = entry.target.querySelector('.stat-number');
                if (numberElement && !numberElement.classList.contains('counted')) {
                    const target = parseFloat(numberElement.textContent.replace(/[^0-9.]/g, ''));
                    animateNumber(numberElement, target);
                    numberElement.classList.add('counted');
                }
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat').forEach(stat => {
        observer.observe(stat);
    });
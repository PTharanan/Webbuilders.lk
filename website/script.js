/* ==================== MAIN APPLICATION ==================== */
document.addEventListener('DOMContentLoaded', function() {
    console.log('WEBbuilders.lk loaded successfully!');
    
    // Initialize all features
    initHeroAnimation();
    initTestimonialSlider();
    initMobileMenu();
    initScrollAnimations();
    initFormValidation();
});

/* ==================== HERO TEXT ANIMATION ==================== */
function initHeroAnimation() {
    const header = document.getElementById('header-text');
    if (!header) return;
    
    const content = header.innerHTML;
    header.innerHTML = '';
    const parts = content.split('<br>');
    
    parts.forEach((part, index) => {
        part.split('').forEach((char, i) => {
            const span = document.createElement('span');
            span.textContent = char === " " ? "\u00A0" : char;
            span.className = 'letter';
            span.style.animationDelay = (index * 0.5 + i * 0.05) + 's';
            header.appendChild(span);
        });
        
        if (index < parts.length - 1) {
            header.appendChild(document.createElement('br'));
        }
    });
    
    console.log('Hero animation initialized');
}

/* ==================== TESTIMONIAL SLIDER ==================== */
function initTestimonialSlider() {
    const allTSlides = document.querySelectorAll('.single-slide');
    if (allTSlides.length === 0) return;
    
    let currentTIndex = 0;
    let slideInterval;
    
    // Show specific slide
    function showTSlide(n) {
        // Remove active class from all slides
        allTSlides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.opacity = '0';
        });
        
        // Calculate new index with wrap-around
        currentTIndex = (n + allTSlides.length) % allTSlides.length;
        
        // Add active class to current slide
        allTSlides[currentTIndex].classList.add('active');
        setTimeout(() => {
            allTSlides[currentTIndex].style.opacity = '1';
        }, 10);
        
        console.log(`Showing slide ${currentTIndex + 1} of ${allTSlides.length}`);
    }
    
    // Move to next/previous slide
    window.moveT = function(direction) {
        showTSlide(currentTIndex + direction);
        resetAutoSlide(); // Reset timer on manual navigation
    };
    
    // Auto slide every 5 seconds
    function startAutoSlide() {
        slideInterval = setInterval(() => {
            moveT(1);
        }, 5000);
    }
    
    // Reset auto slide timer
    function resetAutoSlide() {
        clearInterval(slideInterval);
        startAutoSlide();
    }
    
    // Pause on hover
    const sliderEngine = document.querySelector('.slider-engine');
    if (sliderEngine) {
        sliderEngine.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
            console.log('Slider paused');
        });
        
        sliderEngine.addEventListener('mouseleave', () => {
            startAutoSlide();
            console.log('Slider resumed');
        });
    }
    
    // Initialize
    showTSlide(0);
    startAutoSlide();
    console.log('Testimonial slider initialized');
}

/* ==================== MOBILE MENU ==================== */
function initMobileMenu() {
    const openBtn = document.getElementById('openMenu');
    const closeBtn = document.getElementById('closeMenu');
    const overlay = document.getElementById('mobileOverlay');
    
    if (!openBtn || !overlay) {
        console.warn('Mobile menu elements not found');
        return;
    }
    
    // Open mobile menu
    openBtn.addEventListener('click', function() {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
        console.log('Mobile menu opened');
    });
    
    // Close mobile menu
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            closeMobileMenu();
        });
    }
    
    // Close menu when clicking on links
    const mobileLinks = document.querySelectorAll('.mobile-links a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            closeMobileMenu();
            console.log('Mobile menu closed via link click');
        });
    });
    
    // Close menu when clicking outside content
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeMobileMenu();
        }
    });
    
    // Close menu with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeMobileMenu();
            console.log('Mobile menu closed via ESC key');
        }
    });
    
    function closeMobileMenu() {
        overlay.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        console.log('Mobile menu closed');
    }
    
    console.log('Mobile menu initialized');
}

/* ==================== SCROLL ANIMATIONS ==================== */
function initScrollAnimations() {
    // Add scroll-triggered animations
    const animatedElements = document.querySelectorAll('.service-content, .service-card, .partner-card');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                console.log(`Element animated: ${entry.target.className}`);
            }
        });
    }, observerOptions);
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            const targetElement = document.querySelector(href);
            if (targetElement) {
                e.preventDefault();
                
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                console.log(`Smooth scroll to: ${href}`);
            }
        });
    });
    
    console.log('Scroll animations initialized');
}

/* ==================== FORM VALIDATION ==================== */
function initFormValidation() {
    // Domain check form
    const domainForm = document.querySelector('.exact-input-group');
    if (domainForm) {
        const domainInput = domainForm.querySelector('input[type="text"]');
        const domainButton = domainForm.querySelector('button');
        
        if (domainButton) {
            domainButton.addEventListener('click', function() {
                checkDomain(domainInput.value);
            });
            
            // Allow Enter key to submit
            domainInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    checkDomain(domainInput.value);
                }
            });
        }
    }
    
    function checkDomain(domainName) {
        if (!domainName || domainName.trim() === '') {
            alert('Please enter a domain name to check.');
            return;
        }
        
        // Show loading state
        const button = document.querySelector('.exact-input-group button');
        const originalText = button.textContent;
        button.textContent = 'Checking...';
        button.disabled = true;
        
    }
    
    console.log('Form validation initialized');
}

/* ==================== UTILITY FUNCTIONS ==================== */

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Check if element is in viewport
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Add CSS class for loaded state
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
    console.log('Page fully loaded');
});

// Handle window resize with debounce
window.addEventListener('resize', debounce(function() {
    console.log(`Window resized to: ${window.innerWidth}px x ${window.innerHeight}px`);
}, 250));

// Log page visibility changes
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        console.log('Page hidden');
    } else {
        console.log('Page visible');
    }
});

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.message, 'at', e.filename, ':', e.lineno);
});

/* ==================== PERFORMANCE OPTIMIZATION ==================== */

// Lazy load images
function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                    console.log('Lazy loaded image:', img.alt);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

// Initialize lazy loading on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLazyLoading);
} else {
    initLazyLoading();
}

console.log('WEBbuilders.lk JavaScript loaded successfully!');

    const openMenu = document.getElementById('openMenu');
    const closeMenu = document.getElementById('closeMenu');
    const mobileOverlay = document.getElementById('mobileOverlay');

    openMenu.addEventListener('click', () => {
        mobileOverlay.style.display = 'flex';
    });

    closeMenu.addEventListener('click', () => {
        mobileOverlay.style.display = 'none';
    });

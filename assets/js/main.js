/**
 * Thanchi Eco Resort - Main JavaScript
 *
 * @package Thanchi_Eco_Resort
 */

(function() {
    'use strict';

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (!menuToggle || !mobileMenu) {
            return;
        }

        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            const icon = this.querySelector('.material-symbols-outlined');

            this.setAttribute('aria-expanded', !isExpanded);
            mobileMenu.classList.toggle('hidden');

            // Toggle icon
            if (icon) {
                icon.textContent = isExpanded ? 'menu' : 'close';
            }

            // Prevent body scroll when menu is open
            document.body.style.overflow = !isExpanded ? 'hidden' : '';
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                menuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.classList.add('hidden');
                const icon = menuToggle.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = 'menu';
                document.body.style.overflow = '';
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                menuToggle.setAttribute('aria-expanded', 'false');
                mobileMenu.classList.add('hidden');
                const icon = menuToggle.querySelector('.material-symbols-outlined');
                if (icon) icon.textContent = 'menu';
                document.body.style.overflow = '';
                menuToggle.focus();
            }
        });
    }

    /**
     * Sticky Header Enhancement with Hero Detection
     */
    function initStickyHeader() {
        const header = document.querySelector('header');

        if (!header) {
            return;
        }

        // Find hero section (look for hero-gradient which indicates a hero with background image)
        const heroSection = document.querySelector('main > section:first-child');
        const hasHero = heroSection && heroSection.querySelector('.hero-gradient');

        // If no hero section, always use solid header
        if (!hasHero) {
            header.classList.remove('header-transparent');
            header.classList.add('header-solid');
            return;
        }

        // Get hero height for scroll threshold
        function getHeroThreshold() {
            return heroSection.offsetHeight - 100;
        }

        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const heroThreshold = getHeroThreshold();

            if (scrollTop > heroThreshold) {
                // Past hero - solid header with dark text
                header.classList.add('header-solid');
                header.classList.remove('header-transparent');
            } else {
                // On hero - transparent header with white text
                header.classList.remove('header-solid');
                header.classList.add('header-transparent');
            }
        }

        // Throttle scroll events
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Run on load
        handleScroll();
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');

                if (targetId === '#') {
                    return;
                }

                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();

                    const headerOffset = 100;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Set focus for accessibility
                    targetElement.setAttribute('tabindex', '-1');
                    targetElement.focus();
                }
            });
        });
    }

    /**
     * Form Validation
     */
    function initFormValidation() {
        const forms = document.querySelectorAll('form');

        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('ring-2', 'ring-red-500');
                        showFieldError(field, 'This field is required');
                    } else {
                        field.classList.remove('ring-2', 'ring-red-500');
                        clearFieldError(field);
                    }

                    // Email validation
                    if (field.type === 'email' && field.value) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(field.value)) {
                            isValid = false;
                            field.classList.add('ring-2', 'ring-red-500');
                            showFieldError(field, 'Please enter a valid email address');
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    const firstError = form.querySelector('.ring-red-500');
                    if (firstError) {
                        firstError.focus();
                    }
                }
            });

            // Clear errors on input
            form.querySelectorAll('input, textarea, select').forEach(field => {
                field.addEventListener('input', function() {
                    this.classList.remove('ring-2', 'ring-red-500');
                    clearFieldError(this);
                });
            });
        });
    }

    function showFieldError(field, message) {
        clearFieldError(field);

        const errorEl = document.createElement('span');
        errorEl.className = 'field-error text-red-500 text-xs mt-1 block';
        errorEl.textContent = message;
        errorEl.setAttribute('role', 'alert');

        field.parentNode.appendChild(errorEl);
    }

    function clearFieldError(field) {
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Lazy Load Images
     */
    function initLazyLoad() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;

                        if (image.dataset.src) {
                            image.src = image.dataset.src;
                            image.removeAttribute('data-src');
                        }

                        if (image.dataset.srcset) {
                            image.srcset = image.dataset.srcset;
                            image.removeAttribute('data-srcset');
                        }

                        image.classList.add('loaded');
                        observer.unobserve(image);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    /**
     * Animate Elements on Scroll
     */
    function initScrollAnimations() {
        if ('IntersectionObserver' in window) {
            const animateObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('opacity-100', 'translate-y-0');
                        entry.target.classList.remove('opacity-0', 'translate-y-4');
                    }
                });
            }, {
                rootMargin: '-50px 0px',
                threshold: 0.1
            });

            // Add initial state to elements
            document.querySelectorAll('[data-animate]').forEach(el => {
                el.classList.add('opacity-0', 'translate-y-4', 'transition-all', 'duration-700');
                animateObserver.observe(el);
            });
        }
    }

    /**
     * Horizontal Scroll with Mouse Wheel
     */
    function initHorizontalScroll() {
        const scrollContainers = document.querySelectorAll('.custom-scrollbar');

        scrollContainers.forEach(container => {
            container.addEventListener('wheel', function(e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    container.scrollLeft += e.deltaY;
                }
            }, { passive: false });
        });
    }

    /**
     * Date Picker Enhancement
     */
    function initDatePickers() {
        const checkinInputs = document.querySelectorAll('input[name="checkin"]');
        const checkoutInputs = document.querySelectorAll('input[name="checkout"]');

        checkinInputs.forEach((checkin, index) => {
            const checkout = checkoutInputs[index];

            if (checkin && checkout) {
                checkin.addEventListener('change', function() {
                    const checkinDate = new Date(this.value);
                    checkinDate.setDate(checkinDate.getDate() + 1);

                    const minCheckout = checkinDate.toISOString().split('T')[0];
                    checkout.min = minCheckout;

                    if (checkout.value && new Date(checkout.value) <= new Date(this.value)) {
                        checkout.value = minCheckout;
                    }
                });
            }
        });
    }

    /**
     * Accessibility: Focus Management
     */
    function initAccessibility() {
        // Add focus visible class for keyboard navigation
        document.body.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-navigation');
            }
        });

        document.body.addEventListener('mousedown', function() {
            document.body.classList.remove('keyboard-navigation');
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const button = document.createElement('button');
        button.className = 'back-to-top fixed bottom-8 right-8 w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center opacity-0 invisible transition-all duration-300 z-50 hover:bg-[#855935] shadow-lg';
        button.innerHTML = '<span class="material-symbols-outlined">expand_less</span>';
        button.setAttribute('aria-label', 'Back to top');

        document.body.appendChild(button);

        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 500) {
                button.classList.remove('opacity-0', 'invisible');
                button.classList.add('opacity-100', 'visible');
            } else {
                button.classList.add('opacity-0', 'invisible');
                button.classList.remove('opacity-100', 'visible');
            }
        });

        button.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Initialize all functions on DOM ready
     */
    function init() {
        initMobileMenu();
        initStickyHeader();
        initSmoothScroll();
        initFormValidation();
        initLazyLoad();
        initScrollAnimations();
        initHorizontalScroll();
        initDatePickers();
        initAccessibility();
        initBackToTop();
    }

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();

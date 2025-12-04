/**
 * Central CMI - Main JavaScript File
 * Handles common functionality across all pages
 */

// Global variables
let mobileMenuOpen = false;
let userDropdownOpen = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
    initializeMobileMenu();
    initializeUserDropdown();
    initializeBackToTop();
    initializeFormEnhancements();
    initializeTooltips();
    initializeNotifications();
    initializeProgressBars();
    initializeSearchFunctionality();
}

/**
 * Mobile Menu Functionality
 */
function initializeMobileMenu() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        
        // Close menu when clicking overlay
        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (mobileMenuOpen && !mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                closeMobileMenu();
            }
        });
        
        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenuOpen) {
                closeMobileMenu();
            }
        });
    }
}

function toggleMobileMenu() {
    if (mobileMenuOpen) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

function openMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    
    if (mobileMenu) {
        mobileMenu.classList.add('active');
        document.body.classList.add('mobile-menu-open');
        mobileMenuOpen = true;
        
        if (mobileMenuOverlay) {
            mobileMenuOverlay.classList.add('active');
        }
    }
}

function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.querySelector('.mobile-menu-overlay');
    
    if (mobileMenu) {
        mobileMenu.classList.remove('active');
        document.body.classList.remove('mobile-menu-open');
        mobileMenuOpen = false;
        
        if (mobileMenuOverlay) {
            mobileMenuOverlay.classList.remove('active');
        }
    }
}

/**
 * User Dropdown Functionality
 */
function initializeUserDropdown() {
    const userDropdownBtn = document.getElementById('user-dropdown-btn');
    const userDropdown = document.getElementById('user-dropdown');
    
    if (userDropdownBtn && userDropdown) {
        userDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleUserDropdown();
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (userDropdownOpen && !userDropdown.contains(e.target)) {
                closeUserDropdown();
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userDropdownOpen) {
                closeUserDropdown();
            }
        });
    }
}

function toggleUserDropdown() {
    if (userDropdownOpen) {
        closeUserDropdown();
    } else {
        openUserDropdown();
    }
}

function openUserDropdown() {
    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
        userDropdown.classList.add('active');
        userDropdownOpen = true;
    }
}

function closeUserDropdown() {
    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
        userDropdown.classList.remove('active');
        userDropdownOpen = false;
    }
}

/**
 * Back to Top Button
 */
function initializeBackToTop() {
    const backToTopBtn = document.getElementById('back-to-top');
    
    if (backToTopBtn) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        
        // Smooth scroll to top
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

/**
 * Form Enhancements
 */
function initializeFormEnhancements() {
    // Add floating labels
    const formInputs = document.querySelectorAll('.form-control');
    formInputs.forEach(function(input) {
        // Add focus/blur events for floating labels
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(function(field) {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    field.parentElement.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const existingError = field.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Tooltips
 */
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(function(element) {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const element = e.target;
    const tooltipText = element.getAttribute('data-tooltip');
    
    if (tooltipText) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = tooltipText;
        tooltip.id = 'active-tooltip';
        
        document.body.appendChild(tooltip);
        
        // Position tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
        
        // Show tooltip
        setTimeout(() => tooltip.classList.add('visible'), 10);
    }
}

function hideTooltip() {
    const tooltip = document.getElementById('active-tooltip');
    if (tooltip) {
        tooltip.classList.remove('visible');
        setTimeout(() => tooltip.remove(), 200);
    }
}

/**
 * Notifications
 */
function initializeNotifications() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (!alert.classList.contains('alert-permanent')) {
            setTimeout(function() {
                hideAlert(alert);
            }, 5000);
        }
        
        // Add close button if not present
        if (!alert.querySelector('.alert-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'alert-close';
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.addEventListener('click', function() {
                hideAlert(alert);
            });
            alert.appendChild(closeBtn);
        }
    });
}

function hideAlert(alert) {
    alert.classList.add('hiding');
    setTimeout(function() {
        alert.remove();
    }, 300);
}

function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification`;
    notification.innerHTML = `
        <i class="fas fa-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button class="alert-close"><i class="fas fa-times"></i></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('visible'), 10);
    
    // Auto-hide
    if (duration > 0) {
        setTimeout(() => hideAlert(notification), duration);
    }
    
    // Close button
    notification.querySelector('.alert-close').addEventListener('click', function() {
        hideAlert(notification);
    });
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Progress Bars Animation
 */
function initializeProgressBars() {
    const progressBars = document.querySelectorAll('.progress-fill');
    
    // Animate progress bars when they come into view
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const progressBar = entry.target;
                const width = progressBar.style.width;
                progressBar.style.width = '0%';
                setTimeout(function() {
                    progressBar.style.width = width;
                }, 100);
            }
        });
    });
    
    progressBars.forEach(function(bar) {
        observer.observe(bar);
    });
}

/**
 * Search Functionality
 */
function initializeSearchFunctionality() {
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(function(input) {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                performSearch(input.value, input);
            }, 300);
        });
    });
}

function performSearch(query, inputElement) {
    // This would typically make an AJAX request to search
    // For now, we'll just show a simple implementation
    const searchResults = inputElement.parentElement.querySelector('.search-results');
    
    if (searchResults) {
        if (query.length > 2) {
            // Show loading state
            searchResults.innerHTML = '<div class="search-loading">Searching...</div>';
            searchResults.classList.add('visible');
            
            // Simulate search delay
            setTimeout(function() {
                // In a real implementation, this would be actual search results
                searchResults.innerHTML = `
                    <div class="search-result">
                        <i class="fas fa-search"></i>
                        <span>Search for "${query}"</span>
                    </div>
                `;
            }, 500);
        } else {
            searchResults.classList.remove('visible');
        }
    }
}

/**
 * Utility Functions
 */

// Debounce function
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

// Throttle function
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// Format date
function formatDate(date, format = 'MMM DD, YYYY') {
    const options = {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    };
    return new Date(date).toLocaleDateString('en-US', options);
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Copy to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('Copied to clipboard!', 'success', 2000);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Copied to clipboard!', 'success', 2000);
    }
}

// Smooth scroll to element
function scrollToElement(elementId, offset = 0) {
    const element = document.getElementById(elementId);
    if (element) {
        const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

// Export functions for global use
window.Central CMI = {
    showNotification,
    hideAlert,
    copyToClipboard,
    scrollToElement,
    formatDate,
    formatNumber,
    openMobileMenu,
    closeMobileMenu,
    openUserDropdown,
    closeUserDropdown
};

// Handle page-specific functionality
if (document.body.classList.contains('dashboard-page')) {
    // Dashboard-specific JavaScript
    initializeDashboard();
}

function initializeDashboard() {
    // Add any dashboard-specific functionality here
    console.log('Dashboard initialized');
}

// Handle responsive behavior
window.addEventListener('resize', debounce(function() {
    // Close mobile menu on resize to desktop
    if (window.innerWidth > 768 && mobileMenuOpen) {
        closeMobileMenu();
    }
    
    // Close user dropdown on resize
    if (userDropdownOpen) {
        closeUserDropdown();
    }
}, 250));

// Handle print styles
window.addEventListener('beforeprint', function() {
    // Close any open menus before printing
    closeMobileMenu();
    closeUserDropdown();
});

// Performance monitoring (optional)
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(function() {
            const perfData = performance.timing;
            const loadTime = perfData.loadEventEnd - perfData.navigationStart;
            console.log('Page load time:', loadTime + 'ms');
        }, 0);
    });
}
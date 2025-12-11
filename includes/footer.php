<?php 
// Use dynamic base URL from app config
if (!isset($base_url)) { 
    $base_url = defined('BASE_URL') ? BASE_URL : '/'; 
} 
?>
<footer class="bg-surface border-t border-secondary-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 py-12">
            <!-- Company Info -->
            <div class="lg:col-span-1">
                <div class="flex items-center mb-4">
                    <svg class="h-8 w-8 text-primary mr-3" viewBox="0 0 32 32" fill="currentColor">
                        <path d="M16 2L3 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-13-5z"/>
                        <path d="M14 16l-2-2-1.41 1.41L14 18.83l6-6L18.59 11.41 14 16z" fill="white"/>
                    </svg>
                    <span class="text-xl font-semibold text-text-primary">Central CMI</span>
                </div>
                <p class="text-text-secondary text-sm mb-6 leading-relaxed">
                    WESMAARRDEC Activity Management System designed to streamline workflows and enhance collaboration across government agencies.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro p-2 rounded-md hover:bg-secondary-100" aria-label="Facebook">
                        <i class="fab fa-facebook-f text-lg"></i>
                    </a>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro p-2 rounded-md hover:bg-secondary-100" aria-label="Twitter">
                        <i class="fab fa-twitter text-lg"></i>
                    </a>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro p-2 rounded-md hover:bg-secondary-100" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in text-lg"></i>
                    </a>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro p-2 rounded-md hover:bg-secondary-100" aria-label="Email">
                        <i class="fas fa-envelope text-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-3">
                    <?php
                    // Get role-based links (same logic as navbar)
                    $canRep = function_exists('is_representative') ? is_representative() : true;
                    $canSec = function_exists('is_secretariat') ? is_secretariat() : false;
                    $roleDir = $canSec ? 'is_secretariat' : 'is_representative';
                    $activityHref = $base_url . 'pages/' . $roleDir . '/activity_management.php';
                    $calendarHref = $base_url . 'pages/' . $roleDir . '/calendar_management.php';
                    $reportsHref = $base_url . 'pages/is_secretariat/report_generation.php';
                    ?>
                    <li><a href="<?php echo $base_url; ?>index.php" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-home mr-2 w-4"></i> Dashboard
                    </a></li>
                    <li><a href="<?php echo $activityHref; ?>" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-tasks mr-2 w-4"></i> Activities
                    </a></li>
                    <li><a href="<?php echo $calendarHref; ?>" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-calendar-alt mr-2 w-4"></i> Calendar
                    </a></li>
                    <?php if ($canSec): ?>
                    <li><a href="<?php echo $reportsHref; ?>" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-chart-bar mr-2 w-4"></i> Reports
                    </a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Support</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-question-circle mr-2 w-4"></i> Help Center
                    </a></li>
                    <li><a href="#" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-book mr-2 w-4"></i> Documentation
                    </a></li>
                    <li><a href="#" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-headset mr-2 w-4"></i> Contact Support
                    </a></li>
                    <li><a href="#" class="text-text-secondary hover:text-primary transition-micro text-sm flex items-center">
                        <i class="fas fa-bug mr-2 w-4"></i> Report Issue
                    </a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Contact Information</h4>
                <div class="space-y-3">
                    <div class="flex items-start text-sm">
                        <i class="fas fa-map-marker-alt text-text-secondary mr-3 mt-0.5 w-4"></i>
                        <span class="text-text-secondary">WESMAARRDEC Complex, Zamboanga City, Philippines</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-phone text-text-secondary mr-3 w-4"></i>
                        <span class="text-text-secondary">+63 (62) 123-4567</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-envelope text-text-secondary mr-3 w-4"></i>
                        <span class="text-text-secondary">support@wesmaarrdec.gov.ph</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-clock text-text-secondary mr-3 w-4"></i>
                        <span class="text-text-secondary">Mon - Fri: 8:00 AM - 5:00 PM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="border-t border-secondary-200 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-4 md:mb-0">
                    <p class="text-text-secondary text-sm">&copy; <?php echo date('Y'); ?> Central CMI - WESMAARRDEC. All rights reserved.</p>
                    <p class="text-text-secondary text-xs mt-1">Version 1.0.0</p>
                </div>
                <div class="flex flex-wrap justify-center md:justify-end items-center space-x-1 text-sm">
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro px-2 py-1 rounded">Privacy Policy</a>
                    <span class="text-secondary-300">•</span>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro px-2 py-1 rounded">Terms of Service</a>
                    <span class="text-secondary-300">•</span>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro px-2 py-1 rounded">Security</a>
                    <span class="text-secondary-300">•</span>
                    <a href="#" class="text-text-secondary hover:text-primary transition-micro px-2 py-1 rounded">Accessibility</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-6 right-6 bg-primary text-white p-3 rounded-full shadow-lg hover:bg-primary-700 transition-all duration-300 opacity-0 invisible translate-y-4" aria-label="Back to top">
    <i class="fas fa-chevron-up text-sm"></i>
</button>

<!-- JavaScript for Footer Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Back to top button functionality
    const backToTopButton = document.getElementById('back-to-top');
    
    if (backToTopButton) {
        // Show/hide button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        // Smooth scroll to top
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});
</script>

<!-- Additional JavaScript if needed -->
<?php if(isset($additional_js)): ?>
    <?php foreach($additional_js as $js): ?>
        <script src="<?php echo $base_url . $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Common JavaScript includes -->
<script src="<?php echo $base_url; ?>assets/js/script.js"></script>

</body>
</html>
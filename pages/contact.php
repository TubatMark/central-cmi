<?php
require_once __DIR__ . '/../database/auth.php';
require_role(['representative', 'secretariat']);
?>
<?php
$pageTitle = "Contact Us - Central CMI";
$bodyClass = "bg-background";
include '../includes/header.php';
include '../includes/navbar.php';

// Handle form submission
$form_submitted = false;
$form_errors = [];
$form_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_submitted = true;
    
    // Validate form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $inquiry_type = $_POST['inquiry_type'] ?? '';
    
    // Basic validation
    if (empty($name)) {
        $form_errors[] = 'Name is required.';
    }
    
    if (empty($email)) {
        $form_errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($subject)) {
        $form_errors[] = 'Subject is required.';
    }
    
    if (empty($message)) {
        $form_errors[] = 'Message is required.';
    }
    
    if (empty($inquiry_type)) {
        $form_errors[] = 'Please select an inquiry type.';
    }
    
    // If no errors, process the form
    if (empty($form_errors)) {
        $form_success = true;
    }
}

?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <section class="mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-text-primary mb-4">
                    <i class="fas fa-envelope text-primary mr-3"></i>
                    Contact Us
                </h1>
                <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                    Get in touch with our support team. We're here to help you with Central CMI.
                </p>
            </div>
        </section>

        <!-- Contact Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Contact Information -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-text-primary mb-6 flex items-center">
                        <i class="fas fa-info-circle text-primary mr-3"></i>
                        Contact Information
                    </h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-text-primary mb-1">Office Address</h4>
                                <p class="text-text-secondary text-sm leading-relaxed">
                                    Government Center<br>Manila, Philippines 1000
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-text-primary mb-1">Phone Number</h4>
                                <p class="text-text-secondary text-sm">
                                    +63 (2) 8888-0000<br>+63 (2) 8888-0001
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-text-primary mb-1">Email Address</h4>
                                <p class="text-text-secondary text-sm">
                                    support@centralcmi.gov.ph<br>info@centralcmi.gov.ph
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-medium text-text-primary mb-1">Office Hours</h4>
                                <p class="text-text-secondary text-sm">
                                    Monday - Friday<br>8:00 AM - 5:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h4 class="text-lg font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-external-link-alt text-primary mr-3"></i>
                        Quick Links
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="<?php echo $base_url; ?>pages/user_guide.php" class="flex flex-col items-center p-3 bg-gray-50 hover:bg-primary/5 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-book text-primary mb-2"></i>
                            <span class="text-sm text-text-secondary">User Guide</span>
                        </a>
                        <a href="<?php echo $base_url; ?>pages/faq.php" class="flex flex-col items-center p-3 bg-gray-50 hover:bg-primary/5 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-question-circle text-primary mb-2"></i>
                            <span class="text-sm text-text-secondary">FAQ</span>
                        </a>
                        <a href="<?php echo $base_url; ?>pages/system_status.php" class="flex flex-col items-center p-3 bg-gray-50 hover:bg-primary/5 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-server text-primary mb-2"></i>
                            <span class="text-sm text-text-secondary">System Status</span>
                        </a>
                        <a href="<?php echo $base_url; ?>pages/downloads.php" class="flex flex-col items-center p-3 bg-gray-50 hover:bg-primary/5 rounded-lg transition-colors duration-200 text-center">
                            <i class="fas fa-download text-primary mb-2"></i>
                            <span class="text-sm text-text-secondary">Downloads</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-text-primary mb-2 flex items-center">
                        <i class="fas fa-paper-plane text-primary mr-3"></i>
                        Send us a Message
                    </h2>
                    <p class="text-text-secondary">
                        Fill out the form below and we'll get back to you as soon as possible.
                    </p>
                </div>
                
                <?php if ($form_success): ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-start space-x-3">
                        <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-green-800 mb-1">Message Sent Successfully!</h4>
                            <p class="text-green-700 text-sm">Thank you for contacting us. We'll get back to you within 24 hours.</p>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($form_errors)): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                        <div>
                            <h4 class="font-medium text-red-800 mb-2">Please correct the following errors:</h4>
                            <ul class="text-red-700 text-sm space-y-1">
                                <?php foreach ($form_errors as $error): ?>
                                    <li class="flex items-center"><i class="fas fa-circle text-xs mr-2"></i><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-text-primary mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                                   placeholder="Enter your full name" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-text-primary mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                   placeholder="Enter your email address" required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="inquiry_type" class="block text-sm font-medium text-text-primary mb-2">
                                Inquiry Type <span class="text-red-500">*</span>
                            </label>
                            <select id="inquiry_type" name="inquiry_type" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200" required>
                                <option value="">Select inquiry type</option>
                                <option value="technical_support" <?php echo ($_POST['inquiry_type'] ?? '') === 'technical_support' ? 'selected' : ''; ?>>Technical Support</option>
                                <option value="account_issues" <?php echo ($_POST['inquiry_type'] ?? '') === 'account_issues' ? 'selected' : ''; ?>>Account Issues</option>
                                <option value="feature_request" <?php echo ($_POST['inquiry_type'] ?? '') === 'feature_request' ? 'selected' : ''; ?>>Feature Request</option>
                                <option value="bug_report" <?php echo ($_POST['inquiry_type'] ?? '') === 'bug_report' ? 'selected' : ''; ?>>Bug Report</option>
                                <option value="training" <?php echo ($_POST['inquiry_type'] ?? '') === 'training' ? 'selected' : ''; ?>>Training & Documentation</option>
                                <option value="general" <?php echo ($_POST['inquiry_type'] ?? '') === 'general' ? 'selected' : ''; ?>>General Inquiry</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="subject" class="block text-sm font-medium text-text-primary mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200" 
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>" 
                                   placeholder="Brief description of your inquiry" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-text-primary mb-2">
                            Message <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="6" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200 resize-vertical" 
                                  placeholder="Please provide detailed information about your inquiry..." required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                        <button type="reset" class="flex-1 bg-gray-100 hover:bg-gray-200 text-text-secondary font-medium py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Form
                        </button>
                    </div>
                </form>
            </div>
            </div>
            
        </div>
        
        <!-- Additional Support Options -->
        <div class="mt-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-text-primary flex items-center">
                        <i class="fas fa-life-ring text-primary mr-3"></i>
                        Additional Support Options
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="flex flex-col items-center text-center p-4 bg-gray-50 hover:bg-primary/5 rounded-lg transition-colors duration-200">
                        <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-headset text-white"></i>
                        </div>
                        <h4 class="font-medium text-text-primary mb-2">Live Chat Support</h4>
                        <p class="text-sm text-text-secondary mb-3">Get instant help from our support team during office hours.</p>
                        <button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200" onclick="openLiveChat()">
                            Start Chat
                        </button>
                    </div>
                    
                    <div class="flex flex-col items-center text-center p-4 bg-gray-50 hover:bg-green-50 rounded-lg transition-colors duration-200">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                        <h4 class="font-medium text-text-primary mb-2">Schedule a Call</h4>
                        <p class="text-sm text-text-secondary mb-3">Book a one-on-one session with our technical experts.</p>
                        <a href="<?php echo $base_url; ?>pages/schedule_call.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Schedule Now
                        </a>
                    </div>
                    
                    <div class="flex flex-col items-center text-center p-4 bg-gray-50 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-video text-white"></i>
                        </div>
                        <h4 class="font-medium text-text-primary mb-2">Video Tutorials</h4>
                        <p class="text-sm text-text-secondary mb-3">Watch step-by-step guides and training videos.</p>
                        <a href="<?php echo $base_url; ?>pages/tutorials.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            Watch Videos
                        </a>
                    </div>
                    
                    <div class="flex flex-col items-center text-center p-4 bg-gray-50 hover:bg-yellow-50 rounded-lg transition-colors duration-200">
                        <div class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center mb-3">
                            <i class="fas fa-ticket-alt text-white"></i>
                        </div>
                        <h4 class="font-medium text-text-primary mb-2">Support Tickets</h4>
                        <p class="text-sm text-text-secondary mb-3">Track your support requests and view ticket history.</p>
                        <a href="<?php echo $base_url; ?>pages/support_tickets.php" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200">
                            View Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Include Footer -->
    <?php include '../includes/footer.php'; ?>

<script>
// Live chat functionality
function openLiveChat() {
    // In a real implementation, this would open a live chat widget
    alert('Live chat feature will be available soon. Please use the contact form for now.');
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    // Add real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-500')) {
                validateField(this);
            }
        });
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showAlert('Please correct the errors in the form before submitting.', 'error');
        }
    });
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Remove existing error styling
        field.classList.remove('border-red-500');
        field.classList.add('border-gray-300');
        const existingError = field.parentNode.querySelector('.text-red-500');
        if (existingError) {
            existingError.remove();
        }
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required.';
        }
        
        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
        }
        
        // Message length validation
        if (field.name === 'message' && value && value.length < 10) {
            isValid = false;
            errorMessage = 'Message must be at least 10 characters long.';
        }
        
        if (!isValid) {
            field.classList.remove('border-gray-300');
            field.classList.add('border-red-500');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'text-red-500 text-sm mt-1';
            errorDiv.textContent = errorMessage;
            field.parentNode.appendChild(errorDiv);
        }
        
        return isValid;
    }
    
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `bg-red-50 border border-red-200 rounded-lg p-4 mb-6 flex items-start space-x-3`;
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
            <div>
                <p class="text-red-700 text-sm">${message}</p>
            </div>
        `;
        
        const form = document.querySelector('form');
        form.insertBefore(alertDiv, form.firstChild);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>

<style>
/* Contact page specific styles */
.contact-content {
    margin-bottom: var(--spacing-8);
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: var(--spacing-8);
    margin-bottom: var(--spacing-8);
}

.contact-details {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-6);
    margin-bottom: var(--spacing-6);
}

.contact-detail {
    display: flex;
    gap: var(--spacing-4);
    padding: var(--spacing-4);
    background-color: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.detail-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-xl);
    color: var(--white);
    flex-shrink: 0;
}

.detail-content h4 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin: 0 0 var(--spacing-1) 0;
    color: var(--gray-900);
}

.detail-content p {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin: 0;
    line-height: 1.5;
}

.quick-links {
    padding-top: var(--spacing-6);
    border-top: 1px solid var(--gray-200);
}

.quick-links h4 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin: 0 0 var(--spacing-4) 0;
    color: var(--gray-900);
}

.links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: var(--spacing-3);
}

.quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: var(--spacing-3);
    background-color: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    text-decoration: none;
    color: var(--gray-700);
    font-size: var(--font-size-sm);
    font-weight: 500;
    transition: all var(--transition-fast);
}

.quick-link:hover {
    border-color: var(--primary-green);
    color: var(--primary-green);
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

.quick-link i {
    font-size: var(--font-size-lg);
    margin-bottom: var(--spacing-2);
}

.contact-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--spacing-4);
}

.form-actions {
    display: flex;
    gap: var(--spacing-3);
    justify-content: flex-start;
    margin-top: var(--spacing-2);
}

.error-list {
    margin: var(--spacing-2) 0 0 0;
    padding-left: var(--spacing-4);
}

.error-list li {
    margin-bottom: var(--spacing-1);
}

.support-options {
    margin-top: var(--spacing-8);
}

.support-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-6);
}

.support-option {
    display: flex;
    gap: var(--spacing-4);
    padding: var(--spacing-6);
    background-color: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    transition: all var(--transition-fast);
}

.support-option:hover {
    background-color: var(--primary-green-50);
    border-color: var(--primary-green-200);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.support-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-2xl);
    color: var(--white);
    flex-shrink: 0;
}

.support-content h4 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin: 0 0 var(--spacing-2) 0;
    color: var(--gray-900);
}

.support-content p {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin: 0 0 var(--spacing-3) 0;
    line-height: 1.5;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-6);
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: var(--spacing-3);
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .links-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: var(--spacing-2);
    }
    
    .support-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-4);
    }
    
    .support-option {
        flex-direction: column;
        text-align: center;
        padding: var(--spacing-4);
    }
}
</style>
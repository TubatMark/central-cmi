<?php
require_once __DIR__ . '/../database/auth.php';
require_role(['representative', 'secretariat']);
?>
<?php
$pageTitle = "About Central CMI";
$bodyClass = "bg-background";
include '../includes/header.php';
include '../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <section class="text-center mb-12">
            <h1 class="text-4xl font-bold text-text-primary mb-4">
                <i class="fas fa-info-circle text-primary mr-3"></i>
                About Central CMI
            </h1>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto leading-relaxed">
                WESMAARRDEC Activity Management System empowering government agencies with efficient activity management and tracking solutions.
            </p>
        </section>

        <!-- About Content -->
        <div class="space-y-12">
            <!-- About WESMAARRDEC Section -->
            <section>
                <div class="bg-primary-50 rounded-xl p-8 border border-primary-200">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-university text-primary mr-3"></i>
                            About WESMAARRDEC
                        </h2>
                        <p class="text-lg text-text-secondary max-w-4xl mx-auto leading-relaxed">
                            The Western Mindanao Agriculture, Aquatic and Natural Resources Research and Development Consortium (WESMAARRDEC) is a regional research and development consortium dedicated to advancing agriculture, aquatic, and natural resources research in the Western Mindanao region of the Philippines.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-seedling text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Agriculture</h3>
                            <p class="text-sm text-text-secondary">Research and development in agricultural technologies and practices</p>
                        </div>
                        
                        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-fish text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Aquatic Resources</h3>
                            <p class="text-sm text-text-secondary">Marine and freshwater resource management and conservation</p>
                        </div>
                        
                        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-tree text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Natural Resources</h3>
                            <p class="text-sm text-text-secondary">Sustainable management of forest and natural resources</p>
                        </div>
                        
                        <div class="text-center p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-microscope text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Research & Development</h3>
                            <p class="text-sm text-text-secondary">Innovation and technology transfer for regional development</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Mission Section -->
            <section>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-bullseye text-primary mr-3"></i>
                            Our Mission
                        </h2>
                        <p class="text-lg text-text-secondary max-w-4xl mx-auto leading-relaxed mb-4">
                            Central CMI is WESMAARRDEC's comprehensive activity management system designed to streamline government operations by providing a robust platform for activity management, progress tracking, and collaborative governance across the Western Mindanao region.
                        </p>
                        <p class="text-text-secondary max-w-4xl mx-auto leading-relaxed">
                            We believe in transparency, efficiency, and accountability in public service. Our platform enables WESMAARRDEC and its member agencies to better serve their constituents through improved project management and real-time progress monitoring in agriculture, aquatic, and natural resources research and development.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="text-center p-6 bg-primary/5 rounded-lg hover:bg-primary/10 transition-colors duration-200">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Efficiency</h3>
                            <p class="text-sm text-text-secondary">Streamline workflows and reduce administrative overhead</p>
                        </div>
                        
                        <div class="text-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-eye text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Transparency</h3>
                            <p class="text-sm text-text-secondary">Provide clear visibility into project progress and outcomes</p>
                        </div>
                        
                        <div class="text-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-users text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Collaboration</h3>
                            <p class="text-sm text-text-secondary">Foster teamwork and communication across departments</p>
                        </div>
                        
                        <div class="text-center p-6 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors duration-200">
                            <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shield-alt text-white text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-text-primary mb-2">Accountability</h3>
                            <p class="text-sm text-text-secondary">Ensure responsible governance and measurable results</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-star text-primary mr-3"></i>
                            Key Features
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tasks text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">Activity Management</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Create, assign, and track activities with detailed progress monitoring and milestone tracking.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-chart-bar text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">Comprehensive Reporting</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Generate detailed reports with analytics and insights for informed decision-making.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">Calendar Integration</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Seamlessly manage deadlines, meetings, and important dates with our integrated calendar system.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bell text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">Smart Notifications</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Stay informed with intelligent alerts for deadlines, updates, and important announcements.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-users-cog text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">User Management</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Flexible role-based access control with customizable permissions for different user types.</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-4 p-6 bg-gray-50 rounded-lg border border-gray-200 hover:bg-primary-50 hover:border-primary-200 transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-mobile-alt text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-text-primary mb-2">Mobile Responsive</h3>
                                <p class="text-sm text-text-secondary leading-relaxed">Access your dashboard and manage activities from any device with our responsive design.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technology Section -->
            <section>
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-cogs text-primary mr-3"></i>
                            Technology Stack
                        </h2>
                        <p class="text-lg text-text-secondary max-w-4xl mx-auto leading-relaxed">
                            Central CMI is built using modern web technologies to ensure reliability, security, and performance for WESMAARRDEC's research and development activities.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="text-center p-6 bg-purple-50 rounded-lg border border-purple-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-php text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">PHP</h3>
                            <p class="text-sm text-text-secondary">Server-side scripting for robust backend functionality</p>
                        </div>
                        
                        <div class="text-center p-6 bg-blue-50 rounded-lg border border-blue-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-database text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">MySQL</h3>
                            <p class="text-sm text-text-secondary">Reliable database management for data integrity</p>
                        </div>
                        
                        <div class="text-center p-6 bg-orange-50 rounded-lg border border-orange-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-html5 text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">HTML5</h3>
                            <p class="text-sm text-text-secondary">Modern markup for semantic and accessible content</p>
                        </div>
                        
                        <div class="text-center p-6 bg-cyan-50 rounded-lg border border-cyan-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-css3-alt text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">CSS3</h3>
                            <p class="text-sm text-text-secondary">Advanced styling with responsive design principles</p>
                        </div>
                        
                        <div class="text-center p-6 bg-yellow-50 rounded-lg border border-yellow-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fab fa-js-square text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">JavaScript</h3>
                            <p class="text-sm text-text-secondary">Interactive user experience and dynamic functionality</p>
                        </div>
                        
                        <div class="text-center p-6 bg-green-50 rounded-lg border border-green-200 hover:shadow-lg transition-all duration-200 hover:-translate-y-1">
                            <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-shield-alt text-white text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-text-primary mb-2">Security</h3>
                            <p class="text-sm text-text-secondary">Enterprise-grade security measures and data protection</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section>
                <div class="bg-primary-50 rounded-xl p-8 border border-primary-200">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-text-primary mb-4 flex items-center justify-center">
                            <i class="fas fa-envelope text-primary mr-3"></i>
                            Get in Touch
                        </h2>
                        <p class="text-lg text-text-secondary max-w-3xl mx-auto leading-relaxed">
                            Have questions about Central CMI? We're here to help you optimize your WESMAARRDEC research and development operations.
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="flex flex-col items-center gap-4 p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-phone text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <h4 class="font-semibold text-text-primary mb-1">Phone Support</h4>
                                <p class="text-text-primary font-medium mb-1">+63 (62) 123-4567</p>
                                <small class="text-text-secondary text-sm">Monday - Friday, 8:00 AM - 5:00 PM</small>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-center gap-4 p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-envelope text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <h4 class="font-semibold text-text-primary mb-1">Email Support</h4>
                                <p class="text-text-primary font-medium mb-1">support@wesmaarrdec.gov.ph</p>
                                <small class="text-text-secondary text-sm">Response within 24 hours</small>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-center gap-4 p-6 bg-white rounded-lg shadow-sm">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-white text-lg"></i>
                            </div>
                            <div class="text-center">
                                <h4 class="font-semibold text-text-primary mb-1">Office Address</h4>
                                <p class="text-text-primary font-medium mb-1">WESMAARRDEC Complex, Zamboanga City</p>
                                <small class="text-text-secondary text-sm">Philippines 7000</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?php echo $base_url; ?>/pages/contact.php" class="inline-flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane"></i>
                            Contact Us
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<!-- Include Footer -->
<?php include '../includes/footer.php'; ?>

<style>
/* About page specific styles */
.about-content {
    margin-bottom: var(--spacing-8);
}

.mission-highlights {
    margin-top: var(--spacing-6);
}

.highlight-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-6);
}

.highlight-item {
    text-align: center;
    padding: var(--spacing-4);
}

.highlight-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-2xl);
    color: var(--white);
    margin: 0 auto var(--spacing-4) auto;
}

.highlight-item h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin: 0 0 var(--spacing-2) 0;
    color: var(--gray-900);
}

.highlight-item p {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin: 0;
    line-height: 1.5;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: var(--spacing-6);
}

.feature-item {
    display: flex;
    gap: var(--spacing-4);
    padding: var(--spacing-4);
    background-color: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    transition: all var(--transition-fast);
}

.feature-item:hover {
    background-color: var(--primary-green-50);
    border-color: var(--primary-green-200);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.feature-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-light) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-xl);
    color: var(--white);
    flex-shrink: 0;
}

.feature-content h3 {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin: 0 0 var(--spacing-2) 0;
    color: var(--gray-900);
}

.feature-content p {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin: 0;
    line-height: 1.5;
}

.tech-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: var(--spacing-4);
}

.tech-item {
    text-align: center;
    padding: var(--spacing-4);
    background-color: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-lg);
    transition: all var(--transition-fast);
}

.tech-item:hover {
    border-color: var(--primary-green);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.tech-icon {
    font-size: var(--font-size-3xl);
    color: var(--primary-green);
    margin-bottom: var(--spacing-3);
}

.tech-item h4 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin: 0 0 var(--spacing-2) 0;
    color: var(--gray-900);
}

.tech-item p {
    font-size: var(--font-size-sm);
    color: var(--gray-600);
    margin: 0;
    line-height: 1.4;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-6);
    margin-bottom: var(--spacing-6);
}

.contact-item {
    display: flex;
    gap: var(--spacing-4);
    padding: var(--spacing-4);
    background-color: var(--gray-50);
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
}

.contact-icon {
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

.contact-content h4 {
    font-size: var(--font-size-base);
    font-weight: 600;
    margin: 0 0 var(--spacing-1) 0;
    color: var(--gray-900);
}

.contact-content p {
    font-size: var(--font-size-base);
    color: var(--gray-700);
    margin: 0 0 var(--spacing-1) 0;
    font-weight: 500;
}

.contact-content small {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
}

@media (max-width: 768px) {
    .highlight-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: var(--spacing-4);
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-4);
    }
    
    .feature-item {
        flex-direction: column;
        text-align: center;
    }
    
    .tech-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: var(--spacing-3);
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-4);
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
    }
}
</style>
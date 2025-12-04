<?php
$pageTitle = "User Registration - Central CMI";
$bodyClass = "bg-background";
$submitError = isset($_GET['err']) ? urldecode($_GET['err']) : '';

include '../includes/header.php';
include '../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <section class="text-center mb-12">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-50 to-accent-50 rounded-2xl opacity-50"></div>
                <div class="relative px-8 py-12">
                    <div class="flex justify-center mb-6">
                        <div class="bg-primary-100 p-4 rounded-full">
                            <i class="fas fa-user-plus text-3xl text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-text-primary mb-4">Create Your Account</h1>
                    <p class="text-lg text-text-secondary max-w-2xl mx-auto">
                        Join the Central CMI platform to streamline your activity tracking, 
                        reporting, and collaboration workflows.
                    </p>
                </div>
            </div>
        </section>

        <!-- Registration Form -->
        <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-8">
            <?php if (!empty($submitError)): ?>
            <div class="mb-6 bg-error-50 border border-error-200 text-error rounded-md p-4">
                <?php echo htmlspecialchars($submitError); ?>
            </div>
            <?php endif; ?>
            <form id="registrationForm" method="POST" action="../database/register-new-user.php" class="space-y-8">
                <!-- Personal Information Section -->
                <div>
                    <h2 class="text-2xl font-semibold text-text-primary mb-6 flex items-center">
                        <i class="fas fa-user text-primary mr-3"></i>
                        Personal Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-text-primary mb-2">
                                First Name <span class="text-error">*</span>
                            </label>
                            <input type="text" id="firstName" name="firstName" required class="form-input" placeholder="Enter your first name" />
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-text-primary mb-2">
                                Last Name <span class="text-error">*</span>
                            </label>
                            <input type="text" id="lastName" name="lastName" required class="form-input" placeholder="Enter your last name" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-text-primary mb-2">
                                Email Address <span class="text-error">*</span>
                            </label>
                            <input type="email" id="email" name="email" required class="form-input" placeholder="your.email@agency.gov" />
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-text-primary mb-2">
                                Username <span class="text-error">*</span>
                            </label>
                            <input type="text" id="username" name="username" required class="form-input" placeholder="Set a unique username" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-text-primary mb-2">
                                Password <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required class="form-input pr-10" placeholder="Create a secure password" />
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('password')">
                                    <i id="password-eye" class="fas fa-eye text-secondary-400"></i>
                                </button>
                            </div>
                            
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-text-primary mb-2">
                                Confirm Password <span class="text-error">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="confirmPassword" required class="form-input pr-10" placeholder="Confirm your password" />
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('confirmPassword')">
                                    <i id="confirmPassword-eye" class="fas fa-eye text-secondary-400"></i>
                                </button>
                            </div>
                            
                        </div>

                        <!-- Birthdate -->
                        <div>
                            <label for="birthdate" class="block text-sm font-medium text-text-primary mb-2">
                                Birthdate
                            </label>
                            <input type="date" id="birthdate" name="birthdate" class="form-input" />
                        </div>
                    </div>
                </div>

                <!-- Professional Information Section -->
                <div>
                    <h2 class="text-2xl font-semibold text-text-primary mb-6 flex items-center">
                        <i class="fas fa-briefcase text-primary mr-3"></i>
                        Professional Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Designation (free text) -->
                        <div>
                            <label for="designation" class="block text-sm font-medium text-text-primary mb-2">
                                Designation <span class="text-error">*</span>
                            </label>
                            <input type="text" id="designation" name="designation" required class="form-input" placeholder="e.g., Senior Policy Analyst" />
                        </div>

                        <!-- Position (Cluster) -->
                        <div>
                            <label for="position" class="block text-sm font-medium text-text-primary mb-2">
                                Cluster <span class="text-error">*</span>
                            </label>
                            <select id="position" name="position" required class="form-input">
                                <option value>Select your cluster</option>
                                <option value="ICTC">ICTC - Information, Communication, and Technology Cluster</option>
                                <option value="RDC">RDC - Research and Development Cluster</option>
                                <option value="SCC">SCC - Science Communication Cluster</option>
                                <option value="TTC">TTC - Technology Transfer Cluster</option>
                            </select>
                        </div>

                        <!-- Agency Affiliation -->
                        <div class="md:col-span-2">
                            <label for="agency" class="block text-sm font-medium text-text-primary mb-2">
                                Agency Affiliation <span class="text-error">*</span>
                            </label>
                            <select id="agency" name="agency" required class="form-input">
                                <option value>Select your agency</option>
                                <option value="PCAARRD">Philippine Council for Agriculture, Aquatic, and Natural Resources Research and Development (PCAARRD)</option>
                                <option value="DOST-IX">Department of Science and Technology  Region IX (DOST-IX)</option>
                                <option value="DA-RFO IX">Department of Agriculture  Regional Field Office IX (DA-RFO IX)</option>
                                <option value="WMSU">Western Mindanao State University (WMSU)</option>
                                <option value="JHCSC">Josefina H. Cerilles State College (JHCSC)</option>
                                <option value="DTI-IX">Department of Trade and Industry  Region IX (DTI-IX)</option>
                                <option value="BFAR-IX">Bureau of Fisheries and Aquatic Resources  Region IX (BFAR-IX)</option>
                                <option value="NEDA-IX">National Economic and Development Authority  Region IX (NEDA-IX)</option>
                                <option value="PRRI-IX">Philippine Rubber Research Institute  Region IX (PRRI-IX)</option>
                                <option value="PhilFIDA-IX">Philippine Fiber Industry Development Authority  Region IX (PhilFIDA-IX)</option>
                                <option value="DA-BAR">Department of Agriculture  Bureau of Agricultural Research (DA-BAR)</option>
                                <option value="PCA-ZRC">Philippine Coconut Authority  Zamboanga Research Center (PCA-ZRC)</option>
                            </select>
                            
                        </div>
                    </div>
                </div>
                <!-- Role Selection: hidden, default to representative -->
                <input type="hidden" id="role" name="role" value="representative" />

                

                

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" id="submit-btn" class="flex-1 bg-primary text-white px-6 py-3 rounded-md font-medium hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-micro">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Account
                    </button>
                    <button type="button" class="flex-1 sm:flex-none bg-secondary-100 text-secondary-700 px-6 py-3 rounded-md font-medium hover:bg-secondary-200 focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 transition-micro" onclick="resetForm()">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Form
                    </button>
                </div>

                <!-- Login Link -->
                <div class="text-center pt-4 border-t border-secondary-200">
                    <p class="text-sm text-text-secondary">
                        Already have an account? 
                        <a href="login.php" class="text-primary hover:text-primary-700 font-medium">Sign in here</a>
                    </p>
                </div>
            </form>
        </section>

        
    </main>

    <script>
        // Password visibility toggle (minimal)
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        function resetForm() {
            document.getElementById('registrationForm').reset();
        }
    </script>

<?php include '../includes/footer.php'; ?>
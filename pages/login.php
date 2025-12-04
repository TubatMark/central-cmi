<?php
$pageTitle = "Login - Central CMI";
$bodyClass = "bg-background min-h-screen flex flex-col";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- Main Content -->
<main class="flex-1 flex items-center justify-center relative z-10">
    <!-- Login Container -->
    <div class="w-full max-w-md px-6 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-xl font-semibold text-text-primary">Welcome back</h2>
            <p class="text-text-secondary">Sign in to your Central CMI account</p>
        </div>

        <!-- Login Form -->
        <div class="bg-surface rounded-xl shadow-lg border border-secondary-200 p-8 backdrop-blur-sm bg-opacity-95">
            <form id="loginForm" method="POST" action="../database/login.php">
                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-text-primary mb-2">
                        <i class="fas fa-envelope mr-2 text-text-secondary"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email" required class="form-input" placeholder="Enter your email" autocomplete="email" />
                    <div id="emailError" class="text-error text-sm mt-1 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span>Please enter a valid email address</span>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-text-primary mb-2">
                        <i class="fas fa-lock mr-2 text-text-secondary"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required class="form-input pr-10" placeholder="Enter your password" autocomplete="current-password" />
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePasswordVisibility()">
                            <i id="passwordToggleIcon" class="fas fa-eye text-text-secondary hover:text-primary transition-micro"></i>
                        </button>
                    </div>
                    <div id="passwordError" class="text-error text-sm mt-1 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span>Password is required</span>
                    </div>
                </div>

                <!-- Forgot Password Link -->
                <div class="flex items-center justify-end mb-6">
                    <a href="#" class="text-sm font-medium text-primary hover:text-primary-700 transition-micro">Forgot your password?</a>
                </div>

                <!-- Login Button -->
                <div class="mb-6">
                    <button type="submit" id="loginButton" class="w-full btn-primary">
                        Sign in
                    </button>
                </div>

                <!-- Registration Link -->
                <div class="text-center">
                    <p class="text-text-secondary text-sm">
                        Need access?
                        <a href="user_registration.php" class="font-medium text-primary hover:text-primary-700 transition-micro">
                            Request account registration
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>


<script>
    // Password visibility toggle (kept for usability)
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>

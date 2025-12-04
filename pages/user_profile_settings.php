<?php
require_once __DIR__ . '/../database/auth.php';
require_role(['representative', 'secretariat']);
@require_once __DIR__ . '/../database/config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$firstName = '';
$lastName = '';
$emailAddr = '';
$position = '';
$agency = '';

if (isset($_SESSION['user_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare('SELECT firstName, lastName, email, position, agency FROM `User` WHERE UserID = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        if ($u = $stmt->fetch()) {
            $firstName = trim($u['firstName'] ?? '');
            $lastName = trim($u['lastName'] ?? '');
            $emailAddr = trim($u['email'] ?? '');
            $position = trim($u['position'] ?? '');
            $agency = trim($u['agency'] ?? '');
        }
    } catch (Throwable $e) {}
}

$initials = 'U';
if ($firstName !== '' || $lastName !== '') {
    $initials = strtoupper((($firstName !== '' ? $firstName[0] : '') . ($lastName !== '' ? $lastName[0] : '')));
}

$clusterMap = [
    'SCC' => 'Science Communication Cluster',
    'ICTC' => 'Information, Communication, Technology Cluster',
    'RDC' => 'Research & Development Cluster',
    'TTC' => 'Technology Transfer Cluster',
];
$positionFull = isset($clusterMap[strtoupper($position)]) ? $clusterMap[strtoupper($position)] : $position;
?>
<?php
$pageTitle = "User Profile Settings";
$bodyClass = "bg-background";
include '../includes/header.php';
include '../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary flex items-center">
                        <i class="fas fa-user-cog text-primary mr-3"></i>
                        User Profile Settings
                    </h1>
                    <p class="text-text-secondary mt-2">Manage your personal information, and security settings</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button type="button" class="btn-secondary" onclick="resetToDefaults()">
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Defaults
                    </button>
                    <button type="button" class="btn-primary" onclick="saveAllSettings()">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </div>
            </div>
        </section>

        <!-- Settings Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1">
                <nav class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h2 class="text-lg font-semibold text-text-primary mb-4">Settings</h2>
                    <ul class="space-y-2">
                        <li>
                            <button type="button" class="settings-nav-btn active w-full text-left px-3 py-2 rounded-md transition-micro" onclick="showTab('profile')" id="profile-tab">
                                <i class="fas fa-user mr-2"></i>
                                Profile Information
                            </button>
                        </li>
                        <li>
                            <button type="button" class="settings-nav-btn w-full text-left px-3 py-2 rounded-md transition-micro" onclick="showTab('security')" id="security-tab">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Security
                            </button>
                        </li>
                        <li>
                            <button type="button" class="settings-nav-btn w-full text-left px-3 py-2 rounded-md transition-micro" onclick="showTab('account')" id="account-tab">
                                <i class="fas fa-id-badge mr-2"></i>
                                Account Details
                            </button>
                        </li>
                    </ul>

                    <!-- Quick Actions -->
                    <div class="mt-8 pt-6 border-t border-secondary-200">
                        <h3 class="text-sm font-medium text-text-primary mb-3">Quick Actions</h3>
                        <div class="space-y-2">
                            <button type="button" class="text-error hover:bg-error-50 w-full text-left px-3 py-2 rounded-md text-sm transition-micro" onclick="showDeactivateModal()">
                                <i class="fas fa-user-slash mr-2"></i>
                                Deactivate Account
                            </button>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-3">
                <!-- Profile Information Tab -->
                <div id="profile-content" class="settings-content">
                    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold text-text-primary">Profile Information</h2>
                            <div class="text-sm text-text-secondary">
                                <i class="fas fa-info-circle mr-1"></i>
                                Auto-save enabled
                            </div>
                        </div>

                        <!-- Profile Photo Section -->
                        <div class="flex flex-col sm:flex-row items-start space-y-6 sm:space-y-0 sm:space-x-8 mb-8 pb-8 border-b border-secondary-200">
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    <div class="h-24 w-24 bg-primary rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                        <?php echo htmlspecialchars($initials); ?>
                                    </div>
                                    <button type="button" class="absolute -bottom-2 -right-2 bg-surface border border-secondary-200 rounded-full p-2 hover:bg-secondary-50 transition-micro" onclick="openPhotoUpload()">
                                        <i class="fas fa-camera text-text-secondary"></i>
                                    </button>
                                </div>
                                <p class="text-sm text-text-secondary mt-2 text-center">Click camera to change photo</p>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-text-primary mb-2">Profile Photo Guidelines</h3>
                                <ul class="text-sm text-text-secondary space-y-1">
                                    <li>• Professional government-appropriate image</li>
                                    <li>• Maximum file size: 5MB</li>
                                    <li>• Formats: JPG, PNG, WEBP</li>
                                    <li>• Minimum resolution: 200x200 pixels</li>
                                </ul>
                                <div class="mt-4">
                                    <input type="file" id="profile-photo" accept="image/*" class="hidden" onchange="handlePhotoUpload(this)" />
                                    <button type="button" class="btn-secondary text-sm" onclick="document.getElementById('profile-photo').click()">
                                        <i class="fas fa-upload mr-2"></i>
                                        Upload New Photo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information Form -->
                        <form class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        First Name *
                                    </label>
                                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($firstName); ?>" required />
                                    <div class="text-xs text-success mt-1 hidden">
                                        <i class="fas fa-check mr-1"></i>
                                        Valid
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Last Name *
                                    </label>
                                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($lastName); ?>" required />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Email Address *
                                    </label>
                                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($emailAddr); ?>" required />
                                    <div class="text-xs text-text-secondary mt-1">
                                        <i class="fas fa-shield-alt mr-1"></i>
                                        Verified government email
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" class="form-input" value="+1 (555) 123-4567" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Agency/Department *
                                    </label>
                                    <select class="form-input" required>
                                        <?php if ($agency !== ''): ?>
                                        <option value="<?php echo htmlspecialchars($agency); ?>" selected><?php echo htmlspecialchars($agency); ?></option>
                                        <?php endif; ?>
                                        <option value="PCAARRD">Philippine Council for Agriculture, Aquatic, and Natural Resources Research and Development (PCAARRD)</option>
                                        <option value="DOST-IX">Department of Science and Technology – Region IX (DOST-IX)</option>
                                        <option value="DA-RFO IX">Department of Agriculture – Regional Field Office IX (DA-RFO IX)</option>
                                        <option value="WMSU">Western Mindanao State University (WMSU)</option>
                                        <option value="JHCSC">Josefina H. Cerilles State College (JHCSC)</option>
                                        <option value="DTI-IX">Department of Trade and Industry – Region IX (DTI-IX)</option>
                                        <option value="BFAR-IX">Bureau of Fisheries and Aquatic Resources – Region IX (BFAR-IX)</option>
                                        <option value="NEDA-IX">National Economic and Development Authority – Region IX (NEDA-IX)</option>
                                        <option value="PRRI-IX">Philippine Rubber Research Institute – Region IX (PRRI-IX)</option>
                                        <option value="PhilFIDA-IX">Philippine Fiber Industry Development Authority – Region IX (PhilFIDA-IX)</option>
                                        <option value="DA-BAR">Department of Agriculture – Bureau of Agricultural Research (DA-BAR)</option>
                                        <option value="PCA-ZRC">Philippine Coconut Authority – Zamboanga Research Center (PCA-ZRC)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Job Title
                                    </label>
                                    <input type="text" class="form-input" value="System Administrator" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">
                                    Office Location
                                </label>
                                <input type="text" class="form-input" placeholder="Building, Floor, Room Number" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">
                                    Bio/Description
                                </label>
                                <textarea class="form-input" rows="4" placeholder="Brief professional description...">Experienced system administrator specializing in government activity management systems. Over 8 years of experience in digital transformation and process optimization.</textarea>
                                <div class="text-xs text-text-secondary mt-1">
                                    Characters remaining: 245/500
                                </div>
                            </div>
                        </form>
                    </section>
                </div>

                <!-- Security Tab -->
                <div id="security-content" class="settings-content hidden">
                    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-8">
                        <h2 class="text-2xl font-semibold text-text-primary mb-6">Security Settings</h2>

                        <!-- Password Change -->
                        <div class="mb-8 pb-8 border-b border-secondary-200">
                            <h3 class="text-lg font-medium text-text-primary mb-4">Password</h3>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Current Password
                                    </label>
                                    <input type="password" class="form-input" placeholder="Enter current password" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        New Password
                                    </label>
                                    <input type="password" class="form-input" placeholder="Enter new password" />
                                    <div class="mt-2">
                                        <div class="text-xs text-text-secondary mb-2">Password Strength:</div>
                                        <div class="w-full bg-secondary-200 rounded-full h-2">
                                            <div class="bg-warning h-2 rounded-full transition-all duration-300" style="width: 60%"></div>
                                        </div>
                                        <div class="text-xs text-warning mt-1">Medium - Add special characters</div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-text-primary mb-2">
                                        Confirm New Password
                                    </label>
                                    <input type="password" class="form-input" placeholder="Confirm new password" />
                                </div>
                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-text-primary mb-2">Password Requirements:</h4>
                                    <ul class="text-xs text-text-secondary space-y-1">
                                        <li class="flex items-center"><i class="fas fa-check text-success mr-2"></i>At least 8 characters</li>
                                        <li class="flex items-center"><i class="fas fa-times text-error mr-2"></i>At least one uppercase letter</li>
                                        <li class="flex items-center"><i class="fas fa-check text-success mr-2"></i>At least one lowercase letter</li>
                                        <li class="flex items-center"><i class="fas fa-check text-success mr-2"></i>At least one number</li>
                                        <li class="flex items-center"><i class="fas fa-times text-error mr-2"></i>At least one special character</li>
                                    </ul>
                                </div>
                                <button type="button" class="btn-primary">
                                    <i class="fas fa-key mr-2"></i>
                                    Update Password
                                </button>
                            </form>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="mb-8 pb-8 border-b border-secondary-200">
                            <h3 class="text-lg font-medium text-text-primary mb-4">Two-Factor Authentication</h3>
                            <div class="flex items-start space-x-4">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <p class="font-medium text-text-primary">Enable Two-Factor Authentication</p>
                                            <p class="text-sm text-text-secondary">Add an extra layer of security to your account</p>
                                        </div>
                                        <label class="toggle-switch">
                                            <input type="checkbox" />
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                    
                                    <div class="bg-warning-50 border border-warning-200 rounded-lg p-4">
                                        <div class="flex items-start">
                                            <i class="fas fa-exclamation-triangle text-warning mr-3 mt-1"></i>
                                            <div>
                                                <p class="font-medium text-warning-800">Two-Factor Authentication Disabled</p>
                                                <p class="text-sm text-warning-700 mt-1">
                                                    Your account is not protected by 2FA. Enable it for better security.
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 space-x-3">
                                        <button type="button" class="btn-primary">
                                            <i class="fas fa-mobile-alt mr-2"></i>
                                            Set up with App
                                        </button>
                                        <button type="button" class="btn-secondary">
                                            <i class="fas fa-sms mr-2"></i>
                                            Set up with SMS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Sessions -->
                        <div>
                            <h3 class="text-lg font-medium text-text-primary mb-4">Active Sessions</h3>
                            <div class="space-y-4">
                                <!-- Current Session -->
                                <div class="border border-primary bg-primary-50 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-desktop text-primary text-xl"></i>
                                            <div>
                                                <p class="font-medium text-text-primary">Windows Desktop - Chrome</p>
                                                <p class="text-sm text-text-secondary">192.168.1.100 • Washington, DC</p>
                                                <p class="text-xs text-success mt-1">
                                                    <i class="fas fa-circle mr-1"></i>
                                                    Current session
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right text-sm text-text-secondary">
                                            <p>Last active: Now</p>
                                            <p>Login: Jan 4, 2025 9:15 AM</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other Sessions -->
                                <div class="border border-secondary-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-mobile-alt text-text-secondary text-xl"></i>
                                            <div>
                                                <p class="font-medium text-text-primary">iPhone - Safari</p>
                                                <p class="text-sm text-text-secondary">10.0.0.45 • Washington, DC</p>
                                                <p class="text-xs text-text-secondary mt-1">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Inactive
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-text-secondary mb-2">
                                                <p>Last active: 2h ago</p>
                                                <p>Login: Jan 4, 2025 7:30 AM</p>
                                            </div>
                                            <button type="button" class="text-error hover:bg-error-50 px-3 py-1 rounded text-sm transition-micro">
                                                <i class="fas fa-sign-out-alt mr-1"></i>
                                                Sign out
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="border border-secondary-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-start space-x-3">
                                            <i class="fas fa-tablet-alt text-text-secondary text-xl"></i>
                                            <div>
                                                <p class="font-medium text-text-primary">iPad - Safari</p>
                                                <p class="text-sm text-text-secondary">192.168.1.200 • Washington, DC</p>
                                                <p class="text-xs text-text-secondary mt-1">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Inactive
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-text-secondary mb-2">
                                                <p>Last active: 1 day ago</p>
                                                <p>Login: Jan 3, 2025 3:45 PM</p>
                                            </div>
                                            <button type="button" class="text-error hover:bg-error-50 px-3 py-1 rounded text-sm transition-micro">
                                                <i class="fas fa-sign-out-alt mr-1"></i>
                                                Sign out
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="button" class="btn-secondary" onclick="signOutAllSessions()">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    Sign out of all other sessions
                                </button>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Account Details Tab -->
                <div id="account-content" class="settings-content hidden">
                    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-8">
                        <h2 class="text-2xl font-semibold text-text-primary mb-6">Account Details</h2>

                        <!-- Account Information -->
                        <div class="mb-8 pb-8 border-b border-secondary-200">
                            <h3 class="text-lg font-medium text-text-primary mb-4">Account Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <label class="text-sm font-medium text-text-primary">User ID</label>
                                    <p class="text-text-secondary">USR-2025-001847</p>
                                </div>
                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <label class="text-sm font-medium text-text-primary">Account Created</label>
                                    <p class="text-text-secondary">March 15, 2023</p>
                                </div>
                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <label class="text-sm font-medium text-text-primary">Last Login</label>
                                    <p class="text-text-secondary">January 4, 2025 at 9:15 AM</p>
                                </div>
                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <label class="text-sm font-medium text-text-primary">Account Status</label>
                                    <span class="status-badge status-success">Active</span>
                                </div>
                            </div>
                        </div>

                        <!-- Role & Permissions -->
                        <div class="mb-8 pb-8 border-b border-secondary-200">
                            <h3 class="text-lg font-medium text-text-primary mb-4">Role & Permissions</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-text-primary">Primary Role</p>
                                        <p class="text-sm text-text-secondary">System Administrator</p>
                                    </div>
                                    <span class="status-badge status-primary">Administrative</span>
                                </div>

                                <div class="bg-secondary-50 rounded-lg p-4">
                                    <h4 class="font-medium text-text-primary mb-3">Current Permissions</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Create activities</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Edit all activities</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Delete activities</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Generate reports</span>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Manage users</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>System configuration</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Access all departments</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-check text-success mr-2"></i>
                                                <span>Export data</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agency Verification -->
                        <div>
                            <h3 class="text-lg font-medium text-text-primary mb-4">Agency Verification</h3>
                            <div class="bg-success-50 border border-success-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-shield-check text-success mr-3 mt-1"></i>
                                    <div>
                                        <p class="font-medium text-success-800">Verified Government Employee</p>
                                        <p class="text-sm text-success-700 mt-1">
                                            Your employment status has been verified by the Department of Health HR system.
                                        </p>
                                        <div class="mt-3 text-xs text-success-600">
                                            <p>Verification Date: March 15, 2023</p>
                                            <p>Verified By: HR System Integration</p>
                                            <p>Next Verification: March 15, 2025</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <!-- Deactivate Account Modal -->
        <div id="deactivate-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4 p-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-error-100 p-3 rounded-full">
                            <i class="fas fa-user-slash text-2xl text-error"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Deactivate Account</h3>
                    <p class="text-text-secondary mb-6">
                        Are you sure you want to deactivate your account? This action cannot be undone and will require administrator approval to reactivate.
                    </p>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-text-primary mb-2">
                            Type "DEACTIVATE" to confirm:
                        </label>
                        <input type="text" class="form-input text-center" placeholder="DEACTIVATE" />
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" class="btn-secondary flex-1" onclick="closeDeactivateModal()">
                            Cancel
                        </button>
                        <button type="button" class="bg-error hover:bg-error-600 text-white px-4 py-2 rounded-lg transition-micro flex-1">
                            <i class="fas fa-user-slash mr-2"></i>
                            Deactivate Account
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Success Toast -->
        <div id="save-toast" class="fixed top-20 right-4 bg-success text-white px-6 py-3 rounded-lg shadow-lg z-50 hidden">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Settings saved successfully!</span>
            </div>
        </div>
    </main>

    <style>
        /* Custom Toggle Switch Styles */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .3s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        input:checked + .toggle-slider {
            background-color: var(--color-primary);
        }

        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        /* Settings Navigation Styles */
        .settings-nav-btn {
            color: var(--color-text-secondary);
        }

        .settings-nav-btn:hover {
            color: var(--color-primary);
            background-color: var(--color-secondary-100);
        }

        .settings-nav-btn.active {
            color: var(--color-primary);
            background-color: var(--color-primary-50);
            font-weight: 500;
        }

        /* Toast Animation */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-slide-in {
            animation: slideInRight 0.3s ease-out;
        }
    </style>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Tab switching functionality
        function showTab(tabName) {
            // Hide all content sections
            const contents = document.querySelectorAll('.settings-content');
            contents.forEach(content => content.classList.add('hidden'));

            // Remove active state from all nav buttons
            const navBtns = document.querySelectorAll('.settings-nav-btn');
            navBtns.forEach(btn => btn.classList.remove('active'));

            // Show selected content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Add active state to clicked nav button
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // Profile photo upload handling
        function openPhotoUpload() {
            document.getElementById('profile-photo').click();
        }

        function handlePhotoUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Here you would typically upload the image and update the profile photo
                    console.log('Photo uploaded:', e.target.result);
                    showSaveToast('Profile photo updated successfully!');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Settings save functionality
        function saveAllSettings() {
            // Simulate saving settings
            showSaveToast('All settings saved successfully!');
        }

        function resetToDefaults() {
            if (confirm('Are you sure you want to reset all settings to default values?')) {
                // Reset form values to defaults
                showSaveToast('Settings reset to defaults');
            }
        }

        // Security functions
        function signOutAllSessions() {
            if (confirm('Are you sure you want to sign out of all other sessions?')) {
                showSaveToast('Signed out of all other sessions');
            }
        }

        // Account functions
        function showDeactivateModal() {
            document.getElementById('deactivate-modal').classList.remove('hidden');
        }

        function closeDeactivateModal() {
            document.getElementById('deactivate-modal').classList.add('hidden');
        }

        // Toast notification
        function showSaveToast(message = 'Settings saved successfully!') {
            const toast = document.getElementById('save-toast');
            toast.querySelector('span').textContent = message;
            toast.classList.remove('hidden');
            toast.classList.add('toast-slide-in');
            
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.classList.remove('toast-slide-in');
            }, 3000);
        }

        // Auto-save functionality simulation
        let saveTimeout;
        function autoSave() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                showSaveToast('Changes auto-saved');
            }, 2000);
        }

        // Add event listeners for auto-save on form changes
        document.addEventListener('DOMContentLoaded', function() {
            const formInputs = document.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                input.addEventListener('change', autoSave);
                input.addEventListener('input', autoSave);
            });

            // Initialize with profile tab active
            showTab('profile');
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                numbers: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            Object.values(checks).forEach(check => {
                if (check) strength++;
            });

            return { strength, checks };
        }

        // Update password strength indicator
        function updatePasswordStrength(input) {
            const result = checkPasswordStrength(input.value);
            const strengthBar = input.parentNode.querySelector('.h-2');
            const strengthText = input.parentNode.querySelector('.text-warning');
            
            const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
            const colors = ['bg-error', 'bg-error', 'bg-warning', 'bg-warning', 'bg-success', 'bg-success'];
            const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
            
            strengthBar.style.width = widths[result.strength];
            strengthBar.className = `h-2 rounded-full transition-all duration-300 ${colors[result.strength]}`;
            strengthText.textContent = texts[result.strength];
        }
    </script>

<?php include '../includes/footer.php'; ?>
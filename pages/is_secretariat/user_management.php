<?php
$pageTitle = "User Management - Central CMI";
$bodyClass = "bg-background min-h-screen";
require_once __DIR__ . '/../../database/auth.php';
require_role(['secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary">User Management</h1>
                    <p class="mt-2 text-text-secondary">Create, update, and manage users and their roles</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button type="button" class="btn-secondary" onclick="exportUsers()">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    <button type="button" class="btn-primary" onclick="openUserModal()">
                        <i class="fas fa-user-plus mr-2"></i>
                        New User
                    </button>
                </div>
            </div>
        </section>

        <!-- Filters and Search -->
        <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-text-primary mb-2">Search Users</label>
                    <div class="relative">
                        <input type="text" id="user-search" placeholder="Search by name, email..." class="form-input pl-10" oninput="filterUsers()" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-secondary-400"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Role</label>
                    <select id="role-filter" class="form-input" onchange="filterUsers()">
                        <option value>All Roles</option>
                        <option value="secretariat">Secretariat</option>
                        <option value="representative">Representative</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Status</label>
                    <select id="status-filter" class="form-input" onchange="filterUsers()">
                        <option value>All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Users Table -->
        <section class="bg-surface rounded-xl shadow-card border border-secondary-200 overflow-hidden">
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-secondary-200">
                    <thead class="bg-secondary-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body" class="bg-surface divide-y divide-secondary-200">
                        <!-- Rows populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden" id="users-mobile-view">
                <!-- Cards populated by JS -->
            </div>

            <!-- Empty State -->
            <div id="users-empty-state" class="text-center py-12 hidden">
                <div class="flex justify-center mb-4">
                    <div class="bg-secondary-100 p-4 rounded-full">
                        <i class="fas fa-users text-2xl text-secondary-400"></i>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-text-primary mb-2">No users found</h3>
                <p class="text-text-secondary mb-6">Create a user or adjust your filters.</p>
                <button type="button" class="btn-primary" onclick="openUserModal()">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create User
                </button>
            </div>
        </section>

        <!-- Pagination -->
        <section class="flex items-center justify-between mt-6">
            <div class="text-sm text-text-secondary">
                Showing <span id="users-showing">0</span> of <span id="users-total">0</span> users
            </div>
            <div class="flex space-x-2">
                <button type="button" class="px-3 py-2 text-sm border border-secondary-300 rounded-md text-text-secondary hover:bg-secondary-50 disabled:opacity-50" disabled>
                    <i class="fas fa-chevron-left mr-1"></i>
                    Previous
                </button>
                <button type="button" class="px-3 py-2 text-sm bg-primary text-white rounded-md">1</button>
                <button type="button" class="px-3 py-2 text-sm border border-secondary-300 rounded-md text-text-secondary hover:bg-secondary-50">
                    Next
                    <i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </section>
    </main>

    <!-- Create/Edit User Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 id="user-modal-title" class="text-xl font-semibold text-text-primary">New User</h3>
                    <button type="button" class="text-secondary-400 hover:text-secondary-600" onclick="closeUserModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="user-form" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">First Name <span class="text-error">*</span></label>
                            <input type="text" id="first-name" class="form-input" placeholder="First name" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Last Name <span class="text-error">*</span></label>
                            <input type="text" id="last-name" class="form-input" placeholder="Last name" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Username <span class="text-error">*</span></label>
                            <input type="text" id="username" class="form-input" placeholder="username" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Email <span class="text-error">*</span></label>
                            <input type="email" id="email" class="form-input" placeholder="name@example.com" required />
                        </div>
                    </div>
                    <div id="password-field">
                        <label class="block text-sm font-medium text-text-primary mb-2">Password <span id="password-required" class="text-error">*</span></label>
                        <input type="password" id="password" class="form-input" placeholder="Enter password" />
                        <p id="password-hint" class="text-xs text-text-secondary mt-1 hidden">Leave blank to keep current password</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Birthdate</label>
                            <input type="date" id="birthdate" class="form-input" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Designation</label>
                            <input type="text" id="designation" class="form-input" placeholder="e.g., Program Coordinator" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Position/Cluster <span class="text-error">*</span></label>
                            <select id="position" class="form-input" required>
                                <option value="">Choose position</option>
                                <option value="ICTC">ICTC - Information & Communications Technology Cluster</option>
                                <option value="RDC">RDC - Research & Development Cluster</option>
                                <option value="SCC">SCC - Science Communication Cluster</option>
                                <option value="TTC">TTC - Technology Transfer Cluster</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-primary mb-2">Agency</label>
                            <input type="text" id="agency" class="form-input" placeholder="Agency name" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Role <span class="text-error">*</span></label>
                        <select id="role" class="form-input" required>
                            <option value="">Choose role</option>
                            <option value="representative">Representative</option>
                            <option value="secretariat">Secretariat</option>
                        </select>
                    </div>
                    <div class="hidden">
                        <label class="block text-sm font-medium text-text-primary mb-2">Status</label>
                        <select id="status" class="form-input">
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex gap-3 pt-4 border-t border-secondary-200">
                        <button type="submit" class="flex-1 btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Save User
                        </button>
                        <button type="button" class="flex-1 btn-secondary" onclick="closeUserModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Users data loaded from database
        let users = [];
        const API_URL = BASE_URL + 'api/users.php';

        // Load users from database
        async function loadUsers() {
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                
                if (data.success) {
                    users = data.users;
                    filterUsers();
                } else {
                    showToast('Failed to load users: ' + (data.error || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showToast('Error loading users from server', 'error');
            }
        }

        function renderUsers(list) {
            const tbody = document.getElementById('users-table-body');
            const mobile = document.getElementById('users-mobile-view');
            const empty = document.getElementById('users-empty-state');
            const showing = document.getElementById('users-showing');
            const total = document.getElementById('users-total');

            if (!tbody || !mobile || !empty) return;

            tbody.innerHTML = '';
            mobile.innerHTML = '';

            total.textContent = users.length;
            showing.textContent = list.length;

            if (list.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            // Desktop rows
            list.forEach(u => {
                const displayName = [u.firstName, u.lastName].filter(Boolean).join(' ');
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-secondary-50 transition-micro';
                tr.innerHTML = `
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-text-primary">${displayName}</div>
                        <div class="text-xs text-text-secondary">@${u.username || 'N/A'}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${u.email}</td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${u.position || 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${u.role === 'secretariat' ? 'bg-primary-100 text-primary-700' : 'bg-accent-100 text-accent-700'} capitalize">${u.role}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${u.status === 'active' ? 'status-success' : 'status-warning'}">${u.status === 'active' ? 'Active' : 'Inactive'}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button class="text-primary hover:text-primary-700 text-sm" onclick="editUser(${u.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-error hover:text-error-700 text-sm" onclick="deleteUser(${u.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            // Mobile cards
            list.forEach(u => {
                const displayName = [u.firstName, u.lastName].filter(Boolean).join(' ');
                const card = document.createElement('div');
                card.className = 'p-4 border-b border-secondary-200 hover:bg-secondary-50 transition-micro';
                card.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-sm font-medium text-text-primary">${displayName}</h4>
                            <p class="text-xs text-text-secondary">@${u.username || 'N/A'} • ${u.email}</p>
                            <div class="flex gap-2 mt-2">
                                <span class="status-badge ${u.role === 'secretariat' ? 'bg-primary-100 text-primary-700' : 'bg-accent-100 text-accent-700'} capitalize text-xs">${u.role}</span>
                                <span class="status-badge bg-secondary-100 text-secondary-700 text-xs">${u.position || 'N/A'}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="status-badge ${u.status === 'active' ? 'status-success' : 'status-warning'}">${u.status === 'active' ? 'Active' : 'Inactive'}</span>
                            <div class="mt-2 flex space-x-2 justify-end">
                                <button class="text-primary hover:text-primary-700 text-sm" onclick="editUser(${u.id})"><i class="fas fa-edit"></i></button>
                                <button class="text-error hover:text-error-700 text-sm" onclick="deleteUser(${u.id})"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                mobile.appendChild(card);
            });
        }

        function getFilteredUsers() {
            const search = (document.getElementById('user-search').value || '').toLowerCase();
            const role = document.getElementById('role-filter').value;
            const status = document.getElementById('status-filter').value;
            return users.filter(u => {
                let ok = true;
                const nameForSearch = [u.firstName, u.lastName].filter(Boolean).join(' ').toLowerCase();
                if (search && !(nameForSearch.includes(search) || u.email.toLowerCase().includes(search))) ok = false;
                if (role && u.role !== role) ok = false;
                if (status && u.status !== status) ok = false;
                return ok;
            });
        }

        function filterUsers() {
            renderUsers(getFilteredUsers());
        }

        function openUserModal(editing = false, user = null) {
            document.getElementById('user-modal').classList.remove('hidden');
            const title = document.getElementById('user-modal-title');
            title.textContent = editing ? 'Edit User' : 'New User';
            const form = document.getElementById('user-form');
            form.reset();
            
            // Toggle password requirement based on mode
            const passwordRequired = document.getElementById('password-required');
            const passwordHint = document.getElementById('password-hint');
            const usernameField = document.getElementById('username');
            
            if (editing && user) {
                document.getElementById('first-name').value = user.firstName || '';
                document.getElementById('last-name').value = user.lastName || '';
                document.getElementById('username').value = user.username || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('birthdate').value = user.birthdate || '';
                document.getElementById('designation').value = user.designation || '';
                document.getElementById('position').value = user.position || '';
                document.getElementById('agency').value = user.agency || '';
                document.getElementById('role').value = user.role || '';
                document.getElementById('status').value = user.status || 'active';
                form.dataset.editId = String(user.id);
                
                // Password optional when editing
                passwordRequired.classList.add('hidden');
                passwordHint.classList.remove('hidden');
                usernameField.removeAttribute('required');
            } else {
                delete form.dataset.editId;
                
                // Password required for new users
                passwordRequired.classList.remove('hidden');
                passwordHint.classList.add('hidden');
                usernameField.setAttribute('required', 'required');
            }
        }

        function closeUserModal() {
            document.getElementById('user-modal').classList.add('hidden');
        }

        document.getElementById('user-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userData = {
                firstName: document.getElementById('first-name').value.trim(),
                lastName: document.getElementById('last-name').value.trim(),
                email: document.getElementById('email').value.trim(),
                username: document.getElementById('username').value.trim(),
                birthdate: document.getElementById('birthdate').value || null,
                designation: document.getElementById('designation').value.trim(),
                position: document.getElementById('position').value.trim(),
                agency: document.getElementById('agency').value.trim(),
                role: document.getElementById('role').value
            };
            
            // Add password for new users or if changing password
            const password = document.getElementById('password').value;
            if (password) {
                userData.password = password;
            }
            
            const editId = this.dataset.editId;
            const isEditing = editId && editId !== '';
            
            // Validate required fields
            if (!userData.firstName || !userData.lastName || !userData.email || !userData.role) {
                showToast('Please fill in all required fields', 'error');
                return;
            }
            
            if (!isEditing && !userData.password) {
                showToast('Password is required for new users', 'error');
                return;
            }
            
            if (!isEditing && !userData.username) {
                showToast('Username is required for new users', 'error');
                return;
            }

            try {
                let response;
                if (isEditing) {
                    userData.id = Number(editId);
                    response = await fetch(API_URL, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(userData)
                    });
                } else {
                    response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(userData)
                    });
                }
                
                const result = await response.json();
                
                if (result.success) {
                    closeUserModal();
                    await loadUsers();
                    showToast(isEditing ? 'User updated successfully' : 'User created successfully', 'success');
                } else {
                    showToast(result.error || 'Failed to save user', 'error');
                }
            } catch (error) {
                console.error('Error saving user:', error);
                showToast('Error saving user', 'error');
            }
        });

        function editUser(id) {
            const user = users.find(u => u.id === id);
            if (user) {
                openUserModal(true, user);
            }
        }

        async function deleteUser(id) {
            if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`${API_URL}?id=${id}`, {
                    method: 'DELETE'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    await loadUsers();
                    showToast('User deleted successfully', 'success');
                } else {
                    showToast(result.error || 'Failed to delete user', 'error');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                showToast('Error deleting user', 'error');
            }
        }

        function exportUsers() {
            showToast('Exporting users...', 'info');
        }

        function showToast(message, type = 'info') {
            const note = document.createElement('div');
            note.className = 'fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transition-all duration-300 transform translate-x-full';
            const colors = {
                success: 'bg-success-100 text-success-700 border border-success-200',
                error: 'bg-error-100 text-error-700 border border-error-200',
                warning: 'bg-warning-100 text-warning-700 border border-warning-200',
                info: 'bg-primary-100 text-primary-700 border border-primary-200'
            };
            note.className += ` ${colors[type] || colors.info}`;
            note.innerHTML = `<div class="flex items-center"><i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i><span>${message}</span><button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button></div>`;
            document.body.appendChild(note);
            setTimeout(() => { note.classList.remove('translate-x-full'); }, 100);
            setTimeout(() => { note.classList.add('translate-x-full'); setTimeout(() => note.remove(), 300); }, 3000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadUsers();
        });
    </script>

<?php include '../../includes/footer.php'; ?>



<?php
$pageTitle = "Report Generation";
$bodyClass = "bg-background";
require_once __DIR__ . '/../../database/auth.php';
require_role(['secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <section class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary">Report Generation</h1>
                    <p class="mt-2 text-text-secondary">Create comprehensive reports from your activity data</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button type="button" class="btn-secondary" onclick="loadSavedReport()">
                        <i class="fas fa-folder-open mr-2"></i>
                        Load Saved
                    </button>
                    <button type="button" class="btn-primary" onclick="saveReportTemplate()">
                        <i class="fas fa-save mr-2"></i>
                        Save Template
                    </button>
                </div>
            </div>
        </section>

        <!-- Report Builder Layout -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Configuration Panel -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Activity Selection -->
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h2 class="text-xl font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-tasks text-primary mr-2"></i>
                        Activity Selection
                    </h2>
                    
                    <!-- Search and Filters -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">Period</label>
                                <select class="form-input text-sm" onchange="filterByPeriod(this.value)">
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annual</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">Agency</label>
                                <select class="form-input text-sm" onchange="filterByAgency(this.value)">
                                    <option value="all">All Agencies</option>
                                    <option value="PCAARRD">PCAARRD</option>
                                    <option value="DOST-IX">DOST-IX</option>
                                    <option value="DA-RFO IX">DA-RFO IX</option>
                                    <option value="WMSU">WMSU</option>
                                    <option value="JHCSC">JHCSC</option>
                                    <option value="DTI-IX">DTI-IX</option>
                                    <option value="BFAR-IX">BFAR-IX</option>
                                    <option value="NEDA-IX">NEDA-IX</option>
                                    <option value="PRRI-IX">PRRI-IX</option>
                                    <option value="PhilFIDA-IX">PhilFIDA-IX</option>
                                    <option value="DA-BAR">DA-BAR</option>
                                    <option value="PCA-ZRC">PCA-ZRC</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">Cluster</label>
                                <select class="form-input text-sm" onchange="filterByCluster(this.value)">
                                    <option value="all">All Clusters</option>
                                    <option value="ICTC">ICTC</option>
                                    <option value="RDC">R&D (RDC)</option>
                                    <option value="SCC">SCC</option>
                                    <option value="TTC">Tech Trans (TTC)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </section>


                <!-- Export Options -->
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h2 class="text-xl font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-download text-primary mr-2"></i>
                        Export Options
                    </h2>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" class="export-format-btn border-2 border-error bg-error-50 text-error p-3 rounded-lg text-center hover:bg-error-100 transition-micro">
                                <i class="fas fa-file-pdf text-xl mb-2"></i>
                                <div class="text-sm font-medium">PDF</div>
                            </button>
                            <button type="button" class="export-format-btn border-2 border-secondary-200 p-3 rounded-lg text-center hover:border-success hover:bg-success-50 hover:text-success transition-micro">
                                <i class="fas fa-file-excel text-xl mb-2"></i>
                                <div class="text-sm font-medium">Excel</div>
                            </button>
                            <button type="button" class="export-format-btn border-2 border-secondary-200 p-3 rounded-lg text-center hover:border-primary hover:bg-primary-50 hover:text-primary transition-micro">
                                <i class="fas fa-file-word text-xl mb-2"></i>
                                <div class="text-sm font-medium">Word</div>
                            </button>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">Report Title</label>
                                <input type="text" class="form-input" placeholder="Monthly Activity Report - December 2024" value="Monthly Activity Report - December 2024" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-text-primary mb-2">Author</label>
                                <input type="text" class="form-input" placeholder="Your Name" value="Sarah Johnson" />
                            </div>
                        </div>

                        <!-- Generate Button -->
                        <button type="button" class="btn-primary w-full py-3 text-lg" onclick="generateAIReport()">
                            <i class="fas fa-robot mr-2"></i>
                            Generate AI Narrative Report
                        </button>
                        <p class="text-xs text-text-secondary mt-2 text-center">
                            <i class="fas fa-magic mr-1"></i>Powered by AI - Generates professional narrative from your activities
                        </p>
                    </div>
                </section>
            </div>

            <!-- Preview Section -->
            <div class="xl:col-span-2">
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 h-full">
                    <div class="p-6 border-b border-secondary-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-text-primary flex items-center">
                                <i class="fas fa-eye text-primary mr-2"></i>
                                Report Preview
                            </h2>
                        </div>
                    </div>

                    <!-- Preview Content -->
                    <div class="p-6">
                        <div id="report-preview" class="bg-white border border-secondary-200 rounded-lg shadow-sm min-h-96">
                            <!-- Report Header -->
                            <div class="p-8 border-b border-secondary-200">
                                <div class="text-center mb-6">
                                    <h1 class="text-3xl font-bold text-text-primary mb-2">Monthly Activity Report</h1>
                                    <p class="text-lg text-text-secondary">December 2024</p>
                                    <div class="flex justify-center items-center mt-4 text-sm text-text-secondary">
                                        <span>Generated by Sarah Johnson</span>
                                        <span class="mx-2">•</span>
                                        <span>January 4, 2025</span>
                                    </div>
                                </div>
                                
                                <!-- Executive Summary -->
                                <div class="bg-primary-50 rounded-lg p-6">
                                    <h2 class="text-xl font-semibold text-text-primary mb-4">Executive Summary</h2>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-primary">4</div>
                                            <div class="text-sm text-text-secondary">Total Activities</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-success">2</div>
                                            <div class="text-sm text-text-secondary">Completed</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-warning">2</div>
                                            <div class="text-sm text-text-secondary">In Progress</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Details -->
                            <div class="p-8">
                                <h2 class="text-xl font-semibold text-text-primary mb-6">Activity Details</h2>
                                
                                <!-- Activity 1 -->
                                <div class="mb-8 pb-6 border-b border-secondary-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-text-primary">Public Health Initiative</h3>
                                        <span class="status-badge status-success">Completed</span>
                                    </div>
                                    <p class="text-text-secondary mb-4">Community vaccination program implementation across 15 health centers with focus on rural areas and underserved populations.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-text-primary">Department:</span>
                                            <span class="text-text-secondary ml-1">Health</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-text-primary">Date:</span>
                                            <span class="text-text-secondary ml-1">December 15, 2024</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-text-primary">Accomplishment:</span>
                                            <span class="text-success ml-1">95% target achieved</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Activity 2 -->
                                <div class="mb-8 pb-6 border-b border-secondary-200">
                                    <div class="flex items-start justify-between mb-3">
                                        <h3 class="text-lg font-medium text-text-primary">Infrastructure Assessment</h3>
                                        <span class="status-badge status-success">Completed</span>
                                    </div>
                                    <p class="text-text-secondary mb-4">Comprehensive evaluation of road and bridge conditions across the metropolitan area to identify priority maintenance needs.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-text-primary">Department:</span>
                                            <span class="text-text-secondary ml-1">Transportation</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-text-primary">Date:</span>
                                            <span class="text-text-secondary ml-1">December 18, 2024</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-text-primary">Accomplishment:</span>
                                            <span class="text-success ml-1">Assessment complete</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Department Summary -->
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-text-primary mb-4">Department Summary</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-secondary-50 rounded-lg p-4">
                                            <h4 class="font-medium text-text-primary mb-2">Health Department</h4>
                                            <div class="text-sm text-text-secondary">
                                                <div>Activities: 1</div>
                                                <div>Completion Rate: 100%</div>
                                            </div>
                                        </div>
                                        <div class="bg-secondary-50 rounded-lg p-4">
                                            <h4 class="font-medium text-text-primary mb-2">Transportation</h4>
                                            <div class="text-sm text-text-secondary">
                                                <div>Activities: 1</div>
                                                <div>Completion Rate: 100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Report Footer -->
                            <div class="p-8 border-t border-secondary-200 bg-secondary-50">
                                <div class="text-center text-sm text-text-secondary">
                                    <p>© 2025 Central CMI. All Rights Reserved.</p>
                                    <p class="mt-1">Government Activity Management System</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="text-sm text-text-secondary">
                                Page 1 of 1
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button" class="p-2 text-secondary-400 hover:text-primary transition-micro" disabled>
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="p-2 text-secondary-400 hover:text-primary transition-micro" disabled>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <!-- Generation Progress Modal -->
        <div id="generation-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4 p-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-primary-100 p-3 rounded-full">
                            <i class="fas fa-cog fa-spin text-2xl text-primary"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Generating Report</h3>
                    <p class="text-text-secondary mb-6">Please wait while we compile your report...</p>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-secondary-200 rounded-full h-2 mb-4">
                        <div id="progress-bar" class="bg-primary h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    
                    <div class="text-sm text-text-secondary">
                        <span id="progress-text">Initializing...</span>
                        <span class="ml-2">(<span id="progress-percent">0</span>%)</span>
                    </div>
                    
                    <div class="mt-6">
                        <button type="button" class="btn-secondary" onclick="cancelGeneration()">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4 p-6">
                <div class="text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-success-100 p-3 rounded-full">
                            <i class="fas fa-check-circle text-2xl text-success"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-text-primary mb-2">Report Generated Successfully!</h3>
                    <p class="text-text-secondary mb-6">Your report has been generated and is ready for download.</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button" class="btn-primary flex-1" onclick="downloadReport()">
                            <i class="fas fa-download mr-2"></i>
                            Download
                        </button>
                        <button type="button" class="btn-secondary flex-1" onclick="closeSuccessModal()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const API_URL = BASE_URL + 'api/generate-report.php';
        let generatedReportUrl = null;
        let abortController = null;

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Get current filter values
        function getFilters() {
            return {
                period: document.querySelector('select[onchange*="filterByPeriod"]')?.value || 'monthly',
                agency: document.querySelector('select[onchange*="filterByAgency"]')?.value || 'all',
                cluster: document.querySelector('select[onchange*="filterByCluster"]')?.value || 'all',
                title: document.querySelector('input[placeholder*="Report Title"]')?.value || 'Activity Report',
                author: document.querySelector('input[placeholder*="Your Name"]')?.value || 'WESMAARRDEC'
            };
        }

        // Filter stubs for new filters
        function filterByPeriod(value) {
            console.log('Filtering by period:', value);
            updatePreviewTitle();
        }

        function filterByAgency(value) {
            console.log('Filtering by agency:', value);
        }

        function filterByCluster(value) {
            console.log('Filtering by cluster:', value);
        }

        function updatePreviewTitle() {
            const period = document.querySelector('select[onchange*="filterByPeriod"]')?.value || 'monthly';
            const periodLabels = {
                'monthly': 'Monthly',
                'quarterly': 'Quarterly', 
                'annually': 'Annual'
            };
            const titleInput = document.querySelector('input[placeholder*="Report Title"]');
            if (titleInput) {
                const now = new Date();
                const month = now.toLocaleString('default', { month: 'long' });
                const year = now.getFullYear();
                titleInput.value = `${periodLabels[period]} Activity Report - ${month} ${year}`;
            }
        }

        // Template selection handling
        document.addEventListener('DOMContentLoaded', function() {
            // Export format selection - set Word as default
            const exportBtns = document.querySelectorAll('.export-format-btn');
            exportBtns.forEach(btn => {
                btn.classList.remove('border-error', 'bg-error-50', 'text-error');
                btn.classList.add('border-secondary-200');
                
                if (btn.querySelector('.fa-file-word')) {
                    btn.classList.remove('border-secondary-200');
                    btn.classList.add('border-primary', 'bg-primary-50', 'text-primary');
                }
                
                btn.addEventListener('click', function() {
                    exportBtns.forEach(b => {
                        b.classList.remove('border-error', 'bg-error-50', 'text-error');
                        b.classList.remove('border-success', 'bg-success-50', 'text-success');
                        b.classList.remove('border-primary', 'bg-primary-50', 'text-primary');
                        b.classList.add('border-secondary-200');
                    });
                    
                    if (btn.querySelector('.fa-file-pdf')) {
                        btn.classList.add('border-error', 'bg-error-50', 'text-error');
                    } else if (btn.querySelector('.fa-file-excel')) {
                        btn.classList.add('border-success', 'bg-success-50', 'text-success');
                    } else {
                        btn.classList.add('border-primary', 'bg-primary-50', 'text-primary');
                    }
                });
            });

            updatePreviewTitle();
        });

        // Generate AI Report
        async function generateAIReport() {
            const filters = getFilters();
            
            // Show generation modal
            document.getElementById('generation-modal').classList.remove('hidden');
            
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const progressPercent = document.getElementById('progress-percent');
            
            // Update progress UI
            function updateProgress(percent, text) {
                progressBar.style.width = percent + '%';
                progressPercent.textContent = Math.round(percent);
                progressText.textContent = text;
            }
            
            try {
                updateProgress(10, 'Fetching activity data...');
                
                // Small delay for UX
                await new Promise(r => setTimeout(r, 500));
                updateProgress(25, 'Sending data to AI...');
                
                // Call the API
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(filters)
                });
                
                updateProgress(50, 'AI is generating narrative...');
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.error || 'Failed to generate report');
                }
                
                updateProgress(75, 'Creating DOCX file...');
                await new Promise(r => setTimeout(r, 500));
                
                updateProgress(90, 'Finalizing report...');
                await new Promise(r => setTimeout(r, 300));
                
                // Store the download URL
                generatedReportUrl = result.download_url;
                
                // Update preview with narrative
                updatePreviewWithNarrative(result.narrative, filters.title);
                
                updateProgress(100, 'Complete!');
                
                setTimeout(() => {
                    document.getElementById('generation-modal').classList.add('hidden');
                    document.getElementById('success-modal').classList.remove('hidden');
                }, 500);
                
            } catch (error) {
                console.error('Error generating report:', error);
                document.getElementById('generation-modal').classList.add('hidden');
                alert('Error generating report: ' + error.message);
            }
        }

        // Update preview with AI-generated narrative
        function updatePreviewWithNarrative(narrative, title) {
            const preview = document.getElementById('report-preview');
            const date = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            const author = document.querySelector('input[placeholder*="Your Name"]')?.value || 'WESMAARRDEC';
            
            // Convert markdown-style formatting to HTML
            let formattedNarrative = narrative
                .replace(/^### (.+)$/gm, '<h3 class="text-lg font-semibold text-text-primary mt-6 mb-3">$1</h3>')
                .replace(/^## (.+)$/gm, '<h2 class="text-xl font-bold text-primary mt-8 mb-4">$1</h2>')
                .replace(/^# (.+)$/gm, '<h1 class="text-2xl font-bold text-text-primary mt-8 mb-4">$1</h1>')
                .replace(/^\d+\.\s*([A-Z][A-Z\s]+)/gm, '<h2 class="text-xl font-bold text-primary mt-8 mb-4">$1</h2>')
                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                .replace(/^[-•]\s(.+)$/gm, '<li class="ml-4">$1</li>')
                .replace(/\n\n/g, '</p><p class="text-text-secondary mb-4">')
                .replace(/\n/g, '<br>');
            
            preview.innerHTML = `
                <div class="p-8 border-b border-secondary-200">
                    <div class="text-center mb-6">
                        <h1 class="text-3xl font-bold text-text-primary mb-2">${escapeHtml(title)}</h1>
                        <p class="text-lg text-text-secondary">AI-Generated Narrative Report</p>
                        <div class="flex justify-center items-center mt-4 text-sm text-text-secondary">
                            <span>Generated by ${escapeHtml(author)}</span>
                            <span class="mx-2">•</span>
                            <span>${date}</span>
                        </div>
                        <div class="mt-4 flex justify-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary">
                                <i class="fas fa-robot mr-1"></i> Powered by AI
                            </span>
                            <button onclick="downloadReport()" class="inline-flex items-center px-4 py-1 rounded-full text-xs font-medium bg-success text-white hover:bg-success-700">
                                <i class="fas fa-download mr-1"></i> Download DOCX
                            </button>
                        </div>
                    </div>
                </div>
                <div class="p-8 prose max-w-none">
                    <p class="text-text-secondary mb-4">${formattedNarrative}</p>
                </div>
                <div class="p-8 border-t border-secondary-200 bg-secondary-50">
                    <div class="text-center text-sm text-text-secondary">
                        <p>© ${new Date().getFullYear()} Central CMI - WESMAARRDEC. All Rights Reserved.</p>
                    </div>
                </div>
            `;
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Cancel generation
        function cancelGeneration() {
            if (abortController) {
                abortController.abort();
            }
            document.getElementById('generation-modal').classList.add('hidden');
        }

        // Download report
        function downloadReport() {
            if (generatedReportUrl) {
                // Create temporary link and trigger download
                const link = document.createElement('a');
                link.href = generatedReportUrl;
                link.target = '_blank';
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                setTimeout(() => document.body.removeChild(link), 100);
            } else {
                alert('No report available for download. Please generate a report first.');
            }
            closeSuccessModal();
        }

        // Close success modal
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.add('hidden');
        }

        // Save report template
        function saveReportTemplate() {
            alert('Report template saved successfully!');
        }

        // Load saved report
        function loadSavedReport() {
            alert('Loading saved report templates...');
        }
    </script>

<?php include '../../includes/footer.php'; ?>
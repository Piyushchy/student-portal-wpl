<!-- Footer -->
    <footer class="bg-white border-t p-6 md:p-8 mt-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-semibold mb-3">Contact Information</h3>
                    <address class="not-italic text-sm text-gray-600 space-y-1">
                        <p>University Student Services</p>
                        <p>123 Campus Drive</p>
                        <p>Email: support@university.edu</p>
                        <p>Phone: (555) 123-4567</p>
                    </address>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Quick Links</h3>
                    <ul class="text-sm space-y-2">
                        <li><a href="#" class="text-gray-600 hover:text-blue-600">Academic Calendar</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600">Student Handbook</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600">Library Resources</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600">IT Support</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Connect With Us</h3>
                    <div class="flex gap-4">
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                            <span class="sr-only">Facebook</span>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                            <span class="sr-only">Instagram</span>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                            </svg>
                            <span class="sr-only">Twitter</span>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                            <span class="sr-only">LinkedIn</span>
                        </a>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        <p>© <?php echo date('Y'); ?> University Name. All rights reserved.</p>
                        <div class="mt-2 flex gap-4">
                            <a href="#" class="hover:text-blue-600">Privacy Policy</a>
                            <a href="#" class="hover:text-blue-600">Terms of Use</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const closeMobileMenuButton = document.getElementById('close-mobile-menu');
        
        if (mobileMenuButton && mobileMenu && mobileMenuOverlay && closeMobileMenuButton) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.add('open');
                mobileMenuOverlay.classList.add('open');
            });
            
            closeMobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.remove('open');
                mobileMenuOverlay.classList.remove('open');
            });
            
            mobileMenuOverlay.addEventListener('click', function() {
                mobileMenu.classList.remove('open');
                mobileMenuOverlay.classList.remove('open');
            });
        }
        
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        if (tabButtons.length > 0 && tabContents.length > 0) {
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding content
                    this.classList.add('active');
                    document.getElementById(`${tabName}-tab`).classList.add('active');
                });
            });
        }
    </script>
</body>
</html>


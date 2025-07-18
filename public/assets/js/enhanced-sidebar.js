/* =====================================================
   Enhanced Sidebar JavaScript for Accounting System 2025
   ===================================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize sidebar enhancements
    initializeSidebar();
    
    function initializeSidebar() {
        initializeSearch();
        initializeNavigation();
        initializeMobileSupport();
        initializeTooltips();
        initializeAnimations();
        initializeKeyboardShortcuts();
    }

    // Search Functionality
    function initializeSearch() {
        const searchInput = document.getElementById('sidebarSearch');
        const searchResults = document.getElementById('searchResults');
        
        if (!searchInput || !searchResults) return;

        // Navigation items data for search
        const navItems = [
            { text: 'الرئيسية', icon: 'fas fa-tachometer-alt', url: '/dashboard', category: 'dashboard' },
            { text: 'قائمة الفئات', icon: 'far fa-folder', url: '/accounts', category: 'accounts' },
            { text: 'قائمة الحسابات', icon: 'far fa-list-alt', url: '/accounts/real', category: 'accounts' },
            { text: 'إضافة فئة', icon: 'far fa-folder-open', url: '/accounts/create-group', category: 'accounts' },
            { text: 'إضافة حساب', icon: 'far fa-plus-square', url: '/accounts/create-account', category: 'accounts' },
            { text: 'دليل الحسابات', icon: 'far fa-chart-bar', url: '/accounts/chart', category: 'accounts' },
            { text: 'سندات القبض', icon: 'far fa-arrow-alt-circle-down', url: '/vouchers?type=receipt', category: 'vouchers' },
            { text: 'سندات الدفع', icon: 'far fa-arrow-alt-circle-up', url: '/vouchers?type=payment', category: 'vouchers' },
            { text: 'سندات التحويل', icon: 'fas fa-exchange-alt', url: '/vouchers?type=transfer', category: 'vouchers' },
            { text: 'القيود المحاسبية', icon: 'fas fa-book', url: '/journal-entries', category: 'accounting' },
            { text: 'دفتر الأستاذ', icon: 'fas fa-book-open', url: '/ledger', category: 'accounting' },
            { text: 'العملات', icon: 'fas fa-coins', url: '/currencies', category: 'system' },
            { text: 'الفواتير', icon: 'fas fa-receipt', url: '/invoices', category: 'invoices' },
            { text: 'العملاء', icon: 'fas fa-users', url: '/customers', category: 'contacts' },
            { text: 'الأصناف', icon: 'fas fa-box-open', url: '/items', category: 'inventory' },
            { text: 'الموظفين', icon: 'fas fa-user-tie', url: '/employees', category: 'hr' },
            { text: 'الرواتب', icon: 'fas fa-money-bill-wave', url: '/salaries', category: 'hr' },
            { text: 'دفع الراتب', icon: 'fas fa-money-check-alt', url: '/salary-payments', category: 'hr' },
            { text: 'كشوف الرواتب', icon: 'fas fa-file-invoice-dollar', url: '/salary-batches', category: 'hr' },
            { text: 'الأدوار', icon: 'fas fa-user-shield', url: '/admin/roles', category: 'admin' },
            { text: 'الصلاحيات', icon: 'fas fa-key', url: '/admin/permissions', category: 'admin' },
            { text: 'المستخدمين', icon: 'fas fa-users', url: '/admin/users', category: 'admin' },
            { text: 'الإعدادات المحاسبية', icon: 'fas fa-cogs', url: '/accounting-settings/edit', category: 'settings' },
            { text: 'ميزان المراجعة', icon: 'fas fa-balance-scale', url: '/reports/trial-balance', category: 'reports' },
            { text: 'الميزانية العمومية', icon: 'fas fa-file-invoice-dollar', url: '/reports/balance-sheet', category: 'reports' },
            { text: 'قائمة الدخل', icon: 'fas fa-chart-line', url: '/reports/income-statement', category: 'reports' },
            { text: 'تقرير الرواتب', icon: 'fas fa-money-check-alt', url: '/reports/payroll', category: 'reports' },
            { text: 'المصروفات والإيرادات', icon: 'fas fa-receipt', url: '/reports/expenses-revenues', category: 'reports' }
        ];

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                hideSearchResults();
                return;
            }

            searchTimeout = setTimeout(() => {
                performSearch(query, navItems, searchResults);
            }, 300);
        });

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                hideSearchResults();
            }
        });

        // Handle keyboard navigation
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideSearchResults();
                this.blur();
            }
        });
    }

    function performSearch(query, navItems, searchResults) {
        const results = navItems.filter(item => 
            item.text.includes(query) || 
            item.category.includes(query)
        ).slice(0, 5);

        if (results.length === 0) {
            showNoResults(searchResults);
            return;
        }

        displaySearchResults(results, searchResults);
    }

    function displaySearchResults(results, container) {
        const resultsHTML = results.map(item => `
            <a href="${item.url}" class="search-result-item">
                <i class="${item.icon}"></i>
                <span>${item.text}</span>
            </a>
        `).join('');

        container.innerHTML = resultsHTML;
        container.style.display = 'block';
        container.classList.add('animate-fade-in');
    }

    function showNoResults(container) {
        container.innerHTML = `
            <div class="search-result-item text-muted">
                <i class="fas fa-search"></i>
                <span>لا توجد نتائج</span>
            </div>
        `;
        container.style.display = 'block';
    }

    function hideSearchResults() {
        const searchResults = document.getElementById('searchResults');
        if (searchResults) {
            searchResults.style.display = 'none';
            searchResults.classList.remove('animate-fade-in');
        }
    }

    // Enhanced Navigation
    function initializeNavigation() {
        const navLinks = document.querySelectorAll('.nav-link');
        const treeViewItems = document.querySelectorAll('.has-treeview');

        // Add hover effects to navigation links
        navLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.background = 'linear-gradient(90deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.05) 100%)';
                }
            });

            link.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.background = '';
                }
            });
        });

        // Enhanced TreeView functionality
        treeViewItems.forEach(item => {
            const mainLink = item.querySelector('.nav-link');
            const treeview = item.querySelector('.nav-treeview');
            
            if (mainLink && treeview) {
                mainLink.addEventListener('click', function(e) {
                    if (this.getAttribute('href') === '#') {
                        e.preventDefault();
                        toggleTreeView(item, treeview);
                    }
                });
            }
        });

        // Add loading state simulation for navigation
        navLinks.forEach(link => {
            if (link.getAttribute('href') !== '#') {
                link.addEventListener('click', function() {
                    if (!this.classList.contains('active')) {
                        this.classList.add('loading');
                        setTimeout(() => {
                            this.classList.remove('loading');
                        }, 500);
                    }
                });
            }
        });
    }

    function toggleTreeView(item, treeview) {
        const isOpen = item.classList.contains('menu-open');
        
        if (isOpen) {
            // Close
            item.classList.remove('menu-open');
            treeview.style.maxHeight = '0';
        } else {
            // Open
            item.classList.add('menu-open');
            treeview.style.maxHeight = treeview.scrollHeight + 'px';
        }
    }

    // Mobile Support
    function initializeMobileSupport() {
        const sidebar = document.querySelector('.main-sidebar');
        const backdrop = document.querySelector('.sidebar-backdrop');
        
        if (window.innerWidth <= 768) {
            // Mobile-specific enhancements
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    toggleMobileSidebar();
                });
            }

            // Swipe gesture support
            let startX, startY, currentX, currentY;
            
            sidebar.addEventListener('touchstart', function(e) {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            });

            sidebar.addEventListener('touchmove', function(e) {
                currentX = e.touches[0].clientX;
                currentY = e.touches[0].clientY;
            });

            sidebar.addEventListener('touchend', function(e) {
                const diffX = startX - currentX;
                const diffY = Math.abs(startY - currentY);
                
                // Swipe right to close sidebar
                if (diffX > 50 && diffY < 100) {
                    toggleMobileSidebar();
                }
            });
        }
    }

    function toggleMobileSidebar() {
        document.body.classList.toggle('sidebar-open');
    }

    // Tooltips for collapsed sidebar
    function initializeTooltips() {
        const navIcons = document.querySelectorAll('.nav-icon');
        
        navIcons.forEach(icon => {
            const navItem = icon.closest('.nav-item');
            const navLink = navItem.querySelector('.nav-link');
            const text = navLink.querySelector('p');
            
            if (text) {
                icon.setAttribute('title', text.textContent.trim());
            }
        });
    }

    // Animation enhancements
    function initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-slide-in');
                }
            });
        }, observerOptions);

        // Observe navigation items
        document.querySelectorAll('.nav-item').forEach(item => {
            observer.observe(item);
        });

        // Staggered animation for nav items
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1}s`;
        });
    }

    // Keyboard Shortcuts
    function initializeKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('sidebarSearch');
                if (searchInput) {
                    searchInput.focus();
                }
            }

            // Escape to close search
            if (e.key === 'Escape') {
                hideSearchResults();
                document.getElementById('sidebarSearch')?.blur();
            }

            // Quick navigation shortcuts
            if (e.altKey) {
                switch(e.key) {
                    case '1':
                        e.preventDefault();
                        navigateTo('/dashboard');
                        break;
                    case '2':
                        e.preventDefault();
                        navigateTo('/accounts');
                        break;
                    case '3':
                        e.preventDefault();
                        navigateTo('/vouchers');
                        break;
                    case '4':
                        e.preventDefault();
                        navigateTo('/journal-entries');
                        break;
                }
            }
        });
    }

    function navigateTo(url) {
        window.location.href = url;
    }

    // Badge animations
    function animateBadges() {
        const badges = document.querySelectorAll('.nav-badge');
        badges.forEach(badge => {
            badge.addEventListener('animationend', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = '';
                }, 100);
            });
        });
    }

    // Real-time badge updates (if needed)
    function updateBadges() {
        // This can be connected to WebSocket or polling for real-time updates
        // Example: Update notification counts
        
        setInterval(() => {
            // Simulate real-time updates
            const badges = document.querySelectorAll('.nav-badge');
            badges.forEach(badge => {
                if (Math.random() > 0.95) { // 5% chance of update
                    badge.classList.add('bounce');
                    setTimeout(() => {
                        badge.classList.remove('bounce');
                    }, 2000);
                }
            });
        }, 30000); // Check every 30 seconds
    }

    // Theme toggler (if needed)
    function initializeThemeToggler() {
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                document.documentElement.setAttribute(
                    'data-theme',
                    document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'
                );
                localStorage.setItem('theme', document.documentElement.getAttribute('data-theme'));
            });
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    }

    // Responsive sidebar handling
    function handleResize() {
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                document.body.classList.remove('sidebar-open');
            }
        });
    }

    // Initialize additional features
    animateBadges();
    updateBadges();
    initializeThemeToggler();
    handleResize();

    // Add CSS for animations if not already present
    if (!document.getElementById('sidebar-animations')) {
        const style = document.createElement('style');
        style.id = 'sidebar-animations';
        style.textContent = `
            .ripple-effect {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.3s linear;
                pointer-events: none;
            }

            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }

            .bounce {
                animation: bounce 0.5s ease-in-out 3;
            }

            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-3px); }
            }

            .nav-item {
                opacity: 0;
                animation: slideInFromRight 0.5s ease forwards;
            }

            @keyframes slideInFromRight {
                from {
                    opacity: 0;
                    transform: translateX(20px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .search-result-item {
                opacity: 0;
                animation: fadeInUp 0.3s ease forwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    }
});

// Utility function to toggle sidebar (for external use)
window.toggleSidebar = function() {
    const sidebar = document.querySelector('.main-sidebar');
    const body = document.body;
    
    if (window.innerWidth <= 768) {
        body.classList.toggle('sidebar-open');
    } else {
        body.classList.toggle('sidebar-collapse');
    }
};

// Export functions for external use
window.SidebarEnhancements = {
    search: function(query) {
        const searchInput = document.getElementById('sidebarSearch');
        if (searchInput) {
            searchInput.value = query;
            searchInput.dispatchEvent(new Event('input'));
        }
    },
    
    navigate: function(url) {
        window.location.href = url;
    },
    
    highlightNavItem: function(selector) {
        const item = document.querySelector(selector);
        if (item) {
            item.classList.add('glow-effect');
            setTimeout(() => {
                item.classList.remove('glow-effect');
            }, 2000);
        }
    }
}; 
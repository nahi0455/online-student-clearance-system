// Student Portal Navigation Enhancement Script
document.addEventListener('DOMContentLoaded', function() {
    // Get current page path
    const currentPath = window.location.pathname;
    const currentPage = currentPath.split('/').pop();
    
    // Remove active class from all nav items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add active class based on current page
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        const linkPage = href.split('/').pop();
        
        // Check if current page matches link
        if (currentPage === linkPage || 
            (currentPage === 'index.php' && href === 'index.php') ||
            (currentPath.includes('student/') && href.includes(currentPage))) {
            link.closest('.nav-item').classList.add('active');
        }
    });
    
    // Enhanced navigation interactions
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default for actual navigation
            
            // Add click animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            // Update active state
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            this.closest('.nav-item').classList.add('active');
        });
        
        // Enhanced hover effects
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
    
    // Dynamic notification badge update
    const updateNotificationBadge = () => {
        const badge = document.querySelector('.nav-badge');
        if (badge) {
            // Simulate getting notification count from server
            fetch('../get_notification_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {
                    // Fallback to random count for demo
                    const count = Math.floor(Math.random() * 5) + 1;
                    badge.textContent = count;
                });
        }
    };
    
    // Update notification badge every 30 seconds
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);
    
    // Breadcrumb generation
    const generateBreadcrumb = () => {
        const breadcrumbContainer = document.querySelector('.breadcrumb-container');
        if (!breadcrumbContainer) return;
        
        const pathSegments = currentPath.split('/').filter(segment => segment);
        const breadcrumbs = ['<a href="index.php"><i class="fa fa-home"></i> Dashboard</a>'];
        
        if (pathSegments.includes('student')) {
            const pageName = currentPage.replace('.php', '').replace('_', ' ');
            const formattedName = pageName.charAt(0).toUpperCase() + pageName.slice(1);
            breadcrumbs.push(`<span>${formattedName}</span>`);
        }
        
        breadcrumbContainer.innerHTML = breadcrumbs.join(' <i class="fa fa-chevron-right"></i> ');
    };
    
    generateBreadcrumb();
    
    // Add loading states for navigation
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (this.getAttribute('href').startsWith('student/') || this.getAttribute('href') === 'index.php') {
                // Add loading indicator
                const originalContent = this.innerHTML;
                const icon = this.querySelector('.nav-icon i');
                if (icon) {
                    icon.className = 'fa fa-spinner fa-spin';
                }
                
                // Reset after navigation (this won't execute if page changes)
                setTimeout(() => {
                    this.innerHTML = originalContent;
                }, 2000);
            }
        });
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.altKey) {
            switch(e.key) {
                case '1':
                    e.preventDefault();
                    window.location.href = 'index.php';
                    break;
                case '2':
                    e.preventDefault();
                    window.location.href = 'Clearance_Status.php';
                    break;
                case '3':
                    e.preventDefault();
                    window.location.href = 'profile.php';
                    break;
                case '4':
                    e.preventDefault();
                    window.location.href = 'documents.php';
                    break;
                case '5':
                    e.preventDefault();
                    window.location.href = 'notifications.php';
                    break;
                case '6':
                    e.preventDefault();
                    window.location.href = 'support.php';
                    break;
                case '7':
                    e.preventDefault();
                    window.location.href = 'law.php';
                    break;
            }
        }
    });
    
    // Add tooltips for keyboard shortcuts
    const addKeyboardTooltips = () => {
        const shortcuts = [
            { selector: 'a[href="index.php"]', shortcut: 'Alt+1' },
            { selector: 'a[href="Clearance_Status.php"]', shortcut: 'Alt+2' },
            { selector: 'a[href="profile.php"]', shortcut: 'Alt+3' },
            { selector: 'a[href="documents.php"]', shortcut: 'Alt+4' },
            { selector: 'a[href="notifications.php"]', shortcut: 'Alt+5' },
            { selector: 'a[href="support.php"]', shortcut: 'Alt+6' },
            { selector: 'a[href="law.php"]', shortcut: 'Alt+7' }
        ];
        
        shortcuts.forEach(item => {
            const element = document.querySelector(item.selector);
            if (element) {
                element.setAttribute('title', `${element.textContent.trim()} (${item.shortcut})`);
            }
        });
    };
    
    addKeyboardTooltips();
    
    console.log('🎓 Student Portal Navigation Enhanced');
    console.log('Keyboard shortcuts: Alt+1-7 for quick navigation');
});

// Utility function to show toast notifications
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="fa fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="toast-close">×</button>
        </div>
    `;
    
    // Add toast styles
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 10000;
        animation: slideInFromRight 0.3s ease-out;
        min-width: 300px;
        max-width: 500px;
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove
    setTimeout(() => {
        toast.style.animation = 'slideOutToRight 0.3s ease-in';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, duration);
}

// Add CSS for toast animations
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    @keyframes slideInFromRight {
        0% { opacity: 0; transform: translateX(100%); }
        100% { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOutToRight {
        0% { opacity: 1; transform: translateX(0); }
        100% { opacity: 0; transform: translateX(100%); }
    }
    .toast-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .toast-close {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        margin-left: auto;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
`;
document.head.appendChild(toastStyles);
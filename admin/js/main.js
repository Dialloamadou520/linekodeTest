// Main JavaScript pour l'administration Linekode
// Charge tous les modules dynamiques

// Vérifier si l'utilisateur est connecté
function checkAuth() {
    if (!localStorage.getItem('adminLoggedIn')) {
        window.location.href = 'login.html';
        return false;
    }
    return true;
}

// Déconnexion
function logout() {
    localStorage.removeItem('adminLoggedIn');
    window.location.href = 'login.html';
}

// Initialiser l'administration
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier l'authentification pour toutes les pages sauf login
    if (!window.location.pathname.includes('login.html')) {
        checkAuth();
    }

    // Charger le système d'administration
    if (typeof window.adminSystem === 'undefined') {
        const script = document.createElement('script');
        script.src = 'js/admin-core.js';
        script.onload = () => {
            console.log('Système d\'administration chargé');
            initializeAdmin();
        };
        document.head.appendChild(script);
    } else {
        initializeAdmin();
    }
    
    // Charger le script de réinitialisation
    const resetScript = document.createElement('script');
    resetScript.src = 'reset-data.js';
    document.head.appendChild(resetScript);
});

function initializeAdmin() {
    // Ajouter les fonctionnalités communes
    setupCommonFeatures();
    setupKeyboardShortcuts();
    setupTheme();
    setupNotifications();
}

function setupCommonFeatures() {
    // Bouton d'export de données
    const exportBtn = document.querySelector('.export-btn');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            window.adminSystem.exportData();
        });
    }

    // Bouton d'import de données
    const importBtn = document.querySelector('.import-btn');
    if (importBtn) {
        importBtn.addEventListener('click', () => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            input.onchange = (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.onload = (event) => {
                    const success = window.adminSystem.importData(event.target.result);
                    if (success) {
                        showNotification('Données importées avec succès', 'success');
                        location.reload();
                    } else {
                        showNotification('Erreur lors de l\'import des données', 'error');
                    }
                };
                reader.readAsText(file);
            };
            input.click();
        });
    }

    // Bouton de synchronisation
    const syncBtn = document.querySelector('.sync-btn');
    if (syncBtn) {
        syncBtn.addEventListener('click', () => {
            syncBtn.disabled = true;
            syncBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Synchronisation...';
            
            setTimeout(() => {
                syncBtn.disabled = false;
                syncBtn.innerHTML = '<i class="fas fa-sync"></i> Synchroniser';
                showNotification('Données synchronisées', 'success');
            }, 2000);
        });
    }
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + S pour sauvegarder
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const saveBtn = document.querySelector('.btn-primary[type="submit"]');
            if (saveBtn) {
                saveBtn.click();
            }
        }

        // Ctrl/Cmd + E pour exporter
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            const exportBtn = document.querySelector('.export-btn');
            if (exportBtn) {
                exportBtn.click();
            }
        }

        // Échap pour fermer les modaux
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (modal.style.display === 'block') {
                    modal.style.display = 'none';
                }
            });
        }
    });
}

function setupTheme() {
    // Vérifier le thème sauvegardé
    const savedTheme = localStorage.getItem('adminTheme') || 'light';
    document.body.setAttribute('data-theme', savedTheme);

    // Ajouter le sélecteur de thème
    const themeToggle = document.createElement('button');
    themeToggle.className = 'theme-toggle';
    themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    themeToggle.title = 'Changer le thème';
    themeToggle.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #0284c7;
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: all 0.3s ease;
    `;

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.body.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        document.body.setAttribute('data-theme', newTheme);
        localStorage.setItem('adminTheme', newTheme);
        
        themeToggle.innerHTML = newTheme === 'light' ? 
            '<i class="fas fa-moon"></i>' : 
            '<i class="fas fa-sun"></i>';
        
        showNotification(`Thème ${newTheme === 'light' ? 'clair' : 'sombre'} activé`, 'info');
    });

    themeToggle.addEventListener('mouseenter', () => {
        themeToggle.style.transform = 'scale(1.1)';
    });

    themeToggle.addEventListener('mouseleave', () => {
        themeToggle.style.transform = 'scale(1)';
    });

    document.body.appendChild(themeToggle);
}

function setupNotifications() {
    // Créer le conteneur de notifications
    const notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    notificationContainer.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        pointer-events: none;
    `;
    document.body.appendChild(notificationContainer);
}

function showNotification(message, type = 'info', duration = 3000) {
    const container = document.getElementById('notification-container');
    if (!container) return;

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    notification.innerHTML = `
        <i class="fas ${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    notification.style.cssText = `
        background: ${getNotificationColor(type)};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        pointer-events: auto;
        max-width: 400px;
    `;

    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
    `;

    container.appendChild(notification);

    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto-suppression
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (container.contains(notification)) {
                container.removeChild(notification);
            }
        }, 300);
    }, duration);
}

function getNotificationColor(type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0284c7'
    };
    return colors[type] || colors.info;
}

// Fonctions utilitaires globales
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'XOF',
        minimumFractionDigits: 0
    }).format(amount);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateTime(date) {
    return new Date(date).toLocaleString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Exporter les fonctions globales
window.showNotification = showNotification;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.formatDateTime = formatDateTime;
window.debounce = debounce;
window.throttle = throttle;

// Styles CSS pour le thème sombre
const darkThemeStyles = `
    [data-theme="dark"] {
        --bg-primary: #1f2937;
        --bg-secondary: #111827;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --border-color: #374151;
    }
    
    [data-theme="dark"] .admin-dashboard {
        background: var(--bg-secondary);
    }
    
    [data-theme="dark"] .admin-content {
        background: var(--bg-primary);
    }
    
    [data-theme="dark"] .content-section,
    [data-theme="dark"] .stat-card,
    [data-theme="dark"] .annonce-card,
    [data-theme="dark"] .message-item {
        background: var(--bg-secondary);
        border-color: var(--border-color);
    }
    
    [data-theme="dark"] .page-title,
    [data-theme="dark"] .section-title,
    [data-theme="dark"] .annonce-title,
    [data-theme="dark"] .message-sender {
        color: var(--text-primary);
    }
    
    [data-theme="dark"] .admin-sidebar {
        background: var(--bg-primary);
        border-color: var(--border-color);
    }
`;

// Ajouter les styles de thème
const styleSheet = document.createElement('style');
styleSheet.textContent = darkThemeStyles;
document.head.appendChild(styleSheet);

console.log('Administration Linekode initialisée');

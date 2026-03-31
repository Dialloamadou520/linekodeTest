// Script pour réinitialiser toutes les données de l'administration
// Met tous les compteurs à zéro et nettoie le localStorage

function resetAllData() {
    if (confirm('Êtes-vous sûr de vouloir réinitialiser toutes les données ?\n\nCette action va :\n- Mettre tous les compteurs à 0\n- Supprimer toutes les inscriptions\n- Supprimer tous les messages\n- Supprimer toutes les annonces\n- Réinitialiser les statistiques\n\nCette action est IRREVERSIBLE !')) {
        
        // Réinitialiser les données principales
        const resetData = {
            inscriptions: [],
            annonces: [
                {
                    id: 1,
                    title: 'Bienvenue sur Linekode',
                    content: 'Nous sommes ravis de vous accueillir dans notre école de formation en développement web.',
                    status: 'published',
                    date: new Date().toISOString().split('T')[0],
                    author: 'Admin'
                }
            ],
            messages: [],
            settings: {
                siteName: 'Linekode',
                adminEmail: 'admin@linekode.sn',
                currency: 'FCFA'
            },
            stats: {
                totalInscriptions: 0,
                totalRevenue: 0,
                conversionRate: 0
            }
        };
        
        // Sauvegarder les données réinitialisées
        localStorage.setItem('linekode_admin_data', JSON.stringify(resetData));
        
        // Nettoyer les autres localStorage
        localStorage.removeItem('linekode_inscriptions');
        localStorage.removeItem('linekode_messages');
        localStorage.removeItem('adminSyncQueue');
        localStorage.removeItem('adminNotifications');
        localStorage.removeItem('sentEmails');
        
        // Nettoyer les données client si elles existent
        localStorage.removeItem('inscriptions');
        localStorage.removeItem('messages');
        
        // Afficher un message de confirmation
        alert('Toutes les données ont été réinitialisées avec succès !\n\nLes compteurs sont maintenant à 0.');
        
        // Recharger la page pour voir les changements
        location.reload();
    }
}

function resetCountersOnly() {
    if (confirm('Réinitialiser uniquement les compteurs ?\n\nCette action va :\n- Mettre les statistiques à 0\n- Conserver les inscriptions, messages et annonces existants')) {
        
        const data = JSON.parse(localStorage.getItem('linekode_admin_data') || '{}');
        
        // Réinitialiser uniquement les statistiques
        data.stats = {
            totalInscriptions: data.inscriptions ? data.inscriptions.length : 0,
            totalRevenue: 0,
            conversionRate: 0
        };
        
        localStorage.setItem('linekode_admin_data', JSON.stringify(data));
        
        alert('Compteurs réinitialisés avec succès !');
        location.reload();
    }
}

function clearClientData() {
    if (confirm('Nettoyer les données du client ?\n\nCette action va supprimer les données temporaires du site public')) {
        
        localStorage.removeItem('linekode_inscriptions');
        localStorage.removeItem('linekode_messages');
        
        alert('Données client nettoyées avec succès !');
    }
}

function exportCurrentData() {
    const data = JSON.parse(localStorage.getItem('linekode_admin_data') || '{}');
    const dataStr = JSON.stringify(data, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
    
    const exportFileDefaultName = `linekode-backup-${new Date().toISOString().split('T')[0]}.json`;
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
}

// Ajouter un bouton de réinitialisation dans l'admin
function addResetButton() {
    const resetButton = document.createElement('button');
    resetButton.innerHTML = '<i class="fas fa-trash"></i> Réinitialiser';
    resetButton.className = 'btn-reset';
    resetButton.style.cssText = `
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #ef4444;
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        z-index: 1000;
        transition: all 0.3s ease;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        line-height: 1;
    `;
    
    resetButton.addEventListener('click', () => {
        const options = `
            <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); z-index: 10001; min-width: 300px;">
                <h3 style="margin: 0 0 15px 0; color: #1f2937;">Réinitialiser les données</h3>
                <button onclick="resetAllData()" style="width: 100%; padding: 10px; margin: 5px 0; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    🗑️ Tout réinitialiser
                </button>
                <button onclick="resetCountersOnly()" style="width: 100%; padding: 10px; margin: 5px 0; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    🔄 Réinitialiser compteurs
                </button>
                <button onclick="clearClientData()" style="width: 100%; padding: 10px; margin: 5px 0; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    🧹 Nettoyer client
                </button>
                <button onclick="exportCurrentData()" style="width: 100%; padding: 10px; margin: 5px 0; background: #0284c7; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    💾 Exporter avant
                </button>
                <button onclick="this.parentElement.remove()" style="width: 100%; padding: 10px; margin: 5px 0; background: #f3f4f6; color: #6b7280; border: none; border-radius: 6px; cursor: pointer;">
                    Annuler
                </button>
            </div>
        `;
        
        const modal = document.createElement('div');
        modal.innerHTML = options;
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
        
        document.body.appendChild(modal);
    });
    
    resetButton.addEventListener('mouseenter', () => {
        resetButton.style.transform = 'scale(1.1)';
    });
    
    resetButton.addEventListener('mouseleave', () => {
        resetButton.style.transform = 'scale(1)';
    });
    
    document.body.appendChild(resetButton);
}

// Initialiser le bouton de réinitialisation
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/admin/')) {
        addResetButton();
    }
});

// Rendre les fonctions globales
window.resetAllData = resetAllData;
window.resetCountersOnly = resetCountersOnly;
window.clearClientData = clearClientData;
window.exportCurrentData = exportCurrentData;

// Statistiques dynamiques - Linekode Admin
class StatistiquesManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadStats();
        this.setupFilters();
        this.setupCharts();
        this.startAutoRefresh();
    }

    loadStats() {
        const stats = window.adminSystem.getStats();
        const formationStats = window.adminSystem.getFormationStats();
        const monthlyStats = window.adminSystem.getMonthlyStats();
        const recentActivity = this.getRecentActivity();

        this.updateStatCards(stats);
        this.updateFormationStats(formationStats);
        this.updateMonthlyChart(monthlyStats);
        this.updateRecentActivity(recentActivity);
    }

    updateStatCards(stats) {
        this.animateValue('totalInscriptions', stats.totalInscriptions);
        this.animateValue('conversionRate', stats.conversionRate);
        this.animateValue('totalRevenue', Math.round(stats.totalRevenue / 1000) + 'K');
        this.animateValue('activeFormations', 6);
    }

    animateValue(id, end) {
        const element = document.querySelector(`[data-stat="${id}"]`);
        if (!element) return;

        const start = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            let current;
            if (typeof end === 'string' && end.includes('K')) {
                current = Math.floor(start + (parseInt(end) - start) * progress);
                element.textContent = current + 'K';
            } else {
                current = Math.floor(start + (end - start) * progress);
                element.textContent = current;
            }
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    updateFormationStats(formationStats) {
        const tbody = document.querySelector('.formation-stats tbody');
        if (!tbody) return;

        tbody.innerHTML = formationStats.map(formation => `
            <tr>
                <td>${formation.name}</td>
                <td>${formation.count}</td>
                <td>${formation.revenue.toLocaleString()} FCFA</td>
                <td>${this.calculateSatisfactionRate(formation.name)}%</td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${this.calculateSatisfactionRate(formation.name)}%;"></div>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    calculateSatisfactionRate(formationName) {
        // Simuler un taux de satisfaction basé sur le nom de la formation
        const baseRates = {
            'Développement Web Complet': 92,
            'React Avancé': 88,
            'UI/UX Design': 95,
            'Node.js Backend': 85
        };
        return baseRates[formationName] || Math.floor(Math.random() * 20) + 80;
    }

    updateMonthlyChart(monthlyStats) {
        const chartContainer = document.querySelector('.chart-container');
        if (!chartContainer) return;

        const months = Object.keys(monthlyStats);
        const inscriptions = months.map(month => monthlyStats[month].inscriptions);
        const revenue = months.map(month => monthlyStats[month].revenue);

        // Créer un graphique simple avec CSS (vous pouvez remplacer par Chart.js)
        chartContainer.innerHTML = `
            <div class="simple-chart">
                <div class="chart-title">Évolution des inscriptions mensuelles</div>
                <div class="chart-bars">
                    ${months.map((month, index) => `
                        <div class="bar-container">
                            <div class="bar" style="height: ${Math.max(inscriptions[index] * 10, 10)}%;" data-value="${inscriptions[index]}"></div>
                            <div class="bar-label">${month}</div>
                        </div>
                    `).join('')}
                </div>
                <div class="chart-legend">
                    <span><i class="fas fa-chart-bar"></i> Inscriptions</span>
                </div>
            </div>
        `;

        // Ajouter le style pour le graphique
        if (!document.querySelector('#chart-styles')) {
            const style = document.createElement('style');
            style.id = 'chart-styles';
            style.textContent = `
                .simple-chart {
                    padding: 20px;
                    text-align: center;
                }
                .chart-title {
                    font-size: 16px;
                    font-weight: bold;
                    margin-bottom: 20px;
                    color: #1f2937;
                }
                .chart-bars {
                    display: flex;
                    justify-content: space-around;
                    align-items: flex-end;
                    height: 200px;
                    margin-bottom: 20px;
                }
                .bar-container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    flex: 1;
                    max-width: 80px;
                }
                .bar {
                    width: 40px;
                    background: linear-gradient(to top, #0284c7, #0ea5e9);
                    border-radius: 4px 4px 0 0;
                    position: relative;
                    transition: all 0.3s ease;
                }
                .bar:hover {
                    background: linear-gradient(to top, #0369a1, #0284c7);
                    transform: translateY(-2px);
                }
                .bar::after {
                    content: attr(data-value);
                    position: absolute;
                    top: -25px;
                    left: 50%;
                    transform: translateX(-50%);
                    font-size: 12px;
                    font-weight: bold;
                    color: #0284c7;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }
                .bar:hover::after {
                    opacity: 1;
                }
                .bar-label {
                    font-size: 11px;
                    color: #6b7280;
                    margin-top: 5px;
                }
                .chart-legend {
                    font-size: 12px;
                    color: #6b7280;
                }
            `;
            document.head.appendChild(style);
        }
    }

    getRecentActivity() {
        const data = window.adminSystem.getData();
        const activities = [];

        // Ajouter les inscriptions récentes
        data.inscriptions.slice(-5).forEach(inscription => {
            activities.push({
                date: inscription.date,
                type: 'inscription',
                description: `${inscription.name} - ${inscription.formation}`,
                amount: inscription.status === 'confirmed' ? '20K FCFA' : '-'
            });
        });

        // Ajouter les messages récents
        data.messages.slice(-3).forEach(message => {
            activities.push({
                date: message.date.split(' ')[0],
                type: 'message',
                description: `Contact de ${message.sender}`,
                amount: '-'
            });
        });

        return activities.sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 10);
    }

    updateRecentActivity(activities) {
        const tbody = document.querySelector('.activity-table tbody');
        if (!tbody) return;

        tbody.innerHTML = activities.map(activity => `
            <tr>
                <td>${activity.date}</td>
                <td>${this.getTypeBadge(activity.type)}</td>
                <td>${activity.description}</td>
                <td>${activity.amount}</td>
            </tr>
        `).join('');
    }

    getTypeBadge(type) {
        const badges = {
            'inscription': '<span style="color: #10b981;">Inscription</span>',
            'message': '<span style="color: #f59e0b;">Message</span>',
            'payment': '<span style="color: #3b82f6;">Paiement</span>',
            'cancellation': '<span style="color: #ef4444;">Annulation</span>'
        };
        return badges[type] || '<span style="color: #6b7280;">Autre</span>';
    }

    setupFilters() {
        const periodFilter = document.getElementById('periodFilter');
        const formationFilter = document.getElementById('formationFilter');

        if (periodFilter) {
            periodFilter.addEventListener('change', (e) => {
                this.filterByPeriod(e.target.value);
            });
        }

        if (formationFilter) {
            formationFilter.addEventListener('change', (e) => {
                this.filterByFormation(e.target.value);
            });
        }
    }

    filterByPeriod(period) {
        console.log('Filtrer par période:', period);
        // Implémenter le filtrage par période
        this.showNotification(`Période changée: ${period} jours`, 'info');
    }

    filterByFormation(formation) {
        console.log('Filtrer par formation:', formation);
        // Implémenter le filtrage par formation
        this.showNotification(`Formation filtrée: ${formation}`, 'info');
    }

    setupCharts() {
        // Préparer pour l'intégration de Chart.js si nécessaire
        this.initChartJS();
    }

    initChartJS() {
        // Vérifier si Chart.js est disponible
        if (typeof Chart !== 'undefined') {
            this.createAdvancedCharts();
        } else {
            // Charger Chart.js dynamiquement si nécessaire
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
            script.onload = () => this.createAdvancedCharts();
            document.head.appendChild(script);
        }
    }

    createAdvancedCharts() {
        // Créer des graphiques avancés avec Chart.js
        const ctx1 = document.getElementById('inscriptionsChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Inscriptions',
                        data: [12, 19, 8, 15, 22, 18],
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        const ctx2 = document.getElementById('revenueChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
                    datasets: [{
                        label: 'Revenus (FCFA)',
                        data: [240000, 380000, 160000, 300000, 440000, 360000],
                        backgroundColor: '#0284c7'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
    }

    startAutoRefresh() {
        // Rafraîchir les statistiques toutes les 60 secondes
        setInterval(() => {
            this.loadStats();
            this.showNotification('Statistiques actualisées', 'success');
        }, 60000);
    }

    exportStats() {
        const stats = window.adminSystem.getStats();
        const formationStats = window.adminSystem.getFormationStats();
        const monthlyStats = window.adminSystem.getMonthlyStats();

        const exportData = {
            stats: stats,
            formationStats: formationStats,
            monthlyStats: monthlyStats,
            exportDate: new Date().toISOString()
        };

        const dataStr = JSON.stringify(exportData, null, 2);
        const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
        
        const exportFileDefaultName = `linekode-stats-${new Date().toISOString().split('T')[0]}.json`;
        
        const linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
    }

    printStats() {
        window.print();
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : '#0284c7'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Initialiser le gestionnaire de statistiques
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('statistiques.html')) {
        window.statistiquesManager = new StatistiquesManager();
    }
});

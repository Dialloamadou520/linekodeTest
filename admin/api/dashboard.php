<?php
require_once '../config.php';

header('Content-Type: application/json');

// Router API
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'stats':
        getDashboardStats();
        break;
    case 'recent':
        getRecentActivity();
        break;
    case 'overview':
        getOverview();
        break;
    default:
        jsonResponse(['error' => 'Action non trouvée'], 404);
}

function getDashboardStats() {
    try {
        // Statistiques des inscriptions
        $stmt = $db->prepare("SELECT 
            COUNT(*) as total_inscriptions,
            COUNT(CASE WHEN status = 'new' THEN 1 END) as new_inscriptions,
            COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_inscriptions
        FROM inscriptions");
        $stmt->execute();
        $inscriptionStats = $stmt->fetch();
        
        // Statistiques des messages
        $stmt = $db->prepare("SELECT 
            COUNT(*) as total_messages,
            COUNT(CASE WHEN read_status = 0 THEN 1 END) as unread_messages
        FROM messages");
        $stmt->execute();
        $messageStats = $stmt->fetch();
        
        // Statistiques des annonces
        $stmt = $db->prepare("SELECT COUNT(*) as total_annonces FROM annonces");
        $stmt->execute();
        $annonceStats = $stmt->fetch();
        
        // Calculs
        $totalRevenue = $inscriptionStats['confirmed_inscriptions'] * 20000; // 20K FCFA par inscription
        $conversionRate = $inscriptionStats['total_inscriptions'] > 0 ? 
            round(($inscriptionStats['confirmed_inscriptions'] / $inscriptionStats['total_inscriptions']) * 100, 2) : 0;
        
        jsonResponse([
            'success' => true,
            'data' => [
                'totalInscriptions' => (int)$inscriptionStats['total_inscriptions'],
                'newInscriptions' => (int)$inscriptionStats['new_inscriptions'],
                'totalAnnonces' => (int)$annonceStats['total_annonces'],
                'unreadMessages' => (int)$messageStats['unread_messages'],
                'totalRevenue' => $totalRevenue,
                'conversionRate' => $conversionRate
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function getRecentActivity() {
    try {
        $activities = [];
        
        // Inscriptions récentes
        $stmt = $db->prepare("SELECT 'inscription' as type, name, email, formation, date, status 
                            FROM inscriptions 
                            ORDER BY date DESC 
                            LIMIT 5");
        $stmt->execute();
        $inscriptions = $stmt->fetchAll();
        
        foreach ($inscriptions as $inscription) {
            $activities[] = [
                'type' => 'inscription',
                'title' => $inscription['name'],
                'description' => $inscription['formation'],
                'date' => $inscription['date'],
                'status' => $inscription['status']
            ];
        }
        
        // Messages récents
        $stmt = $db->prepare("SELECT 'message' as type, sender, subject, date, read_status 
                            FROM messages 
                            ORDER BY date DESC 
                            LIMIT 3");
        $stmt->execute();
        $messages = $stmt->fetchAll();
        
        foreach ($messages as $message) {
            $activities[] = [
                'type' => 'message',
                'title' => $message['sender'],
                'description' => $message['subject'],
                'date' => explode(' ', $message['date'])[0],
                'status' => $message['read_status'] ? 'read' : 'unread'
            ];
        }
        
        // Trier par date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        jsonResponse(['success' => true, 'data' => array_slice($activities, 0, 5)]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function getOverview() {
    try {
        // Statistiques mensuelles
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = new DateTime();
            $date->modify("-$i month");
            $monthKey = $date->format('Y-m');
            
            $stmt = $db->prepare("SELECT 
                COUNT(*) as inscriptions,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) * 20000 as revenue
                FROM inscriptions 
                WHERE DATE_FORMAT(date, '%Y-%m') = ?");
            $stmt->execute([$monthKey]);
            $monthStats = $stmt->fetch();
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'inscriptions' => (int)$monthStats['inscriptions'],
                'revenue' => (int)$monthStats['revenue']
            ];
        }
        
        // Formations populaires
        $stmt = $db->prepare("SELECT formation, COUNT(*) as count 
                            FROM inscriptions 
                            GROUP BY formation 
                            ORDER BY count DESC 
                            LIMIT 5");
        $stmt->execute();
        $formations = $stmt->fetchAll();
        
        // Activité récente détaillée
        $stmt = $db->prepare("(SELECT 'inscription' as type, name, email, formation, date, status 
                            FROM inscriptions 
                            ORDER BY date DESC 
                            LIMIT 3)
                            UNION ALL
                            (SELECT 'message' as type, sender, email, subject, date, read_status as status 
                            FROM messages 
                            ORDER BY date DESC 
                            LIMIT 2)");
        $stmt->execute();
        $recentActivity = $stmt->fetchAll();
        
        jsonResponse([
            'success' => true,
            'data' => [
                'monthlyStats' => $monthlyData,
                'popularFormations' => $formations,
                'recentActivity' => $recentActivity
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
?>

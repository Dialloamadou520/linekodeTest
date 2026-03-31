<?php
require_once '../config.php';

header('Content-Type: application/json');

// Router API
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        listInscriptions();
        break;
    case 'add':
        addInscription();
        break;
    case 'update':
        updateInscription();
        break;
    case 'delete':
        deleteInscription();
        break;
    case 'stats':
        getInscriptionStats();
        break;
    default:
        jsonResponse(['error' => 'Action non trouvée'], 404);
}

function listInscriptions() {
    $status = $_GET['status'] ?? null;
    $formation = $_GET['formation'] ?? null;
    $search = $_GET['search'] ?? null;
    
    $sql = "SELECT * FROM inscriptions WHERE 1=1";
    $params = [];
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    if ($formation) {
        $sql .= " AND formation LIKE ?";
        $params[] = "%$formation%";
    }
    
    if ($search) {
        $sql .= " AND (name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY date DESC, created_at DESC";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $inscriptions = $stmt->fetchAll();
        
        jsonResponse(['success' => true, 'data' => $inscriptions]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function addInscription() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (!$data['name'] || !$data['email'] || !$data['formation']) {
        jsonResponse(['error' => 'Champs requis manquants'], 400);
    }
    
    if (!validateEmail($data['email'])) {
        jsonResponse(['error' => 'Email invalide'], 400);
    }
    
    try {
        $sql = "INSERT INTO inscriptions (name, email, phone, formation, niveau, motivation, address, status, date, source) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            sanitizeInput($data['name']),
            sanitizeInput($data['email']),
            sanitizeInput($data['phone'] ?? ''),
            sanitizeInput($data['formation']),
            sanitizeInput($data['niveau'] ?? ''),
            sanitizeInput($data['motivation'] ?? ''),
            sanitizeInput($data['address'] ?? ''),
            $data['status'] ?? 'new',
            $data['date'] ?? date('Y-m-d'),
            $data['source'] ?? 'admin'
        ]);
        
        $id = $db->lastInsertId();
        
        // Envoyer un email de notification
        $subject = "Nouvelle inscription: " . $data['name'];
        $content = "
            <h2>Nouvelle inscription reçue</h2>
            <p><strong>Nom:</strong> {$data['name']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Téléphone:</strong> {$data['phone']}</p>
            <p><strong>Formation:</strong> {$data['formation']}</p>
            <p><strong>Date:</strong> " . ($data['date'] ?? date('Y-m-d')) . "</p>
            <p><strong>Motivation:</strong> " . ($data['motivation'] ?? 'Non spécifiée') . "</p>
        ";
        
        sendEmail('admin@linekode.sn', $subject, $content);
        
        jsonResponse(['success' => true, 'id' => $id, 'message' => 'Inscription ajoutée avec succès']);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateInscription() {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$id || !$data) {
        jsonResponse(['error' => 'ID ou données manquants'], 400);
    }
    
    try {
        $sql = "UPDATE inscriptions SET ";
        $updates = [];
        $params = [];
        
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (isset($data['name'])) {
            $updates[] = "name = ?";
            $params[] = sanitizeInput($data['name']);
        }
        
        if (isset($data['email'])) {
            if (!validateEmail($data['email'])) {
                jsonResponse(['error' => 'Email invalide'], 400);
            }
            $updates[] = "email = ?";
            $params[] = sanitizeInput($data['email']);
        }
        
        if (isset($data['phone'])) {
            $updates[] = "phone = ?";
            $params[] = sanitizeInput($data['phone']);
        }
        
        if (empty($updates)) {
            jsonResponse(['error' => 'Aucune donnée à mettre à jour'], 400);
        }
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse(['success' => true, 'message' => 'Inscription mise à jour avec succès']);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteInscription() {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID manquant'], 400);
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM inscriptions WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(['success' => true, 'message' => 'Inscription supprimée avec succès']);
        } else {
            jsonResponse(['error' => 'Inscription non trouvée'], 404);
        }
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function getInscriptionStats() {
    try {
        // Statistiques générales
        $stmt = $db->prepare("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'new' THEN 1 END) as new_count,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_count,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
        FROM inscriptions");
        $stmt->execute();
        $stats = $stmt->fetch();
        
        // Statistiques par formation
        $stmt = $db->prepare("SELECT formation, COUNT(*) as count FROM inscriptions GROUP BY formation ORDER BY count DESC");
        $stmt->execute();
        $byFormation = $stmt->fetchAll();
        
        // Revenus (20K FCFA par inscription confirmée)
        $revenue = $stats['confirmed_count'] * 20000;
        
        jsonResponse([
            'success' => true,
            'data' => [
                'total' => $stats['total'],
                'new' => $stats['new_count'],
                'pending' => $stats['pending_count'],
                'confirmed' => $stats['confirmed_count'],
                'cancelled' => $stats['cancelled_count'],
                'revenue' => $revenue,
                'conversionRate' => $stats['total'] > 0 ? round(($stats['confirmed_count'] / $stats['total']) * 100, 2) : 0,
                'byFormation' => $byFormation
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
?>

<?php
require_once '../config.php';

header('Content-Type: application/json');

// Router API
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        listAnnonces();
        break;
    case 'add':
        addAnnonce();
        break;
    case 'update':
        updateAnnonce();
        break;
    case 'delete':
        deleteAnnonce();
        break;
    case 'publish':
        publishAnnonce();
        break;
    case 'stats':
        getAnnonceStats();
        break;
    default:
        jsonResponse(['error' => 'Action non trouvée'], 404);
}

function listAnnonces() {
    $status = $_GET['status'] ?? null;
    
    $sql = "SELECT * FROM annonces";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY date DESC, created_at DESC";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $annonces = $stmt->fetchAll();
        
        jsonResponse(['success' => true, 'data' => $annonces]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function addAnnonce() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (!$data['title'] || !$data['content']) {
        jsonResponse(['error' => 'Titre et contenu requis'], 400);
    }
    
    try {
        $sql = "INSERT INTO annonces (title, content, status, date, author) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            sanitizeInput($data['title']),
            sanitizeInput($data['content']),
            $data['status'] ?? 'draft',
            $data['date'] ?? date('Y-m-d'),
            $data['author'] ?? 'Admin'
        ]);
        
        $id = $db->lastInsertId();
        
        jsonResponse(['success' => true, 'id' => $id, 'message' => 'Annonce créée avec succès']);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateAnnonce() {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$id || !$data) {
        jsonResponse(['error' => 'ID ou données manquants'], 400);
    }
    
    try {
        $sql = "UPDATE annonces SET ";
        $updates = [];
        $params = [];
        
        if (isset($data['title'])) {
            $updates[] = "title = ?";
            $params[] = sanitizeInput($data['title']);
        }
        
        if (isset($data['content'])) {
            $updates[] = "content = ?";
            $params[] = sanitizeInput($data['content']);
        }
        
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (isset($data['date'])) {
            $updates[] = "date = ?";
            $params[] = $data['date'];
        }
        
        if (empty($updates)) {
            jsonResponse(['error' => 'Aucune donnée à mettre à jour'], 400);
        }
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        jsonResponse(['success' => true, 'message' => 'Annonce mise à jour avec succès']);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteAnnonce() {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID manquant'], 400);
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM annonces WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(['success' => true, 'message' => 'Annonce supprimée avec succès']);
        } else {
            jsonResponse(['error' => 'Annonce non trouvée'], 404);
        }
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function publishAnnonce() {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID manquant'], 400);
    }
    
    try {
        $stmt = $db->prepare("UPDATE annonces SET status = 'published' WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(['success' => true, 'message' => 'Annonce publiée avec succès']);
        } else {
            jsonResponse(['error' => 'Annonce non trouvée'], 404);
        }
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function getAnnonceStats() {
    try {
        // Statistiques générales
        $stmt = $db->prepare("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_count,
            COUNT(CASE WHEN status = 'published' THEN 1 END) as published_count,
            COUNT(CASE WHEN status = 'scheduled' THEN 1 END) as scheduled_count
        FROM annonces");
        $stmt->execute();
        $stats = $stmt->fetch();
        
        // Annonces récentes
        $stmt = $db->prepare("SELECT * FROM annonces ORDER BY date DESC LIMIT 5");
        $stmt->execute();
        $recent = $stmt->fetchAll();
        
        jsonResponse([
            'success' => true,
            'data' => [
                'total' => $stats['total'],
                'draft' => $stats['draft_count'],
                'published' => $stats['published_count'],
                'scheduled' => $stats['scheduled_count'],
                'recent' => $recent
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
?>

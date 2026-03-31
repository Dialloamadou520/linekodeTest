<?php
require_once '../config.php';

header('Content-Type: application/json');

// Router API
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        listMessages();
        break;
    case 'add':
        addMessage();
        break;
    case 'markAsRead':
        markAsRead();
        break;
    case 'delete':
        deleteMessage();
        break;
    case 'stats':
        getMessageStats();
        break;
    default:
        jsonResponse(['error' => 'Action non trouvée'], 404);
}

function listMessages() {
    $unreadOnly = isset($_GET['unread']) && $_GET['unread'] === 'true';
    
    $sql = "SELECT * FROM messages";
    $params = [];
    
    if ($unreadOnly) {
        $sql .= " WHERE read_status = 0";
    }
    
    $sql .= " ORDER BY date DESC, created_at DESC";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();
        
        jsonResponse(['success' => true, 'data' => $messages]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function addMessage() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (!$data['sender'] || !$data['email'] || !$data['subject'] || !$data['content']) {
        jsonResponse(['error' => 'Champs requis manquants'], 400);
    }
    
    if (!validateEmail($data['email'])) {
        jsonResponse(['error' => 'Email invalide'], 400);
    }
    
    try {
        $sql = "INSERT INTO messages (sender, email, phone, subject, content, date, read_status, source) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            sanitizeInput($data['sender']),
            sanitizeInput($data['email']),
            sanitizeInput($data['phone'] ?? ''),
            sanitizeInput($data['subject']),
            sanitizeInput($data['content']),
            $data['date'] ?? date('Y-m-d H:i:s'),
            0,
            $data['source'] ?? 'admin'
        ]);
        
        $id = $db->lastInsertId();
        
        // Envoyer un email de notification
        $subject = "Nouveau message: " . $data['subject'];
        $content = "
            <h2>Nouveau message reçu</h2>
            <p><strong>Expéditeur:</strong> {$data['sender']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Téléphone:</strong> {$data['phone']}</p>
            <p><strong>Sujet:</strong> {$data['subject']}</p>
            <p><strong>Date:</strong> " . ($data['date'] ?? date('Y-m-d H:i:s')) . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br($data['content']) . "</p>
        ";
        
        sendEmail('admin@linekode.sn', $subject, $content);
        
        jsonResponse(['success' => true, 'id' => $id, 'message' => 'Message ajouté avec succès']);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function markAsRead() {
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID manquant'], 400);
    }
    
    try {
        $stmt = $db->prepare("UPDATE messages SET read_status = 1 WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(['success' => true, 'message' => 'Message marqué comme lu']);
        } else {
            jsonResponse(['error' => 'Message non trouvé'], 404);
        }
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteMessage() {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
    }
    
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        jsonResponse(['error' => 'ID manquant'], 400);
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            jsonResponse(['success' => true, 'message' => 'Message supprimé avec succès']);
        } else {
            jsonResponse(['error' => 'Message non trouvé'], 404);
        }
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function getMessageStats() {
    try {
        // Statistiques générales
        $stmt = $db->prepare("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN read_status = 0 THEN 1 END) as unread_count,
            COUNT(CASE WHEN read_status = 1 THEN 1 END) as read_count
        FROM messages");
        $stmt->execute();
        $stats = $stmt->fetch();
        
        // Messages récents
        $stmt = $db->prepare("SELECT * FROM messages ORDER BY date DESC LIMIT 5");
        $stmt->execute();
        $recent = $stmt->fetchAll();
        
        jsonResponse([
            'success' => true,
            'data' => [
                'total' => $stats['total'],
                'unread' => $stats['unread_count'],
                'read' => $stats['read_count'],
                'recent' => $recent
            ]
        ]);
    } catch(PDOException $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}
?>

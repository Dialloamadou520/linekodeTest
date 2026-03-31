<?php
require_once '../config-server.php';

header('Content-Type: application/json');

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupérer toutes les formations ou une formation spécifique
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $db->prepare("SELECT * FROM formations WHERE id = ?");
            $stmt->execute([$id]);
            $formation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($formation) {
                jsonResponse($formation);
            } else {
                jsonResponse(['error' => 'Formation non trouvée'], 404);
            }
        } else {
            // Récupérer toutes les formations
            $stmt = $db->query("SELECT * FROM formations ORDER BY created_at DESC");
            $formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse($formations);
        }
        break;
        
    case 'POST':
        // Ajouter une nouvelle formation
        $data = json_decode(file_get_contents('php://input'), true);
        
        $title = sanitizeInput($data['title'] ?? '');
        $description = sanitizeInput($data['description'] ?? '');
        $duration = sanitizeInput($data['duration'] ?? '');
        $price = sanitizeInput($data['price'] ?? '');
        $level = sanitizeInput($data['level'] ?? 'debutant');
        $status = sanitizeInput($data['status'] ?? 'active');
        
        if (empty($title) || empty($description)) {
            jsonResponse(['error' => 'Le titre et la description sont obligatoires'], 400);
        }
        
        try {
            $stmt = $db->prepare("INSERT INTO formations (title, description, duration, price, level, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $duration, $price, $level, $status]);
            
            $id = $db->lastInsertId();
            $newFormation = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'duration' => $duration,
                'price' => $price,
                'level' => $level,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            jsonResponse($newFormation, 201);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la création de la formation: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        // Mettre à jour une formation
        $id = $_GET['id'] ?? '';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($id)) {
            jsonResponse(['error' => 'ID de formation requis'], 400);
        }
        
        $title = sanitizeInput($data['title'] ?? '');
        $description = sanitizeInput($data['description'] ?? '');
        $duration = sanitizeInput($data['duration'] ?? '');
        $price = sanitizeInput($data['price'] ?? '');
        $level = sanitizeInput($data['level'] ?? 'debutant');
        $status = sanitizeInput($data['status'] ?? 'active');
        
        if (empty($title) || empty($description)) {
            jsonResponse(['error' => 'Le titre et la description sont obligatoires'], 400);
        }
        
        try {
            $stmt = $db->prepare("UPDATE formations SET title = ?, description = ?, duration = ?, price = ?, level = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $description, $duration, $price, $level, $status, $id]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse(['message' => 'Formation mise à jour avec succès']);
            } else {
                jsonResponse(['error' => 'Formation non trouvée ou aucune modification'], 404);
            }
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la mise à jour de la formation: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        // Supprimer une formation
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            jsonResponse(['error' => 'ID de formation requis'], 400);
        }
        
        try {
            $stmt = $db->prepare("DELETE FROM formations WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse(['message' => 'Formation supprimée avec succès']);
            } else {
                jsonResponse(['error' => 'Formation non trouvée'], 404);
            }
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la suppression de la formation: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
        break;
}
?>

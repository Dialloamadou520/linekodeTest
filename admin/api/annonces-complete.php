<?php
require_once '../config-server.php';

header('Content-Type: application/json');

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupérer toutes les annonces ou une annonce spécifique
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $db->prepare("SELECT * FROM annonces WHERE id = ?");
            $stmt->execute([$id]);
            $annonce = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($annonce) {
                jsonResponse($annonce);
            } else {
                jsonResponse(['error' => 'Annonce non trouvée'], 404);
            }
        } else {
            // Récupérer toutes les annonces
            $stmt = $db->query("SELECT * FROM annonces ORDER BY created_at DESC");
            $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse($annonces);
        }
        break;
        
    case 'POST':
        // Ajouter une nouvelle annonce
        $data = json_decode(file_get_contents('php://input'), true);
        
        $title = sanitizeInput($data['title'] ?? '');
        $content = sanitizeInput($data['content'] ?? '');
        $status = sanitizeInput($data['status'] ?? 'draft');
        $date = $data['date'] ?? date('Y-m-d');
        $author = sanitizeInput($data['author'] ?? 'Admin');
        
        if (empty($title) || empty($content)) {
            jsonResponse(['error' => 'Le titre et le contenu sont obligatoires'], 400);
        }
        
        try {
            $stmt = $db->prepare("INSERT INTO annonces (title, content, status, date, author) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $content, $status, $date, $author]);
            
            $id = $db->lastInsertId();
            $newAnnonce = [
                'id' => $id,
                'title' => $title,
                'content' => $content,
                'status' => $status,
                'date' => $date,
                'author' => $author,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            jsonResponse($newAnnonce, 201);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la création de l\'annonce: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'PUT':
        // Mettre à jour une annonce
        $id = $_GET['id'] ?? '';
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($id)) {
            jsonResponse(['error' => 'ID d\'annonce requis'], 400);
        }
        
        $title = sanitizeInput($data['title'] ?? '');
        $content = sanitizeInput($data['content'] ?? '');
        $status = sanitizeInput($data['status'] ?? 'draft');
        $date = $data['date'] ?? date('Y-m-d');
        $author = sanitizeInput($data['author'] ?? 'Admin');
        
        if (empty($title) || empty($content)) {
            jsonResponse(['error' => 'Le titre et le contenu sont obligatoires'], 400);
        }
        
        try {
            $stmt = $db->prepare("UPDATE annonces SET title = ?, content = ?, status = ?, date = ?, author = ? WHERE id = ?");
            $stmt->execute([$title, $content, $status, $date, $author, $id]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse(['message' => 'Annonce mise à jour avec succès']);
            } else {
                jsonResponse(['error' => 'Annonce non trouvée ou aucune modification'], 404);
            }
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la mise à jour de l\'annonce: ' . $e->getMessage()], 500);
        }
        break;
        
    case 'DELETE':
        // Supprimer une annonce
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            jsonResponse(['error' => 'ID d\'annonce requis'], 400);
        }
        
        try {
            $stmt = $db->prepare("DELETE FROM annonces WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse(['message' => 'Annonce supprimée avec succès']);
            } else {
                jsonResponse(['error' => 'Annonce non trouvée'], 404);
            }
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la suppression de l\'annonce: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
        break;
}
?>

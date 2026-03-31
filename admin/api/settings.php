<?php
require_once '../config-server.php';

header('Content-Type: application/json');

// Récupérer la méthode HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Récupérer tous les paramètres
        $stmt = $db->query("SELECT * FROM settings ORDER BY setting_key");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        jsonResponse($settings);
        break;
        
    case 'POST':
        // Mettre à jour les paramètres
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (empty($data)) {
            jsonResponse(['error' => 'Aucune donnée fournie'], 400);
        }
        
        try {
            foreach ($data as $key => $value) {
                $stmt = $db->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
                
                // Si le paramètre n'existe pas, l'insérer
                if ($stmt->rowCount() === 0) {
                    $insertStmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
                    $insertStmt->execute([$key, $value, "Paramètre: $key"]);
                }
            }
            
            jsonResponse(['message' => 'Paramètres mis à jour avec succès']);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Erreur lors de la mise à jour des paramètres: ' . $e->getMessage()], 500);
        }
        break;
        
    default:
        jsonResponse(['error' => 'Méthode non autorisée'], 405);
        break;
}
?>

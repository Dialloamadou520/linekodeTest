<?php
// Vérification d'authentification intelligente
require_once 'config-server.php';

// Démarrer la session
startSession();

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Fonction pour vérifier la connexion automatique
function checkAutoLogin() {
    // Si l'utilisateur a une session valide, ne pas rediriger
    if (isLoggedIn()) {
        return true;
    }
    
    // Si l'utilisateur a un cookie "remember_me", tenter la reconnexion automatique
    if (isset($_COOKIE['admin_remember']) && !empty($_COOKIE['admin_remember'])) {
        try {
            global $db;
            $token = sanitizeInput($_COOKIE['admin_remember']);
            
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE remember_token = ? AND token_expires > NOW()");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Recréer la session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = $user['username'];
                $_SESSION['admin_id'] = $user['id'];
                
                // Mettre à jour la dernière connexion
                $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                return true;
            }
        } catch (Exception $e) {
            // Erreur lors de la vérification du token
            error_log("Erreur auto-login: " . $e->getMessage());
        }
    }
    
    return false;
}

// Fonction pour rediriger vers la connexion si nécessaire
function requireLogin($redirectUrl = null) {
    if (!checkAutoLogin()) {
        // Sauvegarder l'URL demandée pour rediriger après connexion
        if ($redirectUrl) {
            $_SESSION['redirect_after_login'] = $redirectUrl;
        } else {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: login.php');
        exit;
    }
}

// Fonction pour rediriger après connexion réussie
function redirectAfterLogin() {
    $redirectUrl = $_SESSION['redirect_after_login'] ?? 'menu-admin.php';
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $redirectUrl);
    exit;
}

// Fonction pour créer un token de remember me
function createRememberToken($userId) {
    global $db;
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    $stmt = $db->prepare("UPDATE admin_users SET remember_token = ?, token_expires = ? WHERE id = ?");
    $stmt->execute([$token, $expires, $userId]);
    
    // Définir le cookie pour 30 jours
    setcookie('admin_remember', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    
    return $token;
}

// Fonction pour supprimer le token remember me
function removeRememberToken() {
    global $db;
    
    if (isset($_SESSION['admin_id'])) {
        $stmt = $db->prepare("UPDATE admin_users SET remember_token = NULL, token_expires = NULL WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
    }
    
    // Supprimer le cookie
    setcookie('admin_remember', '', time() - 3600, '/', '', false, true);
}

// Fonction pour obtenir les informations de l'utilisateur connecté
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'username' => $_SESSION['admin_user'] ?? 'Admin',
            'id' => $_SESSION['admin_id'] ?? 1
        ];
    }
    return null;
}

// Vérifier automatiquement la connexion au début de chaque page
checkAutoLogin();
?>

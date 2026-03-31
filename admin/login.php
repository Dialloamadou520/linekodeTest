<?php
require_once 'config-server.php';
require_once 'auth-check.php';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        try {
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Connexion réussie
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ];
                
                // Mettre à jour la dernière connexion
                $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Créer le token remember me si demandé
                if ($remember) {
                    createRememberToken($user['id']);
                }
                
                // Rediriger vers la page demandée ou le menu
                redirectAfterLogin();
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect";
            }
        } catch(PDOException $e) {
            $error = "Erreur de connexion: " . $e->getMessage();
        }
    }
}

// Si l'utilisateur est déjà connecté, rediriger directement
if (isLoggedIn()) {
    redirectAfterLogin();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Linekode</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-login {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            font-family: 'Arial', sans-serif;
        }
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #0284c7;
            margin-bottom: 10px;
            font-size: 24px;
        }
        .login-header p {
            color: #6b7280;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #0284c7;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background: #0369a1;
        }
        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        .success-message {
            background: #f0fdf4;
            color: #16a34a;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #16a34a;
        }
        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #666;
        }
        .checkbox-label input[type="checkbox"] {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-shield-alt" style="font-size: 48px; color: #0284c7; margin-bottom: 20px;"></i>
                <h2>Administration Linekode</h2>
                <p>Connectez-vous pour accéder au panneau</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkmark"></span>
                        Se souvenir de moi
                    </label>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
        </div>
    </div>
</body>
</html>

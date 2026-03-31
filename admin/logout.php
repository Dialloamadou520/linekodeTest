<?php
require_once 'config-server.php';
require_once 'auth-check.php';

// Supprimer le token remember me
removeRememberToken();

// Détruire la session
session_unset();
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php');
exit;
?>

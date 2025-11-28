<?php
// Inclure le fichier de configuration
require_once 'config.php';

function getDatabaseConnection() {
    try {
        // Créer une nouvelle connexion PDO
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        
        // Définir le mode d'erreur PDO sur Exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        return $conn;
        
    } catch(PDOException $e) {
        // Gérer l'erreur proprement
        $error_message = "Connection failed: " . $e->getMessage();
        
        // (Optionnel) Logger l'erreur dans un fichier
        error_log($error_message . "\n", 3, "db_errors.log");
        
        // Retourner null en cas d'échec
        return null;
    }
}
?>
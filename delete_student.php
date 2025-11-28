<?php
require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn = getDatabaseConnection();
    
    if ($conn !== null) {
        try {
            // Vérifier si l'étudiant existe
            $sql = "SELECT * FROM students WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                // Supprimer l'étudiant
                $sql = "DELETE FROM students WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $id]);
                
                // Redirection avec message de succès
                header("Location: list_students.php?message=deleted");
                exit();
            } else {
                // Étudiant non trouvé
                header("Location: list_students.php?message=not_found");
                exit();
            }
            
        } catch(PDOException $e) {
            // Erreur lors de la suppression
            header("Location: list_students.php?message=error");
            exit();
        }
    } else {
        header("Location: list_students.php?message=db_error");
        exit();
    }
} else {
    // Pas d'ID fourni
    header("Location: list_students.php");
    exit();
}
?>
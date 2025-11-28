<?php
require_once 'db_connect.php';

$message = "";
$message_type = "";

if (isset($_POST['submit'])) {
    // Récupérer les données
    $fullname = trim($_POST['fullname']);
    $matricule = trim($_POST['matricule']);
    $group_id = trim($_POST['group_id']);
    
    // Validation
    $errors = [];
    
    if (empty($fullname)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($matricule)) {
        $errors[] = "Matricule is required";
    }
    
    if (empty($group_id)) {
        $errors[] = "Group ID is required";
    }
    
    // Si pas d'erreurs
    if (empty($errors)) {
        $conn = getDatabaseConnection();
        
        if ($conn !== null) {
            try {
                // Préparer la requête SQL
                $sql = "INSERT INTO students (fullname, matricule, group_id) VALUES (:fullname, :matricule, :group_id)";
                $stmt = $conn->prepare($sql);
                
                // Exécuter avec les données
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':matricule' => $matricule,
                    ':group_id' => $group_id
                ]);
                
                $message = "✅ Student added successfully to database!";
                $message_type = "success";
                
            } catch(PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "❌ This matricule already exists!";
                } else {
                    $message = "❌ Error: " . $e->getMessage();
                }
                $message_type = "error";
            }
        } else {
            $message = "❌ Database connection failed";
            $message_type = "error";
        }
    } else {
        $message = "❌ " . implode(", ", $errors);
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student to Database</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 50px auto;">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">Add Student to Database</h2>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required>
                </div>
                
                <div class="form-group">
                    <label>Matricule</label>
                    <input type="text" name="matricule" required>
                </div>
                
                <div class="form-group">
                    <label>Group ID</label>
                    <input type="text" name="group_id" required>
                </div>
                
                <button type="submit" name="submit" class="btn btn-main" style="width: 100%;">
                    Add Student
                </button>
            </form>
        </div>
    </div>
</body>
</html>

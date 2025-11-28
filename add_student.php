<?php
$message = "";
$message_type = "";

if (isset($_POST['submit'])) {
    // 1. Récupérer et nettoyer les données
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $group = trim($_POST['group']);
    
    // 2. Validation
    $errors = [];
    
    if (empty($student_id)) {
        $errors[] = "Student ID is required";
    }
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($group)) {
        $errors[] = "Group is required";
    }
    
    // 3. Si pas d'erreurs, continuer
    if (empty($errors)) {
        $filename = 'students.json';
        $students = [];
        
        // 4. Charger les étudiants existants (si le fichier existe)
        if (file_exists($filename)) {
            $json_data = file_get_contents($filename);
            $students = json_decode($json_data, true);
            
            // Si le fichier est vide ou invalide
            if ($students === null) {
                $students = [];
            }
        }
        
        // 5. Créer le nouvel étudiant
        $new_student = [
            'student_id' => $student_id,
            'name' => $name,
            'group' => $group
        ];
        
        // 6. Ajouter à la liste
        $students[] = $new_student;
        
        // 7. Sauvegarder dans students.json
        if (file_put_contents($filename, json_encode($students, JSON_PRETTY_PRINT))) {
            $message = "✅ Student added successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error saving student data";
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
    <title>Add Student</title>
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
                <h2 class="card-title">Add New Student</h2>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" required>
                </div>
                
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Group</label>
                    <input type="text" name="group" required>
                </div>
                
                <button type="submit" name="submit" class="btn btn-main" style="width: 100%;">
                    Add Student
                </button>
            </form>
        </div>
    </div>
</body>
</html>

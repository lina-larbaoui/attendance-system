<?php
require_once 'db_connect.php';

$message = "";
$message_type = "";
$student = null;

// Récupérer l'ID de l'étudiant
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn = getDatabaseConnection();
    
    if ($conn !== null) {
        try {
            $sql = "SELECT * FROM students WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                $message = "❌ Student not found!";
                $message_type = "error";
            }
        } catch(PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
} else {
    header("Location: list_students.php");
    exit();
}

// Traitement du formulaire de mise à jour
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $fullname = trim($_POST['fullname']);
    $matricule = trim($_POST['matricule']);
    $group_id = trim($_POST['group_id']);
    
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
    
    if (empty($errors)) {
        $conn = getDatabaseConnection();
        
        if ($conn !== null) {
            try {
                $sql = "UPDATE students SET fullname = :fullname, matricule = :matricule, group_id = :group_id WHERE id = :id";
                $stmt = $conn->prepare($sql);
                
                $stmt->execute([
                    ':fullname' => $fullname,
                    ':matricule' => $matricule,
                    ':group_id' => $group_id,
                    ':id' => $id
                ]);
                
                $message = "✅ Student updated successfully!";
                $message_type = "success";
                
                // Recharger les données
                $sql = "SELECT * FROM students WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                
            } catch(PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "❌ This matricule already exists!";
                } else {
                    $message = "❌ Error: " . $e->getMessage();
                }
                $message_type = "error";
            }
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
    <title>Update Student</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
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
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 50px auto;">
        <a href="list_students.php" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Back to Students List
        </a>
        
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">
                    <span class="icon-box"><i class="fa-solid fa-pen"></i></span>
                    Update Student
                </h2>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'circle-check' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($student): ?>
                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
                    
                    <div class="form-group">
                        <label>Full Name</label>
                        <div class="input-box">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Matricule</label>
                        <div class="input-box">
                            <i class="fa-solid fa-id-card"></i>
                            <input type="text" name="matricule" value="<?php echo htmlspecialchars($student['matricule']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Group ID</label>
                        <div class="input-box">
                            <i class="fa-solid fa-users-rectangle"></i>
                            <input type="text" name="group_id" value="<?php echo htmlspecialchars($student['group_id']); ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="update" class="btn btn-main" style="width: 100%; justify-content: center;">
                        <i class="fa-solid fa-save"></i> Update Student
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
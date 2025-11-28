<?php
require_once 'db_connect.php';

$message = "";
$message_type = "";
$session_id = null;

if (isset($_POST['create_session'])) {
    $course_id = trim($_POST['course_id']);
    $group_id = trim($_POST['group_id']);
    $professor_id = trim($_POST['professor_id']);
    $date = date('Y-m-d');
    
    $errors = [];
    
    if (empty($course_id)) {
        $errors[] = "Course ID is required";
    }
    
    if (empty($group_id)) {
        $errors[] = "Group ID is required";
    }
    
    if (empty($professor_id)) {
        $errors[] = "Professor ID is required";
    }
    
    if (empty($errors)) {
        $conn = getDatabaseConnection();
        
        if ($conn !== null) {
            try {
                $sql = "INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) 
                        VALUES (:course_id, :group_id, :date, :opened_by, 'open')";
                $stmt = $conn->prepare($sql);
                
                $stmt->execute([
                    ':course_id' => $course_id,
                    ':group_id' => $group_id,
                    ':date' => $date,
                    ':opened_by' => $professor_id
                ]);
                
                $session_id = $conn->lastInsertId();
                
                $message = "✅ Session created successfully! Session ID: " . $session_id;
                $message_type = "success";
                
            } catch(PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
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
    <title>Create Session</title>
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
        .session-id-box {
            padding: 20px;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            color: white;
            border-radius: 12px;
            text-align: center;
            margin-top: 20px;
        }
        .session-id-box h3 {
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }
        .session-id-box .id {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: 2px;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 50px auto;">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">
                    <span class="icon-box"><i class="fa-solid fa-calendar-plus"></i></span>
                    Create Attendance Session
                </h2>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'circle-check' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($session_id): ?>
                <div class="session-id-box">
                    <h3><i class="fa-solid fa-key"></i> Session ID</h3>
                    <div class="id"><?php echo $session_id; ?></div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Course ID</label>
                    <div class="input-box">
                        <i class="fa-solid fa-book"></i>
                        <input type="text" name="course_id" placeholder="e.g., AWP101" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Group ID</label>
                    <div class="input-box">
                        <i class="fa-solid fa-users-rectangle"></i>
                        <input type="text" name="group_id" placeholder="e.g., G1" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Professor ID</label>
                    <div class="input-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                        <input type="text" name="professor_id" placeholder="e.g., PROF001" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Date (Today)</label>
                    <div class="input-box">
                        <i class="fa-solid fa-calendar"></i>
                        <input type="text" value="<?php echo date('Y-m-d'); ?>" disabled>
                    </div>
                </div>
                
                <button type="submit" name="create_session" class="btn btn-main" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-plus-circle"></i> Create Session
                </button>
            </form>
        </div>
    </div>
</body>
</html>
<?php 

// Gestion des messages
$notification = "";
if (isset($_GET['message'])) {
    switch($_GET['message']) {
        case 'deleted':
            $notification = "<div class='message success'><i class='fa-solid fa-circle-check'></i> Student deleted successfully!</div>";
            break;
        case 'not_found':
            $notification = "<div class='message error'><i class='fa-solid fa-exclamation-circle'></i> Student not found!</div>";
            break;
        case 'error':
            $notification = "<div class='message error'><i class='fa-solid fa-exclamation-circle'></i> Error deleting student!</div>";
            break;
        case 'db_error':
            $notification = "<div class='message error'><i class='fa-solid fa-exclamation-circle'></i> Database connection error!</div>";
            break;
    }
}

require_once 'db_connect.php';

$students = [];
$conn = getDatabaseConnection();

if ($conn !== null) {
    try {
        $sql = "SELECT * FROM students ORDER BY id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Students</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        tbody tr {
            transition: all 0.2s ease;
        }
        
        tbody tr:hover {
            background-color: rgba(124, 58, 237, 0.05);
        }
        
        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .btn-small {
            padding: 8px 16px;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        
        .error-box {
            padding: 15px;
            background: #fee2e2;
            color: #991b1b;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }
        
        .empty-state {
            text-align: center;
            color: #64748b;
            padding: 40px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1200px; margin: 50px auto;">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">
                    <span class="icon-box"><i class="fa-solid fa-users"></i></span>
                    Students List
                </h2>
                <a href="add_student_db.php" class="btn btn-main">
                    <i class="fa-solid fa-plus"></i> Add Student
                </a>
            </div>
            
            <?php if (isset($error_message)): ?>
                <div class="error-box">
                    <i class="fa-solid fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($students)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <p>No students found in database.</p>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><i class="fa-solid fa-hashtag"></i> ID</th>
                                <th><i class="fa-solid fa-user"></i> Full Name</th>
                                <th><i class="fa-solid fa-id-card"></i> Matricule</th>
                                <th><i class="fa-solid fa-users-rectangle"></i> Group</th>
                                <th><i class="fa-solid fa-gear"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                <td><?php echo htmlspecialchars($student['matricule']); ?></td>
                                <td><?php echo htmlspecialchars($student['group_id']); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn-small btn-warning">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a href="delete_student.php?id=<?php echo $student['id']; ?>" 
                                           class="btn-small btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this student?')">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
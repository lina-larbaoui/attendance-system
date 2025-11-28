<?php
$message = "";
$message_type = "";
$students = [];

// Charger les étudiants depuis students.json
if (file_exists('students.json')) {
    $json_data = file_get_contents('students.json');
    $students = json_decode($json_data, true);
    
    if ($students === null) {
        $students = [];
    }
}

// Traitement du formulaire
if (isset($_POST['submit_attendance'])) {
    $today = date('Y-m-d');
    $attendance_file = "attendance_" . $today . ".json";
    
    // Vérifier si l'assiduité d'aujourd'hui existe déjà
    if (file_exists($attendance_file)) {
        $message = "⚠️ Attendance for today has already been taken.";
        $message_type = "warning";
    } else {
        // Créer le tableau d'assiduité
        $attendance = [];
        
        foreach ($students as $student) {
            $status = isset($_POST['status_' . $student['student_id']]) ? 'present' : 'absent';
            
            $attendance[] = [
                'student_id' => $student['student_id'],
                'status' => $status
            ];
        }
        
        // Sauvegarder dans le fichier
        if (file_put_contents($attendance_file, json_encode($attendance, JSON_PRETTY_PRINT))) {
            $message = "✅ Attendance saved successfully for " . $today;
            $message_type = "success";
        } else {
            $message = "❌ Error saving attendance";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Attendance</title>
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
        .warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .student-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .student-info {
            flex: 1;
        }
        .attendance-controls {
            display: flex;
            gap: 15px;
        }
        .attendance-controls label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .attendance-controls input[type="checkbox"] {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 50px auto;">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">Take Attendance - <?php echo date('Y-m-d'); ?></h2>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($students)): ?>
                <p style="text-align: center; color: #64748b;">No students found. Please add students first.</p>
            <?php else: ?>
                <form method="POST" action="">
                    <?php foreach ($students as $student): ?>
                        <div class="student-row">
                            <div class="student-info">
                                <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                <span style="color: #64748b;"> - ID: <?php echo htmlspecialchars($student['student_id']); ?></span>
                                <span style="color: #64748b;"> - Group: <?php echo htmlspecialchars($student['group']); ?></span>
                            </div>
                            <div class="attendance-controls">
                                <label>
                                    <input type="checkbox" name="status_<?php echo $student['student_id']; ?>" value="present">
                                    Present
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" name="submit_attendance" class="btn btn-main" style="width: 100%; margin-top: 20px;">
                        Save Attendance
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>



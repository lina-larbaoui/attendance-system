<?php
require_once 'db_connect.php';

$message = "";
$message_type = "";
$sessions = [];

// Charger toutes les sessions ouvertes
$conn = getDatabaseConnection();
if ($conn !== null) {
    try {
        $sql = "SELECT * FROM attendance_sessions WHERE status = 'open' ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $message = "❌ Error loading sessions: " . $e->getMessage();
        $message_type = "error";
    }
}

// Fermer une session
if (isset($_POST['close_session'])) {
    $session_id = $_POST['session_id'];
    
    if ($conn !== null) {
        try {
            $sql = "UPDATE attendance_sessions SET status = 'closed' WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $session_id]);
            
            $message = "✅ Session #" . $session_id . " closed successfully!";
            $message_type = "success";
            
            // Recharger les sessions
            $sql = "SELECT * FROM attendance_sessions WHERE status = 'open' ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            $message = "❌ Error: " . $e->getMessage();
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
    <title>Close Session</title>
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
        .session-card {
            background: #f8f9fa;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .session-info {
            flex: 1;
        }
        .session-info h3 {
            margin: 0 0 10px 0;
            color: var(--text);
        }
        .session-meta {
            display: flex;
            gap: 20px;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .session-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container" style="max-width: 900px; margin: 50px auto;">
        <div class="card">
            <div class="card-head">
                <h2 class="card-title">
                    <span class="icon-box"><i class="fa-solid fa-door-closed"></i></span>
                    Close Attendance Session
                </h2>
                <a href="create_session.php" class="btn btn-main">
                    <i class="fa-solid fa-plus"></i> Create New Session
                </a>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <i class="fa-solid fa-<?php echo $message_type === 'success' ? 'circle-check' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (empty($sessions)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>No open sessions found.</p>
                </div>
            <?php else: ?>
                <?php foreach ($sessions as $session): ?>
                    <div class="session-card">
                        <div class="session-info">
                            <h3><i class="fa-solid fa-hashtag"></i> Session #<?php echo $session['id']; ?></h3>
                            <div class="session-meta">
                                <span><i class="fa-solid fa-book"></i> Course: <?php echo htmlspecialchars($session['course_id']); ?></span>
                                <span><i class="fa-solid fa-users-rectangle"></i> Group: <?php echo htmlspecialchars($session['group_id']); ?></span>
                                <span><i class="fa-solid fa-calendar"></i> <?php echo htmlspecialchars($session['date']); ?></span>
                                <span><i class="fa-solid fa-user"></i> By: <?php echo htmlspecialchars($session['opened_by']); ?></span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="status-badge">
                                <i class="fa-solid fa-circle-dot"></i> OPEN
                            </span>
                            <form method="POST" action="" style="margin: 0;">
                                <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
                                <button type="submit" name="close_session" class="btn btn-warning" 
                                        onclick="return confirm('Are you sure you want to close this session?')">
                                    <i class="fa-solid fa-lock"></i> Close
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
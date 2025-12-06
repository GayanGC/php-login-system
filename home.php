<?php
require_once 'includes/auth_check.php';
require_once 'includes/db_conn.php';

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, username, bio, profile_pic FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - User System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .welcome-container {
            text-align: center;
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
        }
        
        .welcome-message {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        
        .user-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: block;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .action-buttons {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #3498db;
            color: #3498db;
        }
        
        .btn-outline:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .user-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .user-info p {
            font-size: 1.1rem;
            color: #555;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <?php if (!empty($user['profile_pic'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="user-avatar">
        <?php else: ?>
            <img src="assets/images/default-avatar.png" alt="Default Avatar" class="user-avatar">
        <?php endif; ?>
        
        <h1 class="welcome-message">
            Welcome back, <span style="color: #3498db;"><?php echo htmlspecialchars($user['full_name']); ?>!</span>
        </h1>
        
        <div class="user-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <?php if (!empty($user['bio'])): ?>
                <p><strong>Bio:</strong> <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <a href="profile.php" class="btn btn-primary">
                <i class="fas fa-user-edit"></i> Edit Profile
            </a>
            <a href="logout.php" class="btn btn-outline">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</body>
</html>

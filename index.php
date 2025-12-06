<?php
// 1. Start session
session_start();

// 2. Connect to database
include 'includes/db_conn.php';

// 3. If user is already logged in, send them to Home page directly
if (isset($_SESSION['user_id']) || isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}

$error = "";

// 4. Check if Login button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check username in database
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Verify the encrypted password
            if (password_verify($password, $row['password'])) {
                // Success: Create session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                
                // Redirect to Home page
                header("Location: home.php");
                exit();
            } else {
                $error = "Incorrect Password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { 
            font-family: sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            background: #f1f1f1; 
        }
        .card { 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            width: 350px; 
            text-align: center; 
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        input { 
            width: 90%; 
            padding: 12px; 
            margin: 10px 0; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 14px;
        }
        button { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover { 
            background: #0056b3; 
        }
        a { 
            text-decoration: none; 
            color: #007bff; 
            display: block; 
            margin-top: 20px; 
            font-size: 14px;
        }
        a:hover {
            text-decoration: underline;
        }
        .error { 
            color: red; 
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="index.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
        <a href="register.php">Don't have an account? Register here</a>
    </div>

</body>
</html>
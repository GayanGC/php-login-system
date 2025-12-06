<?php
// 1. Start session
session_start();

// 2. Connect to database
include 'includes/db_conn.php';

// 3. If user is already logged in, redirect to Home
if (isset($_SESSION['user_id']) || isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}

$error = "";
$success = "";

// 4. Check if Register button is clicked
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validation
    if (empty($full_name) || empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        // Check if username already exists
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "The username is already taken. Try another one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $sql2 = "INSERT INTO users (full_name, username, password) VALUES (?, ?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("sss", $full_name, $username, $hashed_password);

            if ($stmt2->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong! " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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
            background: #28a745; /* Green color for Register button */
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
            background: #218838; 
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
        .success { 
            color: green; 
            background: #d4edda;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="card">
        <h2>Register</h2>
        
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        
        <a href="index.php">Already have an account? Login here</a>
    </div>

</body>
</html>
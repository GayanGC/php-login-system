<?php
session_start();
include 'includes/db_conn.php';

// Log වෙලා නැත්නම් එළියට දානවා
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// Form එක Submit කළාම වැඩ කරන කොටස
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $bio = $_POST['bio'];

    // Photo Upload කිරීම
    if (!empty($_FILES['profile_pic']['name'])) {
        $img_name = $_FILES['profile_pic']['name'];
        $tmp_name = $_FILES['profile_pic']['tmp_name'];
        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
        $img_ex_lc = strtolower($img_ex);
        $allowed_exs = array("jpg", "jpeg", "png");

        if (in_array($img_ex_lc, $allowed_exs)) {
            $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
            $img_upload_path = 'uploads/' . $new_img_name;
            move_uploaded_file($tmp_name, $img_upload_path);

            // Database update with photo
            $sql = "UPDATE users SET full_name=?, bio=?, profile_pic=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $full_name, $bio, $new_img_name, $user_id);
        } else {
            $message = "You can't upload files of this type";
        }
    } else {
        // Database update without photo
        $sql = "UPDATE users SET full_name=?, bio=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $full_name, $bio, $user_id);
    }

    if (isset($stmt) && $stmt->execute()) {
        $message = "Profile updated successfully!";
    }
}

// User ගේ විස්තර ලබා ගැනීම
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f1f1f1; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 400px; text-align: center; }
        input, textarea { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        a { text-decoration: none; color: #555; display: block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Edit Profile</h2>
        <?php if ($message) echo "<p style='color:green'>$message</p>"; ?>
        
        <img src="uploads/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
        
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
            <textarea name="bio" placeholder="Bio"><?php echo $user['bio']; ?></textarea>
            <label>Change Photo:</label>
            <input type="file" name="profile_pic">
            <button type="submit">Update Profile</button>
        </form>
        <a href="home.php">Back to Home</a>
    </div>
</body>
</html>
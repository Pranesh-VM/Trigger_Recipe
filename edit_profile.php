<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Fetch current user data
$stmt = $connection->prepare("SELECT username, email FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$result = $stmt->get_result();
$current_user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated values from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $current_user['password']; // Don't change password if not provided

    // Update user information in the database
    $stmt = $connection->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE username = ?");
    $stmt->bind_param("ssss", $username, $email, $password, $_SESSION['user']);
    if ($stmt->execute()) {
        $_SESSION['user'] = $username;  // Update session with the new username
        header("Location: profile.php");
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styling for the form and inputs */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .profile-form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .profile-form h2 {
            text-align: center;
        }

        .profile-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .profile-form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Profile Edit Form -->
    <div class="profile-form">
        <h2>Edit Profile</h2>
        <form method="post" action="">
            <!-- Current Username (read-only) -->
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($current_user['username']); ?>" readonly>

            <!-- Current Email -->
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>

            <!-- Password (optional) -->
            <label for="password">New Password (optional)</label>
            <input type="password" name="password" id="password" placeholder="Enter new password if you want to change it">

            <!-- Submit Button -->
            <button type="submit">Update Profile</button>
        </form>
    </div>

</body>
</html>

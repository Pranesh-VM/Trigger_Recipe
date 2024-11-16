<?php
session_start();
require 'db.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['user'];
$stmt = $connection->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
</head>
<body>
    <h1>Profile Overview</h1>
    <p>Username: <?php echo $user['username']; ?></p>
    <p>Email: <?php echo $user['email']; ?></p>
    <h3>Submitted Recipes:</h3>
    <?php
    $stmt = $connection->prepare("SELECT * FROM recipes WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $recipes = $stmt->get_result();
    while ($recipe = $recipes->fetch_assoc()) {
        echo "<p><a href='recipe.php?id={$recipe['id']}'>{$recipe['title']}</a></p>";
    }
    ?>

    <h2>Edit Profile</h2>
    <form method="post" action="edit_profile.php">
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
        <input type="password" name="password" placeholder="New Password">
        <button type="submit">Update Profile</button>
    </form>
</body>
</html>

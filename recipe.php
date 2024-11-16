<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");  // Redirect to login if not logged in
    exit();
}

// Handle adding to cart
if (isset($_GET['add_to_cart'])) {
    $recipe_id = $_GET['add_to_cart'];
    // Initialize the cart if not already done
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Add recipe to cart if not already added
    if (!in_array($recipe_id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $recipe_id;
    }
}

// Fetch recipe details
$recipe_id = $_GET['id'];
$stmt = $connection->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $recipe['title']; ?></title>
</head>
<body>
    <h1><?php echo $recipe['title']; ?></h1>
    <img src="<?php echo $recipe['image']; ?>" alt="<?php echo $recipe['title']; ?>" style="width:400px;">
    <h3>Ingredients:</h3>
    <p><?php echo $recipe['ingredients']; ?></p>
    <h3>Instructions:</h3>
    <p><?php echo $recipe['instructions']; ?></p>

    <!-- Add to Cart Button -->
    <form method="get" action="">
        <input type="hidden" name="id" value="<?php echo $recipe['id']; ?>">
        <button type="submit" name="add_to_cart" value="<?php echo $recipe['id']; ?>">Add to Cart</button>
    </form>

    <!-- Cart Link -->
    <div>
        <a href="cart.php">View Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>

    <!-- Continue Shopping Button -->
    <div>
        <a href="recipe_list.php">
            <button>Continue Shopping</button>
        </a>
    </div>
</body>
</html>

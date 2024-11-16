<?php
session_start();
require 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// If cart is empty, notify user
if (empty($_SESSION['cart'])) {
    $cart_message = "Your cart is empty.";
} else {
    $cart_message = "You have " . count($_SESSION['cart']) . " item(s) in your cart.";
}

// Fetch details of selected dishes from the cart
$cart_items = [];
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $connection->prepare("SELECT * FROM recipes WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($_SESSION['cart'])), ...$_SESSION['cart']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
}

// Remove dish from cart
if (isset($_GET['remove_from_cart'])) {
    $recipe_id = $_GET['remove_from_cart'];
    $_SESSION['cart'] = array_diff($_SESSION['cart'], [$recipe_id]); // Remove the recipe from the cart
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Your Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <p><?php echo $cart_message; ?></p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>">
                    <h3><?php echo $item['title']; ?></h3>
                    <a href="?remove_from_cart=<?php echo $item['id']; ?>" class="button">Remove</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="cart-actions">
        <a href="index.php" class="button">Continue Shopping</a>
        <button class="button" disabled>Checkout (Coming Soon)</button>
    </div>
</body>
</html>

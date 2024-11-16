<?php
session_start();
require 'db.php'; 

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Initialize search term and category filter
$search_term = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Prepare query with search and category filter
$query = "SELECT * FROM recipes WHERE title LIKE ? ";
$params = ["%$search_term%"];

if ($category_filter) {
    $query .= " AND category = ? ";
    $params[] = $category_filter;
}

// Prepare the statement
$stmt = $connection->prepare($query);

if ($stmt) {
    // Bind parameters and execute query
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Handle query preparation failure
    $result = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS for positioning the Edit Profile button */
        .edit-profile-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .edit-profile-btn:hover {
            background-color: #45a049;
        }

        /* Styling for the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        /* Recipe Card Styling */
        .recipes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .recipe-card {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 10px;
            padding: 15px;
            width: 200px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .recipe-card img {
            width: 100%;
            border-radius: 5px;
        }

        .recipe-card h3 {
            font-size: 18px;
            text-align: center;
            margin-top: 10px;
        }

        .recipe-card p {
            text-align: center;
            font-size: 14px;
        }

        form input, form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        form button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!-- Edit Profile Button (Top Right Corner) -->
    <a href="edit_profile.php">
        <button class="edit-profile-btn">Edit Profile</button>
    </a>

    <h1>Welcome, <?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest'; ?>!</h1>
    
    <!-- Search Form -->
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search recipes" value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Category Filter Form -->
    <form method="get" action="">
        <select name="category">
            <option value="">All Categories</option>
            <option value="Vegetarian" <?php echo $category_filter === 'Vegetarian' ? 'selected' : ''; ?>>Vegetarian</option>
            <option value="Dessert" <?php echo $category_filter === 'Dessert' ? 'selected' : ''; ?>>Dessert</option>
            <!-- Add more categories here if needed -->
        </select>
        <button type="submit">Filter</button>
    </form>

    <h2>Recipes</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="recipes">
            <?php while ($recipe = $result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <img src="<?php echo $recipe['image']; ?>" alt="<?php echo $recipe['title']; ?>">
                    <h3><a href="recipe.php?id=<?php echo $recipe['id']; ?>"><?php echo $recipe['title']; ?></a></h3>
                    <p><?php echo substr($recipe['instructions'], 0, 100); ?>...</p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No recipes found. Please try again with different search criteria.</p>
    <?php endif; ?>
</body>
</html>

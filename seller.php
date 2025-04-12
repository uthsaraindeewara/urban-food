<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urban Food - Products</title>
    <!-- Combined CSS files -->
    <link rel="stylesheet" href="seller.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
    session_start();
    $isLoggedIn = isset($_SESSION['user']);
    $username = $isLoggedIn ? $_SESSION['user']['name'] : '';
    $firstLetter = $isLoggedIn ? strtoupper(substr($username, 0, 1)) : '';
    
    // Database configuration
    $dbConfig = [
        'host' => 'localhost:3306',
        'user' => 'root',
        'password' => '',
        'dbname' => 'storedb'
    ];
    ?>

    <div class="products-list-page">
        <!-- Navigation Bar -->
        <div class="task-baar">
            <div class="navbar-container">
                <img class="nav-logo" src="img/urban-food-logo.png" alt="Urban Food Logo">
                
                <div class="navbar-links">
                    <a href="index.php" class="nav-link">Shop</a>
                </div>

                <div class="search-bar">
                    <form class="search-form" action="search-results.php" method="post">
                        <input type="text" class="search-input" name="search" placeholder="Search for products">
                        <button type="submit" class="search-button">
                            <img src="img/search.svg" alt="Search">
                        </button>
                    </form>
                    <ul class="suggestions-list"></ul>
                </div>

                <div class="user-actions">
                    <a href="wish-list.php" class="action-icon">
                        <img src="img/heart-2.svg" alt="Wishlist">
                    </a>
                    
                    <div class="account-dropdown">
                        <img src="img/heart.svg" alt="Account" class="dropdown-toggle">
                        <div class="dropdown-menu">
                            <?php if ($isLoggedIn): ?>
                                <div class="user-info">
                                    <div class="user-avatar"><?= $firstLetter ?></div>
                                    <span class="username"><?= htmlspecialchars($username) ?></span>
                                </div>
                                <button class="dropdown-item">Edit Account</button>
                                <hr>
                                <a href="logout.php" class="dropdown-item">Logout</a>
                            <?php else: ?>
                                <a href="login.html" class="btn btn-login">Log In</a>
                                <a href="sign.html" class="btn btn-signup">Sign Up</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <a href="cart.php" class="action-icon">
                        <img src="img/shopping-cart.svg" alt="Cart">
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="product-grid">
            <?php
            // Include the database connection file
            include 'connection.php';

            // Ensure the seller ID is provided
            if (!isset($_GET['seller_id']) || !is_numeric($_GET['seller_id'])) {
                die("Invalid seller ID.");
            }

            $seller_id = (int)$_GET['seller_id']; // Sanitize input

            // Prepare the SQL block to call the procedure
            $sql = "BEGIN SYSTEM.get_products_by_seller(:seller_id, :cursor); END;";
            $stid = oci_parse($conn, $sql);

            $cursor = oci_new_cursor($conn);

            // Bind parameters
            oci_bind_by_name($stid, ":seller_id", $seller_id);
            oci_bind_by_name($stid, ":cursor", $cursor, -1, OCI_B_CURSOR);

            // Execute the procedure
            if (!oci_execute($stid)) {
                $e = oci_error($stid);
                die("Error executing procedure: " . $e['message']);
            }

            // Execute the cursor to fetch data
            oci_execute($cursor);

            // Process results
            while ($row = oci_fetch_assoc($cursor)) {
                echo '<div class="product-card">
                        <a href="product.php?id='.htmlspecialchars($row["PRODUCT_ID"]).'">
                            <img class="product-image" src="productImages/'.htmlspecialchars($row["IMAGE_NAME"]).'">
                            <div class="product-details">
                                <div class="product-info">
                                    <h3 class="product-name">'.htmlspecialchars($row["PRODUCT_NAME"]).'</h3>
                                    <div class="product-brand">Priya\'s Brand</div>
                                </div>
                                <div class="product-price">'.htmlspecialchars($row["PRICE"]).'</div>
                            </div>
                        </a>
                    </div>';
            }

            // Free resources
            oci_free_statement($cursor);
            oci_free_statement($stid);
            oci_close($conn);
            ?>
        </div>

        <footer>
            <p>Copyright © 2024 Beliyo Fasion Pvt Ltd. All rights reserved.</p>
        </footer>
    </div>

    <script src="index.js"></script>
</body>
</html>
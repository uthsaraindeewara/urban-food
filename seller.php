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

    if (!isset($_GET['seller_id']) || !is_numeric($_GET['seller_id'])) {
        die("Invalid seller ID.");
    }
    
    $seller_id = (int)$_GET['seller_id'];

    $filter = $_GET['filter'] ?? 'all';
    $sort = $_GET['sort'] ?? 'none';
    $order = $_GET['order'] ?? 'asc';
    $search = $_GET['search'] ?? '';
    ?>

    <div class="products-list-page">
        <!-- Navigation Bar -->
        <div class="task-baar">
            <div class="navbar-container">
                <img class="nav-logo" src="img/urban-food-logo.png" alt="Urban Food Logo">
                
                <div class="navbar-links">
                    <a href="index.php" class="nav-link">Home</a>
                </div>
                <?php
                    if ($isLoggedIn) {
                        echo '
                        <div class="navbar-links">
                            <a href="orders.php" class="nav-link">Orders</a>
                        </div>
                        ';
                    }
                ?>

                <div class="search-bar">
                    <form class="search-form" method="get">
                    <input type="text" class="search-input" name="search" placeholder="Search for products" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <input type="hidden" name="seller_id" value="<?= $seller_id ?>">
                    <input type="hidden" name="filter" value="<?= $filter ?>">
                    <input type="hidden" name="sort" value="<?= $sort ?>">
                    <input type="hidden" name="order" value="<?= $order ?>">
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

        <div class="main-container">
            <div class="side-panel">
                <div class="seller-container">
                    <?php
                        include 'connection.php';

                        $seller_name = '';
                        $seller_address = '';
                        $contact_no = '';

                        // Prepare the SQL block to call the procedure
                        $sql = "BEGIN SYSTEM.get_seller_details(:seller_id, :seller_name, :seller_address, :contact_no); END;";
                        $stid = oci_parse($conn, $sql);

                        // Bind parameters
                        oci_bind_by_name($stid, ":seller_id", $seller_id);
                        oci_bind_by_name($stid, ":seller_name", $seller_name, 200);
                        oci_bind_by_name($stid, ":seller_address", $seller_address, 200);
                        oci_bind_by_name($stid, ":contact_no", $contact_no, 10);

                        if (!oci_execute($stid)) {
                            $e = oci_error($stid);
                            die("Error executing procedure: " . $e['message']);
                        }
                    ?>
                    <div class="seller-image">
                        <img src="sellerImages/seller-<?php echo $seller_id; ?>.jpg" alt="Seller Image">
                    </div>
                    <div class="seller-info">
                        <h2 class="seller-name"><?php echo $seller_name; ?></h2>
                        <p class="seller-detail">
                            <img src="img/location.png" class="icon" alt="Location Icon">
                            <?php echo $seller_address; ?>
                        </p>
                        <p class="seller-detail">
                            <img src="img/phone.png" class="icon" alt="Phone Icon">
                            <?php echo $contact_no; ?>
                        </p>
                    </div>
                </div>
                <form method="get" class="filter-sort-container" id="filterSortForm">
                    <!-- Filter Section -->
                    <input type="hidden" name="seller_id" id="seller_id" value="<?= $seller_id ?>">
                    <?php if (isset($_GET['search'])): ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search']) ?>">
                    <?php endif; ?>
                    <div class="section">
                        <div class="section-title">Filter</div>
                        <div class="btn-group">
                            <input type="radio" name="filter" id="all" value="all" <?= ($_GET['filter'] ?? 'all') === 'all' ? 'checked' : '' ?>>
                            <label for="all">All</label>

                            <input type="radio" name="filter" id="vegetables" value="vegetables" <?= ($_GET['filter'] ?? '') === 'vegetables' ? 'checked' : '' ?>>
                            <label for="vegetables">Vegetables</label>

                            <input type="radio" name="filter" id="fruits" value="fruits" <?= ($_GET['filter'] ?? '') === 'fruits' ? 'checked' : '' ?>>
                            <label for="fruits">Fruits</label>

                            <input type="radio" name="filter" id="greens" value="greens" <?= ($_GET['filter'] ?? '') === 'greens' ? 'checked' : '' ?>>
                            <label for="greens">Greens</label>
                            
                            <input type="radio" name="filter" id="grains" value="grains" <?= ($_GET['filter'] ?? '') === 'grains' ? 'checked' : '' ?>>
                            <label for="grains">Grains</label>
                            
                            <input type="radio" name="filter" id="dairy" value="dairy" <?= ($_GET['filter'] ?? '') === 'dairy' ? 'checked' : '' ?>>
                            <label for="dairy">Dairy</label>
                        </div>
                    </div>

                        <!-- Sort Section -->
                    <div class="section">
                        <div class="section-title">Sort By</div>
                        <div class="btn-group">
                            <input type="radio" name="sort" id="none" value="none" <?= ($_GET['sort'] ?? 'none') === 'none' ? 'checked' : '' ?>>
                            <label for="none">None</label>

                            <input type="radio" name="sort" id="price" value="price" <?= ($_GET['sort'] ?? '') === 'price' ? 'checked' : '' ?>>
                            <label for="price">Price</label>

                            <input type="radio" name="sort" id="ratings" value="ratings" <?= ($_GET['sort'] ?? '') === 'ratings' ? 'checked' : '' ?>>
                            <label for="ratings">Ratings</label>
                        </div>

                        <!-- Sort Direction -->
                        <div class="sort-direction">
                            <input type="hidden" name="order" id="direction" value="<?= $_GET['order'] ?? 'asc' ?>">
                            <button type="button" onclick="toggleDirection()">Sort: <span id="dir-label"><?= ($_GET['order'] ?? 'asc') === 'asc' ? 'Ascending' : 'Descending' ?></span></button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Product Grid -->
            <div class="product-grid">
                <?php
                // Include the database connection file
                include 'connection.php';

                require 'connection1.php'; // MongoDB
    
                // Prepare MongoDB connection
                $db = $client->selectDatabase('productdb');
                $ratingCollection = $db->selectCollection('ratings');

                // Prepare the SQL block to call the procedure
                $sql = "BEGIN SYSTEM.get_products_by_seller(:seller_id, :filter, :sort, :order, :search, :cursor); END;";
                $stid = oci_parse($conn, $sql);

                $cursor = oci_new_cursor($conn);

                // Bind parameters
                oci_bind_by_name($stid, ":seller_id", $seller_id);
                oci_bind_by_name($stid, ":filter", $filter);
                oci_bind_by_name($stid, ":sort", $sort);
                oci_bind_by_name($stid, ":order", $order);
                oci_bind_by_name($stid, ":search", $search);
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
                    $productId = $row['PRODUCT_ID'];
                    $name = htmlspecialchars($row['PRODUCT_NAME']);
                    $description = htmlspecialchars($row['PRODUCT_DESCRIPTION']);
                    $price = number_format($row['PRICE'], 2);
                    $imagePath = "productImages/product-" . $productId . ".jpg";
                    
                    // Get rating from MongoDB
                    $averageRating = 0;

                    try {
                        $result = $ratingCollection->aggregate([
                            ['$match' => ['product_id' => (int)$productId]],
                            ['$group' => [
                                '_id' => '$product_id',
                                'averageRating' => ['$avg' => '$value']
                            ]]
                        ]);
                        
                        foreach ($result as $doc) {
                            $averageRating = round($doc['averageRating'], 2);
                            break;
                        }
                    } catch (Exception $e) {
                        // If MongoDB fails, continue with default rating
                        error_log("MongoDB rating error: " . $e->getMessage());
                    }

                    $fullStars = floor($averageRating);
                    $halfStar = ($averageRating - $fullStars) >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

                    $starsHtml = '';

                    // Full stars
                    for ($i = 0; $i < $fullStars; $i++) {
                        $starsHtml .= '<span class="star full">★</span>';
                    }

                    // Half star
                    if ($halfStar) {
                        $starsHtml .= '<span class="star half">★</span>';
                    }

                    // Empty stars
                    for ($i = 0; $i < $emptyStars; $i++) {
                        $starsHtml .= '<span class="star empty">☆</span>';
                    }

                    $starsHtml .= '<span class="rating-number">(' . number_format($averageRating, 1) . ')</span>';

                    echo '
                    <a href="product.php?id=' . htmlspecialchars($row["PRODUCT_ID"]) . '" class="product-link">
                        <div class="product-card">
                            <img src="productImages/' . htmlspecialchars($row["IMAGE_NAME"]) . '?' . time() . '" alt="' . htmlspecialchars($name) . '" class="product-image">
                            <div class="product-details">
                                <h2>' . htmlspecialchars($name) . '</h2>
                                <p class="description">' . htmlspecialchars($description) . '</p>
                                <div class="rating">' . $starsHtml . '</div>
                                <div class="price">Rs. ' . number_format($price, 2) . '</div>
                            </div>
                        </div>
                    </a>';
                }

                // Only free Oracle resources if they're Oracle resources
                if (is_resource($cursor)) {
                    oci_free_statement($cursor);
                }
                if (is_resource($stid)) {
                    oci_free_statement($stid);
                }
                oci_close($conn);
                ?>
            </div>
        </div>

        <footer>
            <p></p>
        </footer>
    </div>

    <script src="index.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to handle all form submissions
            const handleFormSubmit = (form) => {
                const urlParams = new URLSearchParams(window.location.search);
                
                // Preserve search parameter if it exists
                if (urlParams.has('search')) {
                    let searchInput = form.querySelector('input[name="search"]');
                    if (!searchInput) {
                        searchInput = document.createElement('input');
                        searchInput.type = 'hidden';
                        searchInput.name = 'search';
                        form.appendChild(searchInput);
                    }
                    searchInput.value = urlParams.get('search');
                }
                
                form.submit();
            };

            // Handle filter/sort radio changes
            document.querySelectorAll('input[name="filter"], input[name="sort"]').forEach(el => {
                el.addEventListener('change', () => {
                    handleFormSubmit(document.getElementById('filterSortForm'));
                });
            });

            // Toggle sort direction
            const toggleDirection = () => {
                const dirInput = document.getElementById('direction');
                const dirLabel = document.getElementById('dir-label');
                
                dirInput.value = dirInput.value === 'asc' ? 'desc' : 'asc';
                dirLabel.textContent = dirInput.value === 'asc' ? 'Ascending' : 'Descending';
                
                handleFormSubmit(document.getElementById('filterSortForm'));
            };

            // Initialize direction toggle button
            document.querySelector('.sort-direction button')?.addEventListener('click', toggleDirection);

            // Sync search parameter in filter form on page load
            const urlParams = new URLSearchParams(window.location.search);
            const filterForm = document.getElementById('filterSortForm');
            
            if (filterForm && urlParams.has('search')) {
                let searchInput = filterForm.querySelector('input[name="search"]');
                if (!searchInput) {
                    searchInput = document.createElement('input');
                    searchInput.type = 'hidden';
                    searchInput.name = 'search';
                    filterForm.appendChild(searchInput);
                }
                searchInput.value = urlParams.get('search');
            }
        });
    </script>
</body>
</html>
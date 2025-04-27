<!DOCTYPE html>
<html>
  <?php
  session_start();

  $product_id = isset($_GET['id']) ? $_GET['id'] : null;

  if (!is_numeric($product_id)) {
      die("Invalid ID: Must be a number.");
  }

  // Initialize user variables
  $isLoggedIn = isset($_SESSION['user']);
  $userId = $isLoggedIn ? $_SESSION['user']['userID'] : null;
  $username = $isLoggedIn ? $_SESSION['user']['name'] : '';
  $firstLetter = $isLoggedIn ? strtoupper(substr($username, 0, 1)) : '';
  ?>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="product1.css">
    <!-- Include Swiper's CSS -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
</head>
<body>
<div class="product-detail-page">
    <div class="task-baar">
      <div class="navbar-container">
          <img class="nav-logo" src="img/urban-food-logo.png" alt="Urban Food Logo">
          
          <div class="navbar-links">
              <a href="index.php" class="nav-link">Shop</a>
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
    <div class="product-container">
        <?php
        include 'connection.php';

        $sql = "BEGIN SYSTEM.get_product_details(:product_id, :cursor); END;";
        $stid = oci_parse($conn, $sql);

        $cursor = oci_new_cursor($conn);

        // Bind parameters
        oci_bind_by_name($stid, "product_id", $product_id);
        oci_bind_by_name($stid, ":cursor", $cursor, -1, OCI_B_CURSOR);

        // Execute the procedure
        if (!oci_execute($stid)) {
            $e = oci_error($stid);
            die("Error executing procedure: " . $e['message']);
        }

        oci_execute($cursor);

        $product = oci_fetch_assoc($cursor);

        if ($product) {
            $productName = htmlspecialchars($product['PRODUCT_NAME']);
            $productPrice = htmlspecialchars(number_format($product['PRICE'], 2));
            $productDescription = htmlspecialchars($product['PRODUCT_DESCRIPTION']);
            $sellerId = htmlspecialchars($product['SELLER_ID']);
            $sellerName = htmlspecialchars($product['SELLER_NAME']);
            $sellerAddress = htmlspecialchars($product['FARM_ADDRESS']);
            $contactNo = htmlspecialchars($product['CONTACT_NO']);

            echo '<div>
                    <div class="product-image">
                      <img class="image" src="productImages/' . htmlspecialchars($product["IMAGE_NAME"]) . '">
                    </div>
                    <div class="seller-container">
                      <div class="seller-image">
                          <img src="sellerImages/seller-' . $sellerId . '.jpg" alt="Seller Image">
                      </div>
                      <div class="seller-info">
                          <h2 class="seller-name">' . $sellerName . '</h2>
                          <p class="seller-detail">
                              <img src="img/location.png" class="icon" alt="Location Icon">'
                              . $sellerAddress .
                          '</p>
                          <p class="seller-detail">
                              <img src="img/phone.png" class="icon" alt="Phone Icon">'
                              . $contactNo .
                          '</p>
                      </div>
                    </div>
                  </div>
                  <div class="product-details">
                    <h1 class="product-title">'
                        . $productName .
                    '</h1>
                    <p>'
                        . $productDescription .
                    '</p>';
          
            $rating = 0;

            require 'connection1.php';

            $db = $client->selectDatabase('productdb');
            $ratingCollection = $db->selectCollection('ratings');

            // Aggregation pipeline
            $pipeline = [
                ['$match' => ['product_id' => (int)$product_id]],
                ['$group' => [
                    '_id' => '$product_id',
                    'averageRating' => ['$avg' => '$value']
                ]]
            ];

            // Run the aggregation
            $result = $ratingCollection->aggregate($pipeline);

            $rating = null;
            // Display result
            foreach ($result as $doc) {
              $rating = round($doc['averageRating'], 2);
              break;
            }

            echo '<div class="ratings">
                        <div class="rating">';
            
            for ($i = 1; $i <= 5; $i++) {
              if ($i <= floor($rating)) {
                echo '<span class="star">&#9733;</span>';
              } elseif ($i == ceil($rating) && $rating - floor($rating) >= 0.5) {
                echo '<span class="star half-filled">&#9733;</span>';
              } else {
                echo '<span class="star">&#9734;</span>';
              }
            }
            
            $existingRating = 0;

            if ($userId !== null) {
              $existingRating = $ratingCollection->findone([
                'product_id' => (int)$product_id,
                'user_id' => $userId
              ]);

              $existingRating = $existingRating['value'] ?? 0;
            }

            $starsHtml = '';

            for ($i = 1; $i <= 5; $i++) {
                $activeClass = ($i <= $existingRating) ? 'active' : '';
                
                $starsHtml .= '<span class="select-star ' . $activeClass . '" data-value="' . $i . '">&#9734;</span>';
            }

            echo '<div class="rating-value">' . round($rating, 1) . '/5</div>
                          </div>
                          <div class="rating-container">
                            <button class="rating-toggle" onclick="toggleRating()">Rate this product</button>
                            <div class="rating-dropdown" id="ratingDropdown">
                              <div class="select-rating" id="rating">' 
                              . $starsHtml .
                              ' </div>
                              <p>Selected Rating: <span id="selected-rating">' . $existingRating . '</span></p>
                              
                              <form id="ratingForm">
                                <input type="hidden" name="rating" id="ratingValue" value="' . $existingRating . '">
                                <input type="hidden" name="productId" value="' . $product_id . '">
                                <input type="hidden" name="userId" value="' . $userId . '">
                                <button type="button" onclick="submitRating()">Submit Rating</button>
                              </form>
                            </div>
                        </div>
                    </div>';

              $quantity = $product["QUANTITY"];
              $class = ($quantity == 0) ? 'out-of-stock' : 'in-stock';

            echo '<div class="product-buttons">
                        <form id="addToCartForm" action="addtocart.php" method="POST">
                          <input type="hidden" id="selected-size" name="size" value="" />
                          <input type="hidden" name="productId" value="' . $product_id . '">
                          <label for="quantity">Quantity:</label>
                          <input type="number" name="quantity" id="quantity" value="1" min="1" max="' . $quantity . '" data-max="' . $quantity . '" step="0.5">
                          <input type="hidden" name="price" id="price" value="' . $productPrice . '">
                          <div id="totalPriceContainer" class="total-price">
                              Total: Rs. <span id="totalPrice">' . $productPrice . '</span>
                          </div>
                          <div id="stockMessage" class="stock-message"></div>
                          <button class="add-to-cart">
                              <img src="img/shopping-cart.svg" alt="Cart" class="cart-icon">
                              ADD to Cart
                          </button>
                        </form>
                    </div>
                    <div class="price-container">Rs. '
                        . $productPrice . 
                    '</div>
                    <div class="reviews-container">
                        <button class="toggle-reviews" onclick="toggleReviews()">Show/Hide Reviews</button>
                        
                        <div class="reviews" id="reviews" style="display: none;">
                            <div class="review-list" id="reviewList">';

            $db = $client->selectDatabase('productdb');
            $reviewsCollection = $db->selectCollection('reviews');

            // Fetch and display data
            $reviews = $reviewsCollection->find(['product_id' => (int)$product_id]);

            foreach ($reviews as $review) {
              $user_id = $review['user_id'];
              $user_name = '';
          
              // Prepare the procedure call
              $stmt = oci_parse($conn, 'BEGIN get_user_name(:userId, :userName); END;');
          
              // Bind the input and output
              oci_bind_by_name($stmt, ':userId', $user_id);
              oci_bind_by_name($stmt, ':userName', $user_name, 200);
          
              // Execute the procedure
              if (oci_execute($stmt)) {
                  $user_name = htmlspecialchars($user_name);
              } else {
                  $user_name = 'User';
              }

              $phpDateTime = $review["date"]->toDateTime();
              $formattedDate = $phpDateTime->format('Y-m-d');
              $formattedTime = $phpDateTime->format('H:i:s');

              echo '<div class="review-container">
                      <div class="review-header">
                          <strong>' . $user_name . '</strong>
                      </div>
                      <div class="review-body">
                          <p>' . htmlspecialchars($review["description"]) . '</p>
                      </div>
                      <div class="review-footer">
                          Posted on ' . $formattedDate . ' at ' . $formattedTime . '
                      </div>
                    </div>';
            }
            echo '</div>
                            
                    <h2>Add a Review</h2>
                      <form id="reviewForm">
                          <textarea id="reviewText" rows="4" placeholder="Write your review here..." required></textarea>
                          <input type="hidden" name="productId" value="' . $product_id . '">
                          <input type="hidden" name="cusId" value="' . $userId . '">
                          <button type="button" onclick="submitReview()">Submit Review</button>
                      </form>
                    </div>
                </div>
              </div>';
          echo '</ul>';
        } else {
              echo "Product not found.";
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

<script src="product-details.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper('.swiper-container', {
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        loop: true,
    });
</script>

</body>
</html>

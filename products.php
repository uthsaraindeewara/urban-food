<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="product-list.css" />
    <link rel="stylesheet" href="index.css">
  </head>
  <body>
  <?php
  session_start();

  $isLoggedIn = isset($_SESSION['user']);
  $username = $isLoggedIn ? $_SESSION['user']['name'] : '';
  $firstLetter = $isLoggedIn ? strtoupper(substr($username, 0, 1)) : '';
  $category = null;
  $searchText = null;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchText = $_POST["search"];;
  } else {
    if (isset($_GET['category']) && !empty(trim($_GET['category']))) {
      $category = trim($_GET['category']);
    }
  }
  ?>
    <div class="products-list-page">
      <div class="div">
        <div class="overlap-5">
          <div class="frame-4">
            <div class="text-wrapper-2">Products</div>
            <img class="filter" src="img/filter.svg" />
          </div>
          <div class="frame-9">
            <a href="products.php">
              <div class="frame-10">
                <div class="text-wrapper-4">All</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
            <a href="products.php?category=Vegetables">
              <div class="frame-10">
                <div class="text-wrapper-4">Vegetables</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
            <a href="products.php?category=Greens">
              <div class="frame-10">
                <div class="text-wrapper-4">Greens</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
            <a href="products.php?category=Grains">
              <div class="frame-10">
                <div class="text-wrapper-4">Grains</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
            <a href="products.php?category=Fruits">
              <div class="frame-10">
                <div class="text-wrapper-4">Fruits</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
            <a href="products.php?category=Dairy">
              <div class="frame-10">
                <div class="text-wrapper-4">Dairy</div>
                <img class="icon-2" src="img/icon-3.svg" />
              </div>
            </a>
          </div>
          </div>
        </div>
        <div class="frame-26">
          <div class="text-wrapper-19">
          <?php
            if ($searchText !== null) {
              echo 'Search results for "' . $searchText . '"';
            } else {
              echo $category ? $category : 'All';
            }
          ?>
          </div>
          <div class="text-wrapper-21">
          <div class="text-wrapper-21">
            <div class="toggle-container active">
                <input type="radio" id="vendors" name="toggle">
                <label for="vendors" class="toggle-label" onclick="window.location.href='index.php'">Vendors</label>

                <input type="radio" id="products" name="toggle" checked>
                <label for="products" class="toggle-label" onclick="window.location.href='products.php'">Products</label>

                <div class="toggle-slider"></div>
            </div>
          </div>
          </div>
        </div>
        <div class="task-baar">
          <div class="overlap-15">
            <div class="frame-27">
              <div class="navbar">
                <div class="text-wrapper-22 shop-button"><a href="products.php">Shop</a></div>
                <div class="text-wrapper-22 vegetables-button"><a href="vegetablesProducts.php">Vegetables</a></div>
                <div class="text-wrapper-22 fruits-button"><a href="fruitsProducts.php">Fruits</a></div>
                <div class="text-wrapper-23 dairy-button"><a href="dairyProducts.php">Dairy</a></div>
              </div>
              <div class="search-bar">
                <form id="search-form"  class="search-form" method="post">
                  <input type="text" id="search" class="search" name="search" placeholder="Search for products">
                  <button type="submit" class="search-button"><img src="img/search.svg"></button>
                </form>
                <ul id="suggestions" class="suggestions"></ul>
              </div>
              <div class="frame-28">
                <div class="component wishlist-button" id="wishlist-button"><a href="wish-list.php"><img class="img-2" src="img/heart-2.svg" /></a></div>
                <div class="component account" id="user-menu">
                  <img class="img-2" src="img/heart.svg" onclick="toggleDropdown()" />
                  <div class="dropdown-content" id="dropdown-content">
                      <?php if ($isLoggedIn): ?>
                          <!-- User is logged in -->
                          <div class="user-info">
                              <div class="avatar"><?php echo $firstLetter; ?></div> <!-- First letter of the user's name -->
                              <span class="username"><?php echo htmlspecialchars($username); ?></span> <!-- User's username -->
                          </div>
                          <button class="edit-account">Edit Account</button>
                          <hr />
                          <a href="logout.php" class="logout">Logout</a> <!-- Link to logout script -->
                      <?php else: ?>
                          <!-- User is not logged in -->
                          <a href="login.html" class="account-button signin">Log In</a>
                          <a href="sign.html" class="account-button signup">Sign Up</a>
                      <?php endif; ?>
                  </div>
                </div>
                <div class="shopping-cart-wrapper cart-button" id="cart-button"><a href="cart.php"><img class="img-2" src="img/shopping-cart.svg" /></a></div>
                </div>
              </div>
            </div>
            <div class="rectangle-13">
              <img class="nav-logo" src="img/urban-food-logo.png" />
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <script src="index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </body>
</html>

<div class="product-grid">
    <?php
      // Oracle Database Connection
      include 'connection.php';

      require 'connection1.php'; // MongoDB
    
      // Prepare MongoDB connection
      $db = $client->selectDatabase('productdb');
      $ratingCollection = $db->selectCollection('ratings');
      
      // Prepare the SQL block to call the procedure
      $sql = "BEGIN SYSTEM.get_all_products(:cursor); END;";

      if ($category !== null) {
        $sql = "BEGIN SYSTEM.get_products_by_category(:category, :cursor); END;";
      } else if ($searchText !== null) {
        $sql = "BEGIN SYSTEM.get_products_by_search(:search, :cursor); END;";
      }
      
      $stid = oci_parse($conn, $sql);

      if ($category !== null) {
        oci_bind_by_name($stid, ":category", $category);
      } else if ($searchText !== null) {
        oci_bind_by_name($stid, ":search", $searchText);
      }

      $cursor = oci_new_cursor($conn);

      // Bind parameters
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
          $sellerId = $row['SELLER_ID'];
          $sellerName = htmlspecialchars($row['SELLER_NAME']);
          
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
                      <div class="seller-container">
                        <div class="seller-image">
                            <img src="sellerImages/seller-' . $sellerId . '.jpg" alt="Seller Image">
                        </div>
                        <div class="seller-info">
                            <p class="seller-name">' . $sellerName . '</p>
                        </div>
                      </div>
                      <div class="rating">' . $starsHtml . '</div>
                      <div class="price">Rs. ' . number_format($price, 2) . '</div>
                  </div>
              </div>
          </a>';
      }

      // Free resources
      oci_free_statement($cursor);
      oci_free_statement($stid);
      oci_close($conn);
    ?>
</div>
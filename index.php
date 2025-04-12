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
  ?>
    <div class="products-list-page">
      <div class="div">
        <div class="overlap-5">
          <div class="frame-4">
            <div class="text-wrapper-2">Products</div>
            <img class="filter" src="img/filter.svg" />
          </div>
          <div class="frame-9">
            <div class="frame-10">
              <div class="text-wrapper-4">Vegetables</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-11">
              <div class="text-wrapper-4">Greens</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-12">
              <div class="text-wrapper-4">Grains</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-13">
              <div class="text-wrapper-4">Fruits</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-14">
              <div class="text-wrapper-4">Milk</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-15">
              <div class="text-wrapper-4">Eggs</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-16">
              <div class="text-wrapper-4"></div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-17">
              <div class="text-wrapper-4">Payjamas</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-18">
              <div class="text-wrapper-4">Jeans</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
          </div>
          </div>
        </div>
        <div class="frame-26">
          <div class="text-wrapper-19">Vegetables</div>
          <div class="text-wrapper-21">
          <div class="text-wrapper-21">
            <div class="toggle-container">
                <input type="radio" id="vendors" name="toggle" checked>
                <label for="vendors" class="toggle-label" onclick="toggleSlider(true)">Vendors</label>

                <input type="radio" id="products" name="toggle">
                <label for="products" class="toggle-label" onclick="toggleSlider(false)">Products</label>

                <div class="toggle-slider"></div>
            </div>
          </div>
          </div>
        </div>
        <div class="task-baar">
          <div class="overlap-15">
            <div class="frame-27">
              <div class="navbar">
                <div class="text-wrapper-22 shop-button"><a href="index.php">Shop</a></div>
                <div class="text-wrapper-22 vegetables-button"><a href="vegetablesProducts.php">Vegetables</a></div>
                <div class="text-wrapper-22 fruits-button"><a href="fruitsProducts.php">Fruits</a></div>
                <div class="text-wrapper-23 dairy-button"><a href="dairyProducts.php">Dairy</a></div>
              </div>
              <div class="search-bar">
                <form id="search-form"  class="search-form" action="search-results.php" method="post">
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
            <div class="rectangle-13"></div>
            <img class="nav-logo" src="img/urban-food-logo.png" />
          </div>
        </div>
          <div>
            <p class="text-wrapper-33">Copyright © 2024 Beliyo Fasion Pvt Ltd. All rights reserved.</p>
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

      // Prepare the SQL block to call the stored procedure
      $sql = "BEGIN SYSTEM.get_all_sellers(:cursor); END;";
      $stid = oci_parse($conn, $sql);

      $cursor = oci_new_cursor($conn);
      oci_bind_by_name($stid, ":cursor", $cursor, -1, OCI_B_CURSOR);

      // Execute the procedure
      if (!oci_execute($stid)) {
          $e = oci_error($stid);
          die("Error executing procedure: " . $e['message']);
      }

      // Execute the cursor to fetch data
      oci_execute($cursor);

      // Process results correctly from $cursor
      while ($row = oci_fetch_assoc($cursor)) {
          echo '<div class="overlap-16" id="' . htmlspecialchars($row["SELLER_ID"]) . '">
              <a href="seller.php?seller_id=' . htmlspecialchars($row["SELLER_ID"]) . '">
                  <img class="element-pgr" src="sellerImages/seller-' . htmlspecialchars($row["SELLER_ID"]) .'.jpg" />
                  <div class="product-6">
                      <div class="frame"><img class="heart" src="img/image.svg" /></div>
                      <div class="frame-2">
                          <div class="frame-3">
                              <div class="black-sweatshirt">' . htmlspecialchars($row["SELLER_NAME"]) . '</div>
                              <div class="jhanvi-s-brand">Priya’s&nbsp;&nbsp;Brand</div>
                          </div>
                          <div class="element-wrapper"><div class="text-wrapper">'. htmlspecialchars($row["FARM_ADDRESS"]) . '</div></div>
                      </div>
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
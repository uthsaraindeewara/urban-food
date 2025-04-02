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
                <div class="component notification" id="notifications">
                  <img class="img-2" src="img/icons8-notification-50.png" onclick="toggleDropdownnotification()" />
                  <div class="dropdown-content" id="notification-dropdown-content">
                    <div class="notifications-header">
                        <span>Notifications</span>
                    </div>
                    <div class="notifications-list">
                    <?php
                      // Check if user is logged in
                      if (isset($_SESSION['user'])) {
                                              // Database connection
                      $host = 'localhost:3306';
                      $user = 'root';
                      $password = '';
                      $dbname = 'storedb';

                      $conn = new mysqli($host, $user, $password, $dbname);

                      if ($conn->connect_error) {
                          die("Connection failed: " . $conn->connect_error);
                      }

                      // Assuming the customer ID is stored in session (when the user logs in)
                      $cusID = $_SESSION['user']['cusID'];

                      // If the remove button was clicked, delete the notification
                      if (isset($_POST['delete_notification']) && isset($_POST['notificationID'])) {
                        $notificationID = intval($_POST['notificationID']);

                        // Delete from customer_notification table
                        $sql1 = "DELETE FROM customer_notification WHERE notificationID = ?";
                        $stmt1 = $conn->prepare($sql1);
                        $stmt1->bind_param("i", $notificationID);
                        $stmt1->execute();

                        // Delete from notification table
                        $sql2 = "DELETE FROM notification WHERE notificationID = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $notificationID);
                        $stmt2->execute();

                        // Close the statements
                        $stmt1->close();
                        $stmt2->close();
                    }

                      // Fetch notifications for the logged-in customer
                      $sql = "SELECT n.notificationID, n.time, n.description, n.date 
                              FROM notification n
                              INNER JOIN customer_notification cn ON n.notificationID = cn.notificationID
                              WHERE cn.cusID = ?";
                      $stmt = $conn->prepare($sql);
                      $stmt->bind_param("i", $cusID);
                      $stmt->execute();
                      $result = $stmt->get_result();

                      // Check if there are any notifications
                      if ($result->num_rows > 0): 
                        ?>
                            <div class="notifications-list">
                                <?php while ($notification = $result->fetch_assoc()): ?>
                                    <div class="notification-item">
                                        <div class="notification-description">
                                          <?php echo htmlspecialchars($notification['description']); ?>
                                          <form method="post">
                                            <input type="hidden" name="notificationID" value="<?php echo $notification['notificationID']; ?>">
                                            <button type="submit" name="delete_notification" class="remove-notification">Remove</button>
                                        </form>
                                        </div>
                                        <div class="notification-details">
                                          <div class="notification-time"><?php echo htmlspecialchars($notification['time']); ?></div>
                                          <div class="notification-date"><?php echo htmlspecialchars($notification['date']); ?></div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-notifications">No notifications available</div>
                        <?php endif;

                      $stmt->close();
                      $conn->close();
                      }
                      ?>
                    </div>
                </div>
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
    // Connect to the database
    $servername = "localhost:3306";
    $username = "root";
    $password = "";
    $dbname = "storedb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get product data
    $sql = "
    SELECT 
        p.productID AS id,
        p.productName AS name,
        p.catagory AS brand,
        i.name AS imageName,
        p.price
    FROM 
        product p
    JOIN 
        (SELECT MIN(imageID) AS imageID, productID FROM image GROUP BY productID) AS first_image
        ON p.productID = first_image.productID
    JOIN 
        image i 
        ON i.imageID = first_image.imageID;
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            // Dynamically generate the HTML for each product
            echo '<div class="overlap-16" id="' . htmlspecialchars($row["id"]) . '">
      <a href="product.php?id=' . htmlspecialchars($row["id"]) . '">
          <img class="element-pgr" src="productImages/' . htmlspecialchars($row["imageName"]) . '" />
          <div class="product-6">
              <div class="frame"><img class="heart" src="img/image.svg" /></div>
              <div class="frame-2">
                  <div class="frame-3">
                      <div class="black-sweatshirt">' . htmlspecialchars($row["name"]) . '</div>
                      <div class="jhanvi-s-brand">Priya’s&nbsp;&nbsp;Brand</div>
                  </div>
                  <div class="element-wrapper"><div class="text-wrapper">'. htmlspecialchars($row["price"]) . '</div></div>
              </div>
          </div>
      </a>
  </div>';
        }
    } else {
        echo "No products found.";
    }

    $conn->close();
    ?>
</div>
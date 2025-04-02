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
            <div class="text-wrapper-2">Filter</div>
            <img class="filter" src="img/filter.svg" />
          </div>
          <div class="frame-5">
            <div class="text-wrapper-2">Price</div>
            <img class="icon" src="img/icon.svg" />
          </div>
          <div class="frame-6">
            <div class="text-wrapper-2">Size</div>
            <img class="icon" src="img/icon.svg" />
          </div>
          <div class="frame-7">
            <div class="text-wrapper-2">Dress Style</div>
            <img class="icon" src="img/icon.svg" />
          </div>
          <div class="frame-8">
            <div class="text-wrapper-3">Colors</div>
            <img class="icon" src="img/icon.svg" />
          </div>
          <div class="frame-9">
            <div class="frame-10">
              <div class="text-wrapper-4">Tops</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-11">
              <div class="text-wrapper-4">Printed T-shirts</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-12">
              <div class="text-wrapper-4">Plain T-shirts</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-13">
              <div class="text-wrapper-4">Kurti</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-14">
              <div class="text-wrapper-4">Boxers</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-15">
              <div class="text-wrapper-4">Full sleeve T-shirts</div>
              <img class="icon-2" src="img/icon-3.svg" />
            </div>
            <div class="frame-16">
              <div class="text-wrapper-4">Joggers</div>
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
          <div class="frame-19">
            <div class="text-wrapper-4">Classic</div>
            <img class="icon-2" src="img/icon-3.svg" />
            <div class="text-wrapper-5">Casual</div>
            <div class="text-wrapper-6">Business</div>
            <div class="text-wrapper-7">Sport</div>
            <div class="text-wrapper-8">Elegant</div>
            <div class="text-wrapper-9">Formal (evening)</div>
            <img class="icon-3" src="img/icon-3.svg" />
            <img class="icon-4" src="img/icon-3.svg" />
            <img class="icon-5" src="img/icon-3.svg" />
            <img class="icon-6" src="img/icon-3.svg" />
            <img class="icon-7" src="img/icon-3.svg" />
          </div>
          <div class="frame-20">
            <div class="slider-input-one">
              <div class="overlap-group-2">
                <div class="bg"></div>
                <div class="bar-left"></div>
                <img class="left" src="img/left.svg" />
                <div class="right"></div>
              </div>
            </div>
            <div class="frame-21">
              <div class="div-wrapper"><div class="text-wrapper-10">Rs. 700</div></div>
              <div class="frame-22"><div class="text-wrapper-11">Rs. 6000</div></div>
            </div>
          </div>
          <div class="frame-23">
            <div class="overlap-6"><div class="text-wrapper-12">XXS</div></div>
            <div class="overlap-7"><div class="text-wrapper-13">S</div></div>
            <div class="overlap-8"><div class="text-wrapper-14">XXL</div></div>
            <div class="overlap-9"><div class="text-wrapper-15">XL</div></div>
            <div class="overlap-10"><div class="text-wrapper-16">M</div></div>
            <div class="overlap-11"><div class="text-wrapper-17">3XL</div></div>
            <div class="overlap-12"><div class="text-wrapper-15">XS</div></div>
            <div class="overlap-13"><div class="text-wrapper-13">L</div></div>
            <div class="overlap-14"><div class="text-wrapper-17">4XL</div></div>
          </div>
          <div class="frame-24">
            <div class="frame-25">
              <div class="rectangle"></div>
              <div class="text-wrapper-18">Purple</div>
              <div class="rectangle-2"></div>
              <div class="text-wrapper-18">Navy</div>
              <div class="rectangle-3"></div>
              <div class="text-wrapper-18">Yellow</div>
            </div>
            <div class="frame-25">
              <div class="rectangle-4"></div>
              <div class="text-wrapper-18">Black</div>
              <div class="rectangle-5"></div>
              <div class="text-wrapper-18">White</div>
              <div class="rectangle-6"></div>
              <div class="text-wrapper-18">Grey</div>
            </div>
            <div class="frame-25">
              <div class="rectangle-7"></div>
              <div class="text-wrapper-18">Red</div>
              <div class="rectangle-8"></div>
              <div class="text-wrapper-18">Broom</div>
              <div class="rectangle-9"></div>
              <div class="text-wrapper-18">Pink</div>
            </div>
            <div class="frame-25">
              <div class="rectangle-10"></div>
              <div class="text-wrapper-18">Orange</div>
              <div class="rectangle-11"></div>
              <div class="text-wrapper-18">Green</div>
              <div class="rectangle-12"></div>
              <div class="text-wrapper-18">Blue</div>
            </div>
          </div>
        </div>
        <div class="frame-26">
          <div class="text-wrapper-19">Women’s Clothing</div>
          <div class="text-wrapper-20">New</div>
          <div class="text-wrapper-21">Recommended</div>
        </div>
        <div class="task-baar">
          <div class="overlap-15">
            <div class="frame-27">
              <div class="navbar">
                <div class="text-wrapper-22 shop-button"><a href="index.php">Shop</a></div>
                <div class="text-wrapper-22 mens-button"><a href="mensProducts.php">Men</a></div>
                <div class="text-wrapper-22 womens-button"><a href="womensProducts.php">Women</a></div>
                <div class="text-wrapper-23 kids-button"><a href="kidsProducts.php">Kids</a></div>
              </div>
              <div class="search-bar">
                <form id="search-form"  class="search-form" action="search-results.php">
                  <input type="text" id="search" class="search" placeholder="Search for products">
                  <button class="search-button"><img src="img/search.svg"></button>
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
            <img class="images" src="img/images-1-1.png" />
          </div>
        </div>
          <div>
            <img class="line" src="img/line-6.svg" />
            <img class="line-2" src="img/line-6.svg" />
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
            ON i.imageID = first_image.imageID
        WHERE 
            p.catagory = ?
    ";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    // Bind the parameters (same value for both placeholders)
    $searchTerm = "Kid's";
    $stmt->bind_param("s", $searchTerm);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

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
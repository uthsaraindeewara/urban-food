<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="cart.css" />
    <link rel="stylesheet" href="cart-items.css">
  </head>
  <body>
  <?php
session_start();

// Database connection
$conn = new mysqli("localhost:3306", "root", "", "storedb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user']['name'])) {
    die("User not logged in");
}

$cusID = $_SESSION['user']['cusID'];

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['wishlist_id']) && isset($_GET['productID'])) {
    $wishlistId = $_GET['wishlist_id'];
    $productID = $_GET['productID'];
    $size = $_GET['size'];

    $deleteStmt = $conn->prepare("DELETE FROM product_wishlist WHERE wishlist = ? AND productID = ? AND size = ?");
    $deleteStmt->bind_param("iis", $wishlistId, $productID, $size);
    $deleteStmt->execute();
    $deleteStmt->close();

    header("Location: wish-list.php"); // Refresh the page after deletion
    exit();
}

// Fetch cart details for the logged-in user
$query = "SELECT p.productName, p.price, pw.quantity, pw.size, MIN(i.name) AS image_name, w.wishlist_id, p.productID, pq.quantity AS availableStock
          FROM wishlist w
          JOIN product_wishlist pw ON w.wishlist_id = pw.wishlist_id
          JOIN product p ON pw.product_id = p.productID
          JOIN image i ON p.productID = i.productID
          JOIN product_quantity pq ON p.productID = pq.product_id AND pw.size = pq.size
          WHERE w.cus_id = ?
          GROUP BY p.productID, pw.size, pw.quantity, p.productName, p.price, w.wishlist_id, pq.quantity";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $cusID);
$stmt->execute();
$result = $stmt->get_result();

$cartTotal = 0;
$deliveryCharge = 250;
?>
    <div class="cart-page">
      <div class="div">
        <div class="cart-products">
          <div class="frame-wrapper">
            <div class="frame-4">
              <div class="text-wrapper-3">PRODUCT DETAILS</div>
              <div class="frame-5">
                <div class="text-wrapper-3">PRICE</div>
                <div class="text-wrapper-3">AVAILABILITY</div>
                <div class="text-wrapper-3">BUY</div>
                <div class="action">ACTION</div>
              </div>
            </div>
          </div>
          <div class="cart-container">
            <?php while ($row = $result->fetch_assoc()) :
              $itemTotal = $row['price'] * $row['quantity'];
              $cartTotal += $itemTotal;
            ?>

                <div class="cart-item">
                    <!-- Product Image -->
                    <img src="productImages/<?php echo $row['image_name']; ?>" alt="Product Image">
        
                    <!-- Product Information -->
                    <div class="cart-item-info">
                        <div class="product-name"><?php echo htmlspecialchars($row['productName']); ?></div>
                        <div class="product-size">Size: <?php echo htmlspecialchars($row['size']); ?></div>
                    </div>

                    <!-- Price -->
                    <div class="cart-item-price">Rs. <?php echo number_format($row['price'], 2); ?></div>

                    <!-- Stock Availability -->
                    <div class="cart-item-availability" style="color: <?php echo ($row['availableStock'] > 0) ? 'green' : 'red'; ?>">
                        <?php echo ($row['availableStock'] > 0) ? 'In-Stock' : 'Out-of-Stock'; ?>
                    </div>

                    <!-- Buy Button (show always, disabled if out of stock) -->
                    <div class="cart-item-buy">
                        <a href="product.php?id=<?php echo $row['productID']; ?>" 
                        class="buy-button <?php echo ($row['availableStock'] > 0) ? '' : 'disabled'; ?>" 
                        <?php echo ($row['availableStock'] > 0) ? '' : 'aria-disabled="true" tabindex="-1"'; ?>>
                        Buy
                        </a>
                    </div>
        
                    <!-- Delete Button -->
                    <div class="cart-item-delete">
                        <a href="?delete=true&wishlist_id=<?php echo $row['wishlist_id']; ?>&productID=<?php echo $row['productID']; ?>&size=<?php echo urlencode($row['size']); ?>">
                            <img src="img/deletecon.svg" alt="Delete">
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        </div>
        <div class="div-2">
          <div class="div-2">
            <div class="overlap-3">
              <div class="frame-13">
                <div class="navbar">
                  <div class="text-wrapper-24">Shop</div>
                  <div class="text-wrapper-24">Men</div>
                  <div class="text-wrapper-24">Women</div>
                  <div class="text-wrapper-25">Kids</div>
                </div>
                <button class="button-3">
                  <img class="img-2" src="img/search.svg" /> <button class="button-4">Search</button>
                </button>
                <div class="frame-14">
                  <div class="component"><img class="img-2" src="img/heart-2.svg" /></div>
                  <div class="component"><img class="img-2" src="img/heart.svg" /></div>
                  <div class="shopping-cart-wrapper"><img class="img-2" src="img/shopping-cart.svg" /></div>
                </div>
              </div>
              <div class="rectangle"></div>
            </div>
          </div>
          <div class="div-2">
            <div class="overlap-3">
              <div class="frame-13">
                <div class="navbar">
                  <div class="text-wrapper-24">Shop</div>
                  <div class="text-wrapper-24">Men</div>
                  <div class="text-wrapper-24">Women</div>
                  <div class="text-wrapper-25">Kids</div>
                </div>
                <button class="button-3">
                  <img class="img-2" src="img/search.svg" /> <button class="button-4">Search</button>
                </button>
                <div class="frame-14">
                  <div class="component"><img class="img-2" src="img/heart-2.svg" /></div>
                  <div class="component"><img class="img-2" src="img/heart.svg" /></div>
                  <div class="shopping-cart-wrapper"><img class="img-2" src="img/shopping-cart.svg" /></div>
                </div>
              </div>
              <div class="rectangle"></div>
              <img class="images" src="img/images-1-1.png" />
            </div>
          </div>
        </div>
        <div class="need-help-section">
          <div class="frame-15">
            <img class="icon-4" src="img/icon-2.svg" />
            <div class="frame-16">
              <div class="frame-17">
                <div class="text-wrapper-26">Location</div>
                <div class="frame-18">
                  <p class="text-wrapper-27">No. 24, Negambo Road, Ragama</p>
                  <div class="text-wrapper-28">No. 62, Palawaththa, Baththaramulla</div>
                </div>
              </div>
              <div class="frame-19">
                <div class="text-wrapper-26">More Info</div>
                <div class="frame-20">
                  <div class="text-wrapper-29">Term and Conditions</div>
                  <div class="text-wrapper-30">Privacy Policy</div>
                  <div class="text-wrapper-31">Shipping Policy</div>
                  <div class="text-wrapper-32">Sitemap</div>
                </div>
              </div>
              <div class="frame-21">
                <div class="text-wrapper-26">Need Help</div>
                <div class="frame-22">
                  <div class="text-wrapper-29">Contact Us</div>
                  <div class="text-wrapper-30">Track Order</div>
                  <div class="text-wrapper-31">Returns &amp; Refunds</div>
                  <div class="text-wrapper-32">FAQ&#39;s</div>
                  <div class="text-wrapper-33">Career</div>
                </div>
              </div>
              <div class="frame-23">
                <div class="text-wrapper-26">Company</div>
                <div class="frame-24">
                  <div class="text-wrapper-29">About Us</div>
                  <div class="text-wrapper-30">euphoria Blog</div>
                  <div class="text-wrapper-31">euphoriastan</div>
                  <div class="text-wrapper-32">Collaboration</div>
                  <div class="text-wrapper-33">Media</div>
                </div>
              </div>
            </div>
            <div class="text-wrapper-34">Popular Categories</div>
            <div class="frame-25">
              <div class="group"><img class="vector" src="img/image.svg" /></div>
              <div class="group"><img class="vector-2" src="img/vector.svg" /></div>
              <div class="group"><img class="vector-3" src="img/vector-2.svg" /></div>
              <div class="overlap-group-wrapper">
                <div class="overlap-group-2"><div class="text-wrapper-35">in</div></div>
              </div>
            </div>
            <img class="line-3" src="img/line-6.svg" />
            <img class="line-4" src="img/line-6.svg" />
            <p class="text-wrapper-36">Copyright © 2024 Beliyo Fasion Pvt Ltd. All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
    </div>
  </body>
</html>
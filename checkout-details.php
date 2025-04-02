<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="globals.css" />
    <link rel="stylesheet" href="styleguide.css" />
    <link rel="stylesheet" href="checkout.css" />
    <link rel="stylesheet" href="checkout-details.css">
  </head>
  <body>
    <div class="checkout">
      <div class="div">
        <div class="container">
      <?php
session_start();

// Assuming you have stored the customer ID in the session
$customerID = $_SESSION['user']['cusID'];

// MySQLi connection
$mysqli = new mysqli("localhost:3306", "root", "", "storedb");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch cart items for the customer
$sql = "SELECT product_cart.productID, product_cart.quantity, product_cart.size, product.productName, product.price, discount.amount AS discount
        FROM cart
        INNER JOIN product_cart ON cart.cartID = product_cart.cartID
        INNER JOIN product ON product_cart.productID = product.ProductID
        LEFT JOIN discount on product.productID = discount.productID
        WHERE cart.cusID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();
$result = $stmt->get_result();

// Initialize variables for totals
$total_discount = 0;
$net_total = 0;
$cart_output = '';

while ($row = $result->fetch_assoc()) {
    $product_name = $row['productName'];
    $size = $row['size'];
    $price = $row['price'];
    $discount = $row['discount'];
    $quantity = $row['quantity'];
    
    // Calculate price after discount
    $price_after_discount = $price - $discount;
    $total_for_item = $price_after_discount * $quantity;

    // Update totals
    $total_discount += $discount * $quantity;
    $net_total += $total_for_item;

    // Add to the output
    $cart_output .= "
    <tr>
        <td>{$product_name}</td>
        <td>{$size}</td>
        <td>{$price}</td>
        <td>{$discount}</td>
        <td>{$quantity}</td>
        <td>{$total_for_item}</td>
    </tr>";
}

// Close the statement
$stmt->close();

// Check if the user has an address
$sql = "SELECT address, contactNo FROM customer WHERE cusID = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $customerID);
$stmt->execute();

// Bind the results to variables
$stmt->bind_result($address, $contactNo);
$stmt->fetch();
$stmt->close();
$mysqli->close();
?>
<table border="1">
    <thead>
        <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Quantity</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $cart_output; ?>
    </tbody>
</table>

<p>Total Discount: <?php echo $total_discount; ?></p>
<p>Net Total: <?php echo $net_total; ?></p>

<form method="POST" action="process-payment.php">
    <?php if (empty($address)): ?>
      <div class="address-section">
          <label for="address">Enter your address:</label>
          <input type="text" id="address" name="address" required>
      </div>
    <?php endif; ?>
    <?php if (empty($contactNo)): ?>
      <div class="address-section">
          <label for="contactNo">Enter your Contact No:</label>
          <input type="text" id="contactNo" name="contactNo" required>
      </div>
    <?php endif; ?> 
    <div class="payment-method">
      <div class="payment-title">
        Payment Method
      </div>
      <label>
          <input type="radio" name="payment_method" value="credit_card" required> Credit Card
      </label><br>
      <label>
          <input type="radio" name="payment_method" value="paypal"> PayPal
      </label><br><br>
    </div>

    <!-- Pay button -->
    <button type="submit" name="payBtn">Pay</button>
</form>
</div>
        <img class="line-6" src="img/line-8.svg" />
        <div class="overlap-4">
          <img class="line-7" src="img/line-8.svg" />
          <div class="task-baar">
            <div class="overlap-5">
              <div class="frame-5">
                <div class="navbar">
                  <div class="text-wrapper-14">Shop</div>
                  <div class="text-wrapper-14">Men</div>
                  <div class="text-wrapper-14">Women</div>
                  <div class="text-wrapper-15">Kids</div>
                </div>
                <button class="button">
                  <img class="img-2" src="img/search.svg" /> <button class="button-2">Search</button>
                </button>
                <div class="frame-6">
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
        <div class="div-wrapper">
        </div>
        <div class="frame-30">
          <div class="text-wrapper-25">Home</div>
          <img class="left-stroke" src="img/left-stroke.svg" />
          <div class="text-wrapper-25">My Account</div>
          <img class="left-stroke" src="img/left-stroke.svg" />
          <div class="text-wrapper-26">Check Out</div>
        </div>
        <div class="product-description">
          <div class="text-wrapper-27">Check Out</div>
          <div class="rectangle-2"></div>
        </div>
        <div class="need-help-section">
          <div class="frame-31">
            <img class="icon" src="img/icon.svg" />
            <div class="frame-32">
              <div class="frame-33">
                <div class="text-wrapper-29">Location</div>
                <div class="frame-34">
                  <p class="text-wrapper-30">No. 24, Negambo Road, Ragama</p>
                  <div class="text-wrapper-31">No. 62, Palawaththa, Baththaramulla</div>
                </div>
              </div>
              <div class="frame-35">
                <div class="text-wrapper-29">More Info</div>
                <div class="frame-36">
                  <div class="text-wrapper-32">Term and Conditions</div>
                  <div class="text-wrapper-33">Privacy Policy</div>
                  <div class="text-wrapper-34">Shipping Policy</div>
                  <div class="text-wrapper-35">Sitemap</div>
                </div>
              </div>
              <div class="frame-37">
                <div class="text-wrapper-29">Need Help</div>
                <div class="frame-38">
                  <div class="text-wrapper-32">Contact Us</div>
                  <div class="text-wrapper-33">Track Order</div>
                  <div class="text-wrapper-34">Returns &amp; Refunds</div>
                  <div class="text-wrapper-35">FAQ&#39;s</div>
                  <div class="text-wrapper-36">Career</div>
                </div>
              </div>
              <div class="frame-39">
                <div class="text-wrapper-29">Company</div>
                <div class="frame-40">
                  <div class="text-wrapper-32">About Us</div>
                  <div class="text-wrapper-33">euphoria Blog</div>
                  <div class="text-wrapper-34">euphoriastan</div>
                  <div class="text-wrapper-35">Collaboration</div>
                  <div class="text-wrapper-36">Media</div>
                </div>
              </div>
            </div>
            <div class="text-wrapper-37">Popular Categories</div>
            <div class="frame-41">
              <div class="group"><img class="vector" src="img/image.svg" /></div>
              <div class="group"><img class="vector-2" src="img/vector.svg" /></div>
              <div class="group"><img class="vector-3" src="img/vector-2.svg" /></div>
              <div class="group-2">
                <div class="overlap-group-3"><div class="text-wrapper-38">in</div></div>
              </div>
            </div>
            <img class="line-13" src="img/line-6.svg" />
            <img class="line-14" src="img/line-6.svg" />
            <p class="text-wrapper-39">Copyright © 2024 Beliyo Fasion Pvt Ltd. All rights reserved.</p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
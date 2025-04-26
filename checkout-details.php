<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Checkout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .checkout-container {
      width: 100%;
      max-width: 600px;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2e7d32; /* Dark green */
      border-bottom: 2px solid #fbc02d; /* Yellow */
      padding-bottom: 10px;
      text-align: center;
    }
    .summary {
      font-size: 18px;
      margin-top: 10px;
      padding: 15px;
      background-color: #e8f5e9; /* Light green */
      border-left: 4px solid #fbc02d; /* Yellow */
      text-align: center;
    }
    .summary strong {
      color: #2e7d32; /* Dark green */
    }
    .payment-method {
      margin-top: 30px;
      padding: 15px;
      background-color: #e8f5e9; /* Light green */
      border-radius: 5px;
    }
    .payment-method label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
      color: #2e7d32; /* Dark green */
    }
    input[type="radio"] {
      accent-color: #fbc02d; /* Yellow */
      margin-right: 10px;
    }
    button {
      margin-top: 20px;
      padding: 12px 25px;
      font-size: 16px;
      cursor: pointer;
      background-color: #fbc02d; /* Yellow */
      color: #2e7d32; /* Dark green */
      border: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
      display: block;
      width: 100%;
    }
    button:hover {
      background-color: #f9a825; /* Darker yellow */
    }
    .form-group {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="checkout-container">
  <h2>Checkout</h2>

  <form method="post" action="process_payment.php">
    <div class="form-group">
      <?php
      session_start();
      $customerID = $_SESSION['user']['cusID'];
      $mysqli = new mysqli("localhost:3306", "root", "", "storedb");
      if ($mysqli->connect_error) {
          die("Connection failed: " . $mysqli->connect_error);
      }

      $sql = "SELECT product_cart.quantity, product.price, discount.amount AS discount
              FROM cart
              INNER JOIN product_cart ON cart.cartID = product_cart.cartID
              INNER JOIN product ON product_cart.productID = product.ProductID
              LEFT JOIN discount ON product.productID = discount.productID
              WHERE cart.cusID = ?";
      $stmt = $mysqli->prepare($sql);
      $stmt->bind_param("i", $customerID);
      $stmt->execute();
      $result = $stmt->get_result();

      $total_discount = 0;
      $net_total = 0;

      while ($row = $result->fetch_assoc()) {
          $discount = $row['discount'] ?? 0;
          $price_after_discount = $row['price'] - $discount;
          $total_item = $price_after_discount * $row['quantity'];
          $total_discount += $discount * $row['quantity'];
          $net_total += $total_item;
      }

      $stmt->close();
      $mysqli->close();
      ?>

      <!-- Summary only -->
      <p class="summary">Total Discount: <strong>Rs. <?= number_format($total_discount, 2) ?></strong></p>
      <p class="summary">Net Total: <strong>Rs. <?= number_format($net_total, 2) ?></strong></p>
    </div>

    <!-- Payment Method Selection -->
    <div class="payment-method">
    <div class="payment-title">Payment Method</div>
    <label>
      <input type="radio" name="payment_method" value="credit_card" required onclick="goToCardPage()"> Credit Card
    </label>
    <label>
      <input type="radio" name="payment_method" value="paypal" required onclick="goToPayPalPage()"> PayPal
    </label><br><br>
  </div>

  <!-- Single Pay button here -->
 
</form>

<!-- JS Redirect -->
<script>
  function goToCardPage() {
    window.location.href = "card.php";
  }
  function goToPayPalPage() {
    window.location.href = "paypal.php";
  }
</script>
    
  </form>
</div>

</body>
</html>
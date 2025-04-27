<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include "connection.php";

$customerID = $_SESSION['user']['userID'];

if (!isset($_GET['orderID'])) {
    echo "Order ID not provided.";
    exit();
}

$orderID = (int) $_GET['orderID'];

$total_discount = 0;
$net_total = 0;
$cartItems = [];

if ($conn) {
    $stmt = oci_parse($conn, "BEGIN GetOrderDetails(:orderID, :result); END;");
    $resultCursor = oci_new_cursor($conn);
    oci_bind_by_name($stmt, ":orderID", $orderID);
    oci_bind_by_name($stmt, ":result", $resultCursor, -1, OCI_B_CURSOR);

    oci_execute($stmt);
    oci_execute($resultCursor);

    while ($row = oci_fetch_assoc($resultCursor)) {
        $productName = $row['PRODUCT_NAME'];
        $price = $row['PRICE'];
        $quantity = $row['QUANTITY'];
        $total = $row['TOTAL']; // price * quantity

        $net_total += $total;

        $cartItems[] = [
            'productName' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'total' => $total
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
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
      color: #2e7d32;
      border-bottom: 2px solid #fbc02d;
      padding-bottom: 10px;
      text-align: center;
    }
    .summary {
      font-size: 18px;
      margin-top: 10px;
      padding: 15px;
      background-color: #e8f5e9;
      border-left: 4px solid #fbc02d;
      text-align: center;
    }
    .summary strong {
      color: #2e7d32;
    }
    .payment-method {
      margin-top: 30px;
      padding: 15px;
      background-color: #e8f5e9;
      border-radius: 5px;
    }
    .payment-method label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
      color: #2e7d32;
    }
    input[type="radio"] {
      accent-color: #fbc02d;
      margin-right: 10px;
    }
    button {
      margin-top: 20px;
      padding: 12px 25px;
      font-size: 16px;
      cursor: pointer;
      background-color: #fbc02d;
      color: #2e7d32;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
      display: block;
      width: 100%;
    }
    button:hover {
      background-color: #f9a825;
    }
    .form-group {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<div class="checkout-container">
  <h2>Checkout</h2>

  <form id="checkoutForm" method="post">
    <input type="hidden" name="orderID" value="<?= htmlspecialchars($orderID) ?>">
    
    <div class="form-group">
      <?php foreach ($cartItems as $item): ?>
        <p class="summary">
          <?= htmlspecialchars($item['productName']) ?> (x<?= $item['quantity'] ?>) - 
          Rs. <?= number_format($item['total'], 2) ?>
        </p>
      <?php endforeach; ?>

      <p class="summary">Net Total: <strong>Rs. <?= number_format($net_total, 2) ?></strong></p>
    </div>

    <!-- Payment Method -->
    <div class="payment-method">
      <div class="payment-title">Payment Method</div>
      <label>
        <input type="radio" name="payment_method" value="credit_card" required> Credit Card
      </label>
      <label>
        <input type="radio" name="payment_method" value="paypal" required> PayPal
      </label>
    </div>

    <button type="submit">Pay Now</button>
    <button>Delivary Details</button>
  </form>
</div>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission first
  
  const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
  
  if (!paymentMethod) {
    alert("Please select a payment method.");
    return;
  }

  let actionUrl = '';

  if (paymentMethod.value === 'credit_card') {
    actionUrl = 'card.php';
  } else if (paymentMethod.value === 'paypal') {
    actionUrl = 'paypal.php';
  }

  // Dynamically set the form action
  this.action = actionUrl;

  // Now submit the form manually
  this.submit();
});
</script>

</body>
</html>
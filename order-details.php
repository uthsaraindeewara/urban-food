<?php
session_start();

// Database connection
$conn = oci_connect("system", "sys112233", "//localhost/XEPDB1");
if (!$conn) {
    die("Database connection failed: " . oci_error()['message']);
}

// Validate order 
if (!isset($_GET['order_id'])) {
    die("Invalid request: No order ID provided.");
}

$orderID = $_GET['order_id'];

// get order details 
$orderDetailsStmt = oci_parse($conn, "BEGIN GetOrderDetails(:orderID, :result); END;");
oci_bind_by_name($orderDetailsStmt, ":orderID", $orderID);
$resultCursor = oci_new_cursor($conn);
oci_bind_by_name($orderDetailsStmt, ":result", $resultCursor, -1, OCI_B_CURSOR);
oci_execute($orderDetailsStmt);
oci_execute($resultCursor);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="order-details.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
  </head>
  <body>
    <div class="order-details-container">
      <h1>Order Details</h1>
      <p><strong>Order ID:</strong> <?php echo htmlspecialchars($orderID); ?></p>
      <table class="order-details-table">
        <thead>
          <tr>
            <th>Item ID</th>
            <th>Product Name</th>
            <th>Unit Price</th>
            <th>Quantity</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php while (($row = oci_fetch_assoc($resultCursor)) != false): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['ORDER_ITEM_ID']); ?></td>
            <td><?php echo htmlspecialchars($row['PRODUCT_NAME']); ?></td>
            <td>Rs. <?php echo number_format($row['PRICE'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['QUANTITY']); ?></td>
            <td>Rs. <?php echo number_format($row['TOTAL'], 2); ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <a href="orders.php" class="back-button">Back to Orders</a>
    </div>
  </body>
</html>
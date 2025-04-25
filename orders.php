<?php
session_start();

// Database connection
$conn = oci_connect("system", "sys112233", "//localhost/XEPDB1");
if (!$conn) {
    die("Database connection failed: " . oci_error()['message']);
}

$customerID = 1; 

// remove order
if (isset($_POST['removeOrder'])) {
    $orderID = $_POST['removeOrder'];
    $removeStmt = oci_parse($conn, "BEGIN RemoveOrder(:orderID); END;");
    oci_bind_by_name($removeStmt, ":orderID", $orderID);
    oci_execute($removeStmt);
}

// get ongoing orders
$ongoingOrdersStmt = oci_parse($conn, "BEGIN GetOrders(:customerID, 'ONGOING', :result); END;");
oci_bind_by_name($ongoingOrdersStmt, ":customerID", $customerID);
$ongoingCursor = oci_new_cursor($conn);
oci_bind_by_name($ongoingOrdersStmt, ":result", $ongoingCursor, -1, OCI_B_CURSOR);
oci_execute($ongoingOrdersStmt);
oci_execute($ongoingCursor);

// get completed orders
$completedOrdersStmt = oci_parse($conn, "BEGIN GetOrders(:customerID, 'COMPLETED', :result); END;");
oci_bind_by_name($completedOrdersStmt, ":customerID", $customerID);
$completedCursor = oci_new_cursor($conn);
oci_bind_by_name($completedOrdersStmt, ":result", $completedCursor, -1, OCI_B_CURSOR);
oci_execute($completedOrdersStmt);
oci_execute($completedCursor);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="orders.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
  </head>
  <body>
    <div class="orders-container">
      <h1>My Orders</h1>
      <h2>Ongoing Orders</h2>
      <form method="POST" action="">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Ordered Date</th>
              <th>Total Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while (($row = oci_fetch_assoc($ongoingCursor)) != false): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['ORDER_ID']); ?></td>
              <td><?php echo htmlspecialchars($row['ORDER_DATE']); ?></td>
              <td>Rs. <?php echo number_format($row['AMOUNT'], 2); ?></td>
              <td>
                <a href="order-details.php?order_id=<?php echo urlencode($row['ORDER_ID']); ?>" class="btn-link">View Details</a>
                <button type="submit" name="removeOrder" value="<?php echo htmlspecialchars($row['ORDER_ID']); ?>" class="btn-remove">Remove</button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </form>

      <h2>Completed Orders</h2>
      <form method="POST" action="">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Ordered Date</th>
              <th>Total Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while (($row = oci_fetch_assoc($completedCursor)) != false): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['ORDER_ID']); ?></td>
              <td><?php echo htmlspecialchars($row['ORDER_DATE']); ?></td>
              <td>Rs. <?php echo number_format($row['AMOUNT'], 2); ?></td>
              <td>
              <a href="order-details.php?order_id=<?php echo urlencode($row['ORDER_ID']); ?>" class="btn-link">View Details</a>
              <button type="submit" name="removeOrder" value="<?php echo htmlspecialchars($row['ORDER_ID']); ?>" class="btn-remove">Remove</button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </form>
    </div>
  </body>
</html>
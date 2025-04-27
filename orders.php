<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$customerID = $_SESSION['user']['userID'];

include "connection.php";

 
if (isset($_POST['markAsDelivered'])) {
    $orderID = $_POST['markAsDelivered'];

    $updateOrderStatusStmt = oci_parse($conn, "BEGIN UpdateOrderStatus(:orderID, 'COMPLETED'); END;");
    oci_bind_by_name($updateOrderStatusStmt, ":orderID", $orderID);
    oci_execute($updateOrderStatusStmt);
}

//to get ongoin orders
$ongoingOrdersStmt = oci_parse($conn, "BEGIN GetOngoingOrders(:customerID, :result); END;");
oci_bind_by_name($ongoingOrdersStmt, ":customerID", $customerID);
$ongoingCursor = oci_new_cursor($conn);
oci_bind_by_name($ongoingOrdersStmt, ":result", $ongoingCursor, -1, OCI_B_CURSOR);
oci_execute($ongoingOrdersStmt);
oci_execute($ongoingCursor);

//to get completed orders
$completedOrdersStmt = oci_parse($conn, "BEGIN GetCompletedOrders(:customerID, :result); END;");
oci_bind_by_name($completedOrdersStmt, ":customerID", $customerID);
$completedCursor = oci_new_cursor($conn);
oci_bind_by_name($completedOrdersStmt, ":result", $completedCursor, -1, OCI_B_CURSOR);
oci_execute($completedOrdersStmt);
oci_execute($completedCursor);


if (isset($_POST['removeOrder'])) {
  $orderID = $_POST['removeOrder'];
  $removeStmt = oci_parse($conn, "BEGIN RemoveOrder(:orderID); END;");
  oci_bind_by_name($removeStmt, ":orderID", $orderID);
  oci_execute($removeStmt);
}
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
              <th>Status</th>
              <th><center>Actions</center></th>
            </tr>
          </thead>
          <tbody>
            <?php while (($row = oci_fetch_assoc($ongoingCursor)) != false): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['ORDER_ID']); ?></td>
              <td><?php echo htmlspecialchars($row['ORDER_DATE']); ?></td>
              <td>Rs. <?php echo number_format($row['TOTAL_AMOUNT'], 2); ?></td>
              <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
              <td>
                <center>
              <?php if ($row['STATUS'] == 'SHIPPED'): ?>
                <button type="submit" name="markAsDelivered" value="<?php echo htmlspecialchars($row['ORDER_ID']); ?>" style="background-color: #228b22; color: white; padding: 7px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                Mark as Delivered</button>

             <?php endif; ?>
             <a href="order-details.php?order_id=<?php echo urlencode($row['ORDER_ID']); ?>" class="btn-link">View Details</a>
             <button type="submit" name="removeOrder" value="<?php echo htmlspecialchars($row['ORDER_ID']); ?>" class="btn-remove">Remove</button>
              </center>
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
              <th><center>Actions</center></th>
            </tr>
          </thead>
          <tbody>
            <?php while (($row = oci_fetch_assoc($completedCursor)) != false): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['ORDER_ID']); ?></td>
              <td><?php echo htmlspecialchars($row['ORDER_DATE']); ?></td>
              <td>Rs. <?php echo number_format($row['TOTAL_AMOUNT'], 2); ?></td>
              <td>
                <center>
                <a href="order-details.php?order_id=<?php echo urlencode($row['ORDER_ID']); ?>" class="btn-link">View Details</a>
                <button type="submit" name="removeOrder" value="<?php echo htmlspecialchars($row['ORDER_ID']); ?>" class="btn-remove">Remove</button>
            </center>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </form>
    </div>
  </body>
</html>
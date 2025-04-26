<?php
session_start();

// Get session data or default to an empty array
$data = $_SESSION['delivery_data'] ?? [];

// Extract the required values from the session array
$customer_id = $data['customer_id'] ?? '';
$delivery_id = $data['delivery_id'] ?? '';
$name = $data['name'] ?? '';
$address = $data['address'] ?? '';
$tp = $data['tp'] ?? '';
$message = $data['message'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Place Delivery</title>
  <link rel="stylesheet" href="placedelivery.css">
</head>
<body>
  <div class="container">
    <h2>Delivery Details</h2>

    <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>

    <form action="process_delivery.php" method="POST">
      <label for="customer_id">Customer ID:</label>
      <input type="text" id="customer_id" name="customer_id" value="<?= htmlspecialchars($customer_id) ?>" required>

      <label for="delivery_id">Delivery ID:</label>
      <input type="text" id="delivery_id" name="delivery_id" value="<?= htmlspecialchars($delivery_id) ?>" readonly>

      <label for="order_id">Order ID:</label>
      <input type="text" id="order_id" name="order_id" required>

      <label for="name">Name:</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

      <label for="address">Address:</label>
      <input type="text" id="address" name="address" value="<?= htmlspecialchars($address) ?>" required>

      <label for="Tp">Tp:</label>
      <input type="text" id="Tp" name="Tp" value="<?= htmlspecialchars($tp) ?>" required>

      <div class="button-group">
        <button type="submit" name="action" value="submit" class="small-btn submit-btn">Submit</button>
        <button type="submit" name="action" value="delete" class="small-btn delete-btn">Delete</button>
        <button type="submit" name="action" value="update" class="small-btn update-btn">Update</button>
      </div>
    </form>

    
  </div>
</body>
</html>

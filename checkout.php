<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout-details.css">
</head>
<body>

<?php
session_start();

// Assuming you have stored the customer ID in the session
$customerID = 2;

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
?>

<h1>Your Order</h1>
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

<!-- Payment options -->
<h2>Payment Options</h2>
<form method="POST" action="process_payment.php">
    <label>
        <input type="radio" name="payment_method" value="credit_card" required> Credit Card
    </label><br>
    <label>
        <input type="radio" name="payment_method" value="paypal"> PayPal
    </label><br><br>

    <!-- Pay button -->
    <button type="submit" name="payBtn">Pay</button>
</form>

</body>
</html>
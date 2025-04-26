<?php
session_start();
$customerID = $_SESSION['user']['cusID'];
$mysqli = new mysqli("localhost:3306", "root", "", "storedb");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// SQL query to fetch cart details and calculate total
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

$net_total = 0;
$total_discount = 0;

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

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PayPal Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            padding: 30px;
        }
        h1 {
            color: #2e7d32;
            text-align: center;
            border-bottom: 2px solid #fbc02d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .paypal-logo {
            text-align: center;
            margin: 20px 0;
        }
        .paypal-logo img {
            height: 50px;
        }
        .total-display {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 25px;
            border-left: 4px solid #fbc02d;
        }
        .total-display strong {
            color: #2e7d32;
            font-size: 18px;
        }
        .paypal-btn {
            background-color: #0070ba;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background-color 0.3s;
        }
        .paypal-btn:hover {
            background-color: #005ea6;
        }
        .paypal-btn img {
            height: 20px;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
        }
        .alternative {
            text-align: center;
            margin: 20px 0;
            color: #666;
            position: relative;
        }
        .alternative:before, 
        .alternative:after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #ddd;
            margin: auto 10px;
        }
        .alternative span {
            padding: 0 10px;
        }
        .email-input {
            margin-bottom: 20px;
        }
        .email-input label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2e7d32;
        }
        .email-input input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>PayPal Payment</h1>
        
        <div class="total-display">
            <p>Net Total: <strong>Rs. <?php echo number_format($net_total, 2); ?></strong></p>
        </div>

        <form action="process_paypal_payment.php" method="post">
            <input type="hidden" name="payment_method" value="paypal">
            <input type="hidden" name="amount" value="<?php echo $net_total; ?>">
            
            <div class="paypal-logo">
                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal Logo">
            </div>
            
            <div class="email-input">
                <label for="paypal_email">PayPal Email</label>
                <input type="email" id="paypal_email" name="paypal_email" placeholder="your@email.com" required>
            </div>
            
            <button type="submit" class="paypal-btn">
                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal">
                Pay Now
            </button>
            
            <div class="alternative">
                <span>OR</span>
            </div>
            
            <a href="#" class="paypal-btn" style="background-color: #ffc439; color: #111;">
                <img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" alt="PayPal">
                Pay with PayPal Credit
            </a>
        </form>
        
        <a href="checkout-details.php" class="back-btn">← Back to checkout</a>
    </div>
</body>
</html>
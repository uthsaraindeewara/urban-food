<?php
session_start();
include "connection.php";

if (!isset($_POST['orderID'])) {
    header("Location: checkout-details.php");
    exit();
}

$orderID = (int) $_POST['orderID'];

// Get total amount based on orderID
$totalAmount = 0;
try {
    $stmt = oci_parse($conn, "BEGIN GetOrderDetails(:orderID, :result); END;");
    $resultCursor = oci_new_cursor($conn);
    oci_bind_by_name($stmt, ":orderID", $orderID);
    oci_bind_by_name($stmt, ":result", $resultCursor, -1, OCI_B_CURSOR);

    oci_execute($stmt);
    oci_execute($resultCursor);

    while ($row = oci_fetch_assoc($resultCursor)) {
        $totalAmount += $row['TOTAL']; // Assuming TOTAL = price * quantity
    }

    $_SESSION['total_amount'] = $totalAmount;
    $_SESSION['orderID'] = $orderID;

} catch (Exception $e) {
    $_SESSION['error'] = "Failed to retrieve order details.";
    header("Location: checkout-details.php?orderID=$orderID");
    exit();
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Credit Card Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-container {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #fbc02d;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2e7d32;
        }
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .card-icons {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
        }
        .card-icons img {
            height: 30px;
        }
        .row {
            display: flex;
            gap: 20px;
        }
        .row .form-group {
            flex: 1;
        }
        .submit-btn {
            background-color: #fbc02d;
            color: #2e7d32;
            padding: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #f9a825;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            width: 100%;
        }
        .total-display {
            background-color: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 18px;
            border-left: 4px solid #fbc02d;
        }
        .total-display strong {
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>Credit Card Payment</h1>

        <div class="total-display">
        <p>Net Total: <strong>Rs. <?= number_format($_SESSION['total_amount'], 2) ?></strong></p>

        </div>

        <form id="paymentForm">
            <input type="hidden" name="payment_method" value="credit_card">
            <input type="hidden" name="amount" value="<?= $totalAmount ?>">


            <div class="card-icons">
                <img src="img/visa.png" alt="Visa">
                <img src="img/mastercard.png" alt="Mastercard">
                <img src="img/amex.png" alt="American Express">
            </div>

            <div class="form-group">
                <label for="card_number">Card Number</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
            </div>

            <div class="form-group">
                <label for="card_name">Name on Card</label>
                <input type="text" id="card_name" name="card_name" placeholder="John Doe" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label for="expiry_date">Expiry Date</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="number" id="cvv" name="cvv" placeholder="123" required>
                </div>
            </div>

            <button type="submit" class="submit-btn">Pay Now</button>
        </form>

        <a href="checkout-details.html" class="back-btn">← Back to checkout</a>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function (e) {
            e.preventDefault();
        });
    </script>
</body>
</html>
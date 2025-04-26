<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Successful</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .confirmation-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            color: #4CAF50;
            font-size: 60px;
            margin-bottom: 20px;
        }
        h1 {
            color: #2e7d32;
            margin-bottom: 15px;
        }
        .order-details {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #fbc02d;
            text-align: left;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            color: #555;
            font-weight: bold;
        }
        .detail-value {
            color: #2e7d32;
            font-weight: bold;
        }
        .total-amount {
            font-size: 20px;
            margin-top: 15px;
        }
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            flex: 1;
            text-align: center;
        }
        .btn-primary {
            background-color: #fbc02d;
            color: #2e7d32;
            border: 2px solid #fbc02d;
        }
        .btn-primary:hover {
            background-color: #f9a825;
            border-color: #f9a825;
        }
        .btn-secondary {
            background-color: white;
            color: #2e7d32;
            border: 2px solid #2e7d32;
        }
        .btn-secondary:hover {
            background-color: #e8f5e9;
        }
        .confirmation-text {
            color: #555;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .thank-you {
            font-size: 24px;
            color: #2e7d32;
            margin-bottom: 10px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Payment Successful!</h1>
        <div class="thank-you">Thank You For Your Purchase</div>
        
        <p class="confirmation-text">
            Your payment has been processed successfully. We've sent a confirmation email 
            with your order details. Your items will be shipped within 2-3 business days.
        </p>
        
        <div class="order-details">
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">#<?php echo rand(100000, 999999); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value"><?php echo date('F j, Y'); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">Credit Card</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Shipping Address:</span>
                <span class="detail-value">123 Main St, City, Country</span>
            </div>
            <div class="total-amount">
                <span class="detail-label">Total Paid:</span>
                <span class="detail-value">Rs. <?php echo isset($net_total) ? number_format($net_total, 2) : '0.00'; ?></span>
            </div>
        </div>
        
        <div class="btn-container">
            <a href="order_details.php" class="btn btn-secondary">
                <i class="fas fa-file-alt"></i> View Order
            </a>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Continue Shopping
            </a>
        </div>
    </div>
</body>
</html>
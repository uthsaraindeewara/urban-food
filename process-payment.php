<?php
session_start();

// MySQLi connection
$mysqli = new mysqli("localhost:3307", "root", "", "storedb");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_POST['payBtn'])) {
    $payment_method = $_POST['payment_method'];

    // Get the customer ID from the session
    $cusID = $_SESSION['user']['cusID'];

    if (isset($_POST['address']) && !empty($_POST['address'])) {
        $address = $_POST['address'];
        
        // Insert or update the customer's address in the database
        $sql = "UPDATE customer SET address = ? WHERE cusID = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $address, $cusID);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['contactNo']) && !empty($_POST['contactNo'])) {
        $contactNo = $_POST['contactNo'];
        
        // Insert or update the customer's address in the database
        $sql = "UPDATE customer SET contactNo = ? WHERE cusID = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $contactNo, $cusID);
        $stmt->execute();
        $stmt->close();
    }

    // Get cart items for the customer again (to calculate totals)
    $sql = "SELECT product_cart.productID, product_cart.quantity, product_cart.size, product.price, IFNULL(discount.amount, 0) AS discount
            FROM cart
            INNER JOIN product_cart ON cart.cartID = product_cart.cartID
            INNER JOIN product ON product_cart.productID = product.productID
            LEFT JOIN discount ON product.productID = discount.productID
            WHERE cart.cusID = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $cusID);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_discount = 0;
    $net_total = 0;
    $cart_items = [];

    // Calculate total and prepare cart data
    while ($row = $result->fetch_assoc()) {
        $price_after_discount = $row['price'] - $row['discount'];
        $total_for_item = $price_after_discount * $row['quantity'];

        $total_discount += $row['discount'] * $row['quantity'];
        $net_total += $total_for_item;

        // Store each item data for later insertion into order_item table
        $cart_items[] = [
            'product_id' => $row['productID'],
            'size' => $row['size'],
            'price' => $price_after_discount,
            'quantity' => $row['quantity']
        ];
    }

    // Insert into order_table
    $stmt = $mysqli->prepare("INSERT INTO order_tbl (date, status, amount, discount, cusID) VALUES (NOW(), ?, ?, ?, ?)");
    $stmt->bind_param("sdii", $status, $net_total, $total_discount, $cusID);
    $status = 'Placed';
    $stmt->execute();
    $orderID = $mysqli->insert_id; // Get the inserted order ID

    // Insert each cart item into the order_item table
    foreach ($cart_items as $item) {
        $stmt = $mysqli->prepare("INSERT INTO order_item (product_id, orderID, size, price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdi", $item['product_id'], $orderID, $item['size'], $item['price'], $item['quantity']);
        $stmt->execute();

        // Update product_quantity by deducting the quantity
        $update_stmt = $mysqli->prepare("UPDATE product_quantity SET quantity = quantity - ? WHERE product_id = ? AND size = ?");
        $update_stmt->bind_param("iis", $item['quantity'], $item['product_id'], $item['size']);
        $update_stmt->execute();
    }

    // Clear the customer's cart (optional)
    $stmt = $mysqli->prepare("DELETE FROM product_cart WHERE cartID IN (SELECT cartID FROM cart WHERE cusID = ?)");
    $stmt->bind_param("i", $cusID);
    $stmt->execute();

    // Redirect to success page
    header("Location: sucsess.html");
}
?>
<?php
session_start();

include "connection.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['user']['userID'];

if (isset($_POST['placeOrder'])) {
    if (isset($_POST['selectedProducts']) && is_array($_POST['selectedProducts'])) {
        $selectedProducts = $_POST['selectedProducts'];
        $quantities = $_POST['quantity'];

        $orderID = 0;
        $createOrderStmt = oci_parse($conn, "BEGIN CreateOrder(:customerID, :orderID); END;");
        oci_bind_by_name($createOrderStmt, ":customerID", $customerID);
        oci_bind_by_name($createOrderStmt, ":orderID", $orderID, -1, SQLT_INT);
        oci_execute($createOrderStmt);

        if ($orderID > 0) {
            foreach ($selectedProducts as $productID) {
                if (isset($quantities[$productID])) {
                    $qty = (int) $quantities[$productID];

                    if ($qty > 0) {
                        // Fetch the product price
                        $priceQuery = oci_parse($conn, "SELECT price FROM product WHERE product_id = :productID");
                        oci_bind_by_name($priceQuery, ":productID", $productID);
                        oci_execute($priceQuery);
                        $priceRow = oci_fetch_assoc($priceQuery);

                        if ($priceRow && isset($priceRow['PRICE'])) {
                            $price = (float) $priceRow['PRICE'];

                            // Insert into order_item with price
                            $addItemStmt = oci_parse($conn, "BEGIN AddOrderItem(:orderID, :productID, :quantity, :price); END;");
                            oci_bind_by_name($addItemStmt, ":orderID", $orderID);
                            oci_bind_by_name($addItemStmt, ":productID", $productID);
                            oci_bind_by_name($addItemStmt, ":quantity", $qty);
                            oci_bind_by_name($addItemStmt, ":price", $price);

                            if (!oci_execute($addItemStmt)) {
                                $e = oci_error($addItemStmt);
                                echo "Error adding item: " . htmlentities($e['message']);
                            }
                        } else {
                            echo "Price not found for product ID: " . htmlspecialchars($productID);
                        }
                    }
                }
            }

            header("Location: checkout-details.php?orderID=" . urlencode($orderID));
            exit();
        } else {
            echo "<script>alert('Failed to create order. Please try again.'); window.location.href='cart.php';</script>";
        }
    } else {
        echo "<script>alert('Please select at least one item to place an order.'); window.location.href='cart.php';</script>";
    }
}

//  Remove an item
if (isset($_POST['remove'])) {
    $productID = $_POST['remove'];
    $stmt = oci_parse($conn, "BEGIN RemoveCartItem(:customerID, :productID); END;");
    oci_bind_by_name($stmt, ":customerID", $customerID);
    oci_bind_by_name($stmt, ":productID", $productID);
    oci_execute($stmt);
}

// update quantity
if (isset($_POST['updateQty'])) {
    foreach ($_POST['quantity'] as $productID => $qty) {
        $stmt = oci_parse($conn, "BEGIN UpdateCartQuantity(:customerID, :productID, :quantity); END;");
        oci_bind_by_name($stmt, ":customerID", $customerID);
        oci_bind_by_name($stmt, ":productID", $productID);
        oci_bind_by_name($stmt, ":quantity", $qty);
        oci_execute($stmt);
    }
}


// get cart items 
$cartStmt = oci_parse($conn, "BEGIN GetCartItems(:customerID, :result); END;");
$resultCursor = oci_new_cursor($conn);
oci_bind_by_name($cartStmt, ":customerID", $customerID);
oci_bind_by_name($cartStmt, ":result", $resultCursor, -1, OCI_B_CURSOR);
oci_execute($cartStmt);
oci_execute($resultCursor);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="cart.css">
    <script>
        function calculateTotal() {
            let total = 0;
            const rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                const checkbox = row.querySelector("input[type='checkbox']");
                const qtyInput = row.querySelector("input[name^='quantity']");
                const unitPrice = parseFloat(row.getAttribute('data-price'));

                if (checkbox.checked) {
                    let itemTotal = parseInt(qtyInput.value) * unitPrice;
                    total += itemTotal;
                    row.querySelector(".item-total").innerText = "Rs. " + itemTotal.toFixed(2);
                }
            });

            document.getElementById("totalAmount").innerText = "Rs. " + total.toFixed(2);
        }
    </script>
</head>
<body>
    <div class="cart-container">
        <h1>Shopping Cart</h1>
        <form method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = oci_fetch_assoc($resultCursor)): ?>
                        <tr data-price="<?= htmlspecialchars($row['PRICE']) ?>">
                            <td>
                                <input type="checkbox" name="selectedProducts[]" value="<?= $row['PRODUCT_ID'] ?>" onchange="calculateTotal();">
                            </td>
                            <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
                            <td>Rs. <?= number_format($row['PRICE'], 2) ?></td>
                            <td>
                                <input type="number" name="quantity[<?= $row['PRODUCT_ID'] ?>]" value="<?= $row['QUANTITY'] ?>" min="1" onchange="calculateTotal();">
                            </td>
                            <td class="item-total">Rs. <?= number_format($row['PRICE'] * $row['QUANTITY'], 2) ?></td>
                            <td>
                                <button type="submit" name="remove" value="<?= $row['PRODUCT_ID'] ?>" class="remove-button">Remove</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="summary">
                <p>Total Amount: <span id="totalAmount">Rs. 0.00</span></p>
                <button type="submit" name="updateQty" class="update-button">Update Quantities</button>
                <button type="submit" name="placeOrder" class="checkout-button">Place Order</button>
            </div>
        </form>
    </div>
</body>
</html>

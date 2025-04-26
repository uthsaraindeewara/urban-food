<?php
$conn = oci_connect('system', '123', 'localhost/XE');

// Hardcoded for testing
$customer_id = 3;

// Prepare the procedure call
$stid = oci_parse($conn, 'BEGIN get_delivery_by_customer_id(:p_customer_id, :p_delivery_id, :p_customer_id_out, :p_name, :p_address, :p_tp, :p_message); END;');

// Bind variables
oci_bind_by_name($stid, ':p_customer_id', $customer_id);
oci_bind_by_name($stid, ':p_delivery_id', $delivery_id, 10);
oci_bind_by_name($stid, ':p_customer_id_out', $customer_id_out, 10);
oci_bind_by_name($stid, ':p_name', $name, 100);
oci_bind_by_name($stid, ':p_address', $address, 100);
oci_bind_by_name($stid, ':p_tp', $tp, 20);
oci_bind_by_name($stid, ':p_message', $message, 100);

// Execute
oci_execute($stid);

$e = oci_error($stid);
if ($e) {
    echo "Oracle error: " . $e['message'];
}
echo $message; // To see the status of the operation


// Close connection
oci_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delivery Details</title>
</head>
<body>

<h2>Delivery Details (Customer ID: 1)</h2>

<b>Message:</b> <?php echo $message; ?><br><br>

<form>
    <label>Delivery ID:</label><br>
    <input type="text" name="delivery_id" value="<?php echo $delivery_id; ?>" readonly><br><br>

    <label>Customer ID:</label><br>
    <input type="text" name="customer_id" value="<?php echo $customer_id_out; ?>" readonly><br><br>

    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $name; ?>"><br><br>

    <label>Address:</label><br>
    <input type="text" name="address" value="<?php echo $address; ?>"><br><br>

    <label>Phone:</label><br>
    <input type="text" name="tp" value="<?php echo $tp; ?>"><br><br>
</form>

</body>
</html>

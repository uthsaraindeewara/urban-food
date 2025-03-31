<?php
// Check if the search term is sent via POST
if (isset($_POST['search'])) {
    $search_term = $_POST['search'];

    // MySQLi connection
    $mysqli = new mysqli("localhost:3307", "root", "", "storedb");

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Search for products that match the search term
    $sql = "SELECT productID, productName FROM product WHERE productName LIKE ?";
    $stmt = $mysqli->prepare($sql);
    $search_term_like = '%' . $search_term . '%';  // Use wildcards for partial matching
    $stmt->bind_param("s", $search_term_like);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display search results
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['productName'] . "</li>";
        }
    } else {
        echo "<li>No products found</li>";
    }

    $stmt->close();
    $mysqli->close();
}
?>
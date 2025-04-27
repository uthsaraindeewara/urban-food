<?php
session_start();
include 'connection.php';

// Check if user is logged in as seller
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['user']['userType'] === "seller") {
    $seller_id = $_SESSION['user']['userID'];
} else {
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        // Add new product
        $product_id = 0;
        $name = $_POST['product_name'];
        $description = $_POST['product_description'];
        $quantity = (int)$_POST['product_quantity'];
        $category = $_POST['product_category'];
        $price = (float)$_POST['product_price'];
        
        $sql = "BEGIN SYSTEM.add_product(:seller_id, :name, :description, :quantity, :price, :category, :product_id); END;";
        $stmt = oci_parse($conn, $sql);
        
        oci_bind_by_name($stmt, ":seller_id", $seller_id);
        oci_bind_by_name($stmt, ":name", $name);
        oci_bind_by_name($stmt, ":description", $description);
        oci_bind_by_name($stmt, ":quantity", $quantity);
        oci_bind_by_name($stmt, ":price", $price);
        oci_bind_by_name($stmt, ":category", $category);
        oci_bind_by_name($stmt, ":product_id", $product_id, 32);
        
        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            echo "Oracle Error: " . $e['message'];
            exit();
        }
        
        // Handle image upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "productImages/";
            $extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $target_file = $target_dir . "product-" . $product_id . "." . $extension;
            move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);

            $image_name = "product-" . $product_id . "." . $extension;
            $image_sql = "BEGIN SYSTEM.add_image(:product_id, :image_name); END;";
            $image_stmt = oci_parse($conn, $image_sql);

            oci_bind_by_name($image_stmt, ":product_id", $product_id);
            oci_bind_by_name($image_stmt, ":image_name", $image_name);
            
            if (!oci_execute($image_stmt)) {
                $e = oci_error($image_stmt);
                echo "Oracle Error: " . $e['message'];
                exit();
            }
        }
        
        header("Location: seller-control.php");
        exit();
    }
    elseif (isset($_POST['update_product'])) {
        // Update existing product
        $product_id = $_POST['product_id'];
        $name = $_POST['product_name'];
        $description = $_POST['product_description'];
        $quantity = (int)$_POST['product_quantity'];
        $category = $_POST['product_category'];
        $price = (float)$_POST['product_price'];
        
        $sql = "BEGIN SYSTEM.update_product(:product_id, :name, :description, :quantity, :price, :category); END;";
        $stmt = oci_parse($conn, $sql);
        
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_bind_by_name($stmt, ":name", $name);
        oci_bind_by_name($stmt, ":description", $description);
        oci_bind_by_name($stmt, ":quantity", $quantity);
        oci_bind_by_name($stmt, ":price", $price);
        oci_bind_by_name($stmt, ":category", $category);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            echo "Oracle Error: " . $e['message'];
            exit();
        }
        
        // Handle image update if new image was uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            // Delete existing images for the product
            $existingImages = glob("productImages/product-$product_id.*");
            foreach ($existingImages as $oldImage) {
                unlink($oldImage);
            }

            // Save the new image
            $extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $target_file = "productImages/product-$product_id." . $extension;
            move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);

            $image_name = "product-" . $product_id . "." . $extension;
            $image_sql = "BEGIN SYSTEM.update_image(:product_id, :image_name); END;";
            $image_stmt = oci_parse($conn, $image_sql);

            oci_bind_by_name($image_stmt, ":product_id", $product_id);
            oci_bind_by_name($image_stmt, ":image_name", $image_name);
            
            if (!oci_execute($image_stmt)) {
                $e = oci_error($image_stmt);
                echo "Oracle Error: " . $e['message'];
                exit();
            }
        }
        
        header("Location: seller-control.php");
        exit();
    }
    elseif (isset($_POST['delete_product'])) {
        // Delete product
        $product_id = $_POST['product_id'];
        
        $sql = "BEGIN SYSTEM.delete_product(:product_id); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":product_id", $product_id);
        oci_execute($stmt);
        
        // Delete product image if exists
        $image_path = "productImages/product-" . $product_id . ".*";
        array_map('unlink', glob($image_path));
        
        header("Location: seller-control.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Control Panel</title>
    <link rel="stylesheet" href="seller-control.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Seller Control Panel</h1>
            <div>
                <button id="addProductBtn" class="btn btn-primary">Add Product</button>
                <button id="sellerOrderBtn" class="btn btn-primary">Orders</button>
            </div>
        </div>

        <div class="products-grid" id="productsContainer">
            <?php
            $filter = "all";
            $sort = "none";
            $order = "asc";
            $search = "";

            // Fetch products for this seller
            $sql = "BEGIN SYSTEM.get_products_by_seller(:seller_id, :filter, :sort, :order, :search, :cursor); END;";
            $stmt = oci_parse($conn, $sql);
            
            oci_bind_by_name($stmt, ":seller_id", $seller_id);
            oci_bind_by_name($stmt, ":filter", $filter);
            oci_bind_by_name($stmt, ":sort", $sort);
            oci_bind_by_name($stmt, ":order", $order);
            oci_bind_by_name($stmt, ":search", $search);
            $cursor = oci_new_cursor($conn);
            oci_bind_by_name($stmt, ":cursor", $cursor, -1, OCI_B_CURSOR);
            
            oci_execute($stmt);
            oci_execute($cursor);
            
            while ($product = oci_fetch_assoc($cursor)) {
                $image_path = "productImages/product-" . $product['PRODUCT_ID'] . ".*";
                $images = glob($image_path);
                $image_src = !empty($images) ? $images[0] : "img/default-product.png";
                
                echo '
                <div class="product-card" data-product-id="' . $product['PRODUCT_ID'] . '">
                    <div class="product-view-mode">
                        <img src="' . $image_src . '?' . time() . '" alt="' . htmlspecialchars($product['PRODUCT_NAME']) . '" class="product-image">
                        <div class="product-info">
                            <div class="product-name">' . htmlspecialchars($product['PRODUCT_NAME']) . '</div>
                            <div class="product-description">' . htmlspecialchars($product['PRODUCT_DESCRIPTION'], 2) . '</div>
                            <div class="product-category">Category: ' . htmlspecialchars($product['CATEGORY']) . '</div>
                            <div class="product-price">Price: $' . number_format($product['PRICE'], 2) . '</div>
                            <div class="product-quantity">Quantity: ' . number_format($product['QUANTITY'], 2) . ' kg</div>
                            <div class="product-actions">
                                <button class="btn btn-edit edit-btn" data-product-id="' . $product['PRODUCT_ID'] . '">Edit</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="' . $product['PRODUCT_ID'] . '">
                                    <button type="submit" name="delete_product" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="product-edit-mode" style="display:none;">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="product_id" value="' . $product['PRODUCT_ID'] . '">
                            <img src="' . $image_src . '?' . time() . '" alt="' . htmlspecialchars($product['PRODUCT_NAME']) . '" class="product-image">
                            <div class="product-info">
                                <div class="form-group">
                                    <label for="product_name_' . $product['PRODUCT_ID'] . '">Product Name</label>
                                    <input type="text" id="product_name_' . $product['PRODUCT_ID'] . '" name="product_name" value="' . htmlspecialchars($product['PRODUCT_NAME']) . '" required>
                                </div>
                                <div class="form-group">
                                    <label for="product_description_' . $product['PRODUCT_ID'] . '">Description</label>
                                    <input type="text" id="product_description_' . $product['PRODUCT_ID'] . '" name="product_description" value="' . $product['PRODUCT_DESCRIPTION'] . '" required>
                                </div>
                                <div class="form-group">
                                    <label for="product_category_' . $product['PRODUCT_ID'] . '">Category</label>
                                    <select id="product_category_' . $product['PRODUCT_ID'] . '" name="product_category" required>
                                        <option value="Vegetables"' . ($product['CATEGORY'] == 'Vegetables' ? ' selected' : '') . '>Vegetables</option>
                                        <option value="Fruits"' . ($product['CATEGORY'] == 'Fruits' ? ' selected' : '') . '>Fruits</option>
                                        <option value="Greens"' . ($product['CATEGORY'] == 'Greens' ? ' selected' : '') . '>Greens</option>
                                        <option value="Grains"' . ($product['CATEGORY'] == 'Grains' ? ' selected' : '') . '>Grains</option>
                                        <option value="Dairy"' . ($product['CATEGORY'] == 'Dairy' ? ' selected' : '') . '>Dairy</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="product_price_' . $product['PRODUCT_ID'] . '">Price</label>
                                    <input type="number" step="1" id="product_price_' . $product['PRODUCT_ID'] . '" name="product_price" value="' . $product['PRICE'] . '" required>
                                </div>
                                <div class="form-group">
                                    <label for="product_quantty_' . $product['PRODUCT_ID'] . '">Quantity</label>
                                    <input type="number" step="0.5" id="product_quantity_' . $product['PRODUCT_ID'] . '" name="product_quantity" value="' . $product['QUANTITY'] . '" required>
                                </div>
                                <div class="form-group">
                                    <label for="product_image_' . $product['PRODUCT_ID'] . '">Update Image</label>
                                    <input type="file" id="product_image_' . $product['PRODUCT_ID'] . '" name="product_image" accept="image/*">
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn cancel-edit-btn">Cancel</button>
                                    <button type="submit" name="update_product" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>';
            }
            
            oci_free_statement($cursor);
            oci_free_statement($stmt);
            ?>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Add New Product</div>
                <span class="close-btn">&times;</span>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="product_description">Product Description</label>
                    <input type="text" id="product_description" name="product_description" required>
                </div>
                <div class="form-group">
                    <label for="product_quantity">Product Quantity</label>
                    <input type="number" id="product_quantity" name="product_quantity" required>
                </div>
                <div class="form-group">
                    <label for="product_category">Category</label>
                    <select id="product_category" name="product_category" required>
                        <option value="Vegetables">Vegetables</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Greens">Greens</option>
                        <option value="Grains">Grains</option>
                        <option value="Dairy">Dairy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_price">Price</label>
                    <input type="number" step="0.01" id="product_price" name="product_price" required>
                </div>
                <div class="form-group">
                    <label for="product_image">Product Image</label>
                    <input type="file" id="product_image" name="product_image" accept="image/*" required>
                </div>
                <div class="form-actions">
                    <button type="button" id="cancelAddBtn" class="btn">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        const modal = document.getElementById('addProductModal');
        const addBtn = document.getElementById('addProductBtn');
        const closeBtn = document.querySelector('.close-btn');
        const cancelBtn = document.getElementById('cancelAddBtn');

        addBtn.onclick = function() {
            modal.style.display = 'flex';
        }

        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        cancelBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Edit functionality
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productCard = this.closest('.product-card');
                
                // Hide view mode, show edit mode
                productCard.querySelector('.product-view-mode').style.display = 'none';
                productCard.querySelector('.product-edit-mode').style.display = 'block';
                productCard.classList.add('edit-mode');
            });
        });

        document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                
                // Hide edit mode, show view mode
                productCard.querySelector('.product-edit-mode').style.display = 'none';
                productCard.querySelector('.product-view-mode').style.display = 'block';
                productCard.classList.remove('edit-mode');
            });
        });

        document.getElementById('sellerOrderBtn').addEventListener('click', function() {
            window.location.href = 'seller_orders.php';
        });
    </script>
</body>
</html>
<?php
oci_close($conn);
?>
// Sample product data
const products = [
    { id: 1, name: "Givo Juno Ruched Dress", brand: "Priya's Brand", price: 1300, category: "women", img: "C:\\Users\\dumin\\OneDrive\\Pictures\\ClothingStorePics\\blugown.png"},
    { id: 2, name: "Jobbs Waffle T-Shirt", brand: "Roboto's Brand", price: 1270, category: "men", img: "C:\\Users\\dumin\\OneDrive\\Pictures\\ClothingStorePics\\men-purple-tshirt.png" },
    { id: 3, name: "Ladies Short Sleeve T-Shirt", brand: "Roboto's Brand", price: 1270, category: "women", img: "C:\\Users\\dumin\\OneDrive\\Pictures\\ClothingStorePics\\Ladies-shortSleeve-tshirt.png" },
    { id: 4, name: "Men Plain Yellow T-Shirt", brand: "Roboto's Brand", price: 1270, category: "men", img: "C:\\Users\\dumin\\OneDrive\\Pictures\\ClothingStorePics\\Men-plain-yellow-tshirt.png" },
    { id: 5, name: "Ladies White T-Shirt", brand: "Roboto's Brand", price: 1270, category: "women", img: "C:\\Users\\dumin\\OneDrive\\Pictures\\ClothingStorePics\\Ladies-white-tshirt.png" }
];

// Display product cards
function displayProducts(products) {
    const productList = document.getElementById('productList');
    productList.innerHTML = ''; // Clear the existing products

    products.forEach(product => {
        const productCard = `
            <div class="product-card">
                <img src="${product.img}" alt="${product.name}">
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <p>${product.brand}</p>
                    <p>Rs. ${product.price}</p>
                </div>
                <div class="wishlist-icon">
                    <i class="far fa-heart" onclick="toggleFavorite(this)"></i>
                </div>
            </div>
        `;
        productList.innerHTML += productCard;
    });
}

// Toggle favorite icon
function toggleFavorite(icon) {
    icon.classList.toggle('liked');
}

// Filter products by category
document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value;
    const filteredProducts = selectedCategory === 'all'
        ? products
        : products.filter(product => product.category === selectedCategory);
    displayProducts(filteredProducts);
});

// Filter products by price range
document.getElementById('priceRange').addEventListener('input', function() {
    const maxPrice = this.value;
    document.getElementById('priceMax').textContent = `Rs. ${maxPrice}`;

    const filteredProducts = products.filter(product => product.price <= maxPrice);
    displayProducts(filteredProducts);
});

// Sort products
document.getElementById('sortOptions').addEventListener('change', function() {
    const selectedSort = this.value;
    let sortedProducts = [];

    if (selectedSort === 'priceLowHigh') {
        sortedProducts = [...products].sort((a, b) => a.price - b.price);
    } else if (selectedSort === 'priceHighLow') {
        sortedProducts = [...products].sort((a, b) => b.price - a.price);
    } else {
        sortedProducts = products; // Default sorting (newest)
    }

    displayProducts(sortedProducts);
});

// Search products by name
document.getElementById('searchBar').addEventListener('input', function() {
    const searchText = this.value.toLowerCase();
    const filteredProducts = products.filter(product => product.name.toLowerCase().includes(searchText));
    displayProducts(filteredProducts);
});

// Initial product display
window.onload = () => {
    displayProducts(products);
};

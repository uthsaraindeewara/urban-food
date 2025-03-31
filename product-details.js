function toggleRating() {
    const ratingDropdown = document.getElementById('ratingDropdown');
    ratingDropdown.style.display = ratingDropdown.style.display === 'none' || ratingDropdown.style.display === '' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.select-star');
    const selectedRating = document.getElementById('selected-rating');
    const ratingValueInput = document.getElementById('ratingValue');
    
    stars.forEach((star, index) => {
      star.addEventListener('click', () => {
        // Remove 'active' class from all stars
        stars.forEach(s => s.classList.remove('active'));
        
        // Add 'active' class to the clicked star and all previous stars
        for (let i = 0; i <= index; i++) {
          stars[i].classList.add('active');
        }
        
        // Update the selected rating and hidden input value
        const rating = index + 1;
        selectedRating.textContent = rating;
        ratingValueInput.value = rating;
      });
    });
});
  
  // Function to submit the rating via AJAX
function submitRating() {
    const formData = new FormData(document.getElementById('ratingForm'));
    
    fetch('submit-rating.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      location.reload(true);
    })
    .catch(error => {
      console.error('Error submitting rating:', error);
    });
}

const sizeOptions = document.querySelectorAll('.size-option');
const selectedSizeInput = document.getElementById('selected-size');
const quantityInput = document.getElementById('quantity');
const addToCartButton = document.querySelector('.add-to-cart');
const selectedSizeWishlistInput = document.getElementById('selected-size-wishlist');

// Add click event listener to each size option
sizeOptions.forEach(option => {
    option.addEventListener('click', () => {
        // Remove 'selected' class from all options
        sizeOptions.forEach(opt => opt.classList.remove('selected'));
        
        // Add 'selected' class to the clicked option
        option.classList.add('selected');
        
        // Store selected size value
        const selectedSize = option.getAttribute('data-size');
        const quantity = parseInt(option.getAttribute('data-quantity'), 10); // Get quantity of selected size
        selectedSizeInput.value = selectedSize;
        selectedSizeWishlistInput.value = selectedSize;

        // Disable 'Add to Cart' button if quantity is 0
        if (quantity == 0) {
          addToCartButton.disabled = true;
          addToCartButton.classList.add('disabled');
        } else {
          addToCartButton.disabled = false;
          addToCartButton.classList.remove('disabled');
        }

        // Listen for changes in the quantity input field
        quantityInput.addEventListener('input', () => {
          const selectedQuantity = parseInt(quantityInput.value, 10);

          // Disable 'Add to Cart' button if selected quantity exceeds available stock
          if (selectedQuantity > quantity || selectedQuantity <= 0) {
              addToCartButton.disabled = true;
              addToCartButton.classList.add('disabled');
          } else {
              addToCartButton.disabled = false;
              addToCartButton.classList.remove('disabled');
          }
        });
    });
});

// Function to toggle the visibility of the reviews section
function toggleReviews() {
    const reviews = document.getElementById('reviews');
    reviews.style.display = reviews.style.display === 'none' ? 'block' : 'none';
}

// Function to submit a review
function submitReview() {
    const reviewText = document.getElementById('reviewText').value;
    const productId = document.querySelector('input[name="productId"]').value; // Correct way to get the hidden input value
    const cusID = document.querySelector('input[name="cusId"]').value;
    const date = new Date().toISOString().split('T')[0]; // Get current date
    const time = new Date().toLocaleTimeString(); // Get current time

    if (reviewText) {
        const reviewData = {
            date: date,
            time: time,
            description: reviewText,
            productID: productId,
            cusID: cusID
        };

        saveReviewToDatabase(reviewData);
        document.getElementById('reviewText').value = '';
    }
}

// Function to save the review to the database using fetch
function saveReviewToDatabase(reviewData) {
    fetch('submit-review.php', { // PHP script to handle submission
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(reviewData)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Review saved:', data);
        const reviewList = document.getElementById('reviewList');
        const reviewContainer = document.createElement('div');
        reviewContainer.classList.add('review-container');
        
        const reviewHeader = document.createElement('div');
        reviewHeader.classList.add('review-header');
        const userName = document.createElement('strong');
        userName.textContent = 'You'
        reviewHeader.appendChild(userName);
        
        const reviewBody = document.createElement('div');
        reviewBody.classList.add('review-body');
        const reviewText = document.createElement('p');
        reviewText.textContent = reviewData.description;
        reviewBody.appendChild(reviewText);
        
        const reviewFooter = document.createElement('div');
        reviewFooter.classList.add('review-footer');
        reviewFooter.textContent = `Posted on ${reviewData.date} at ${reviewData.time}`;
        
        reviewContainer.appendChild(reviewHeader);
        reviewContainer.appendChild(reviewBody);
        reviewContainer.appendChild(reviewFooter);

        reviewList.appendChild(reviewContainer);

        reviewList.scrollTop = reviewList.scrollHeight;
    })
    .catch((error) => {
        console.error('Error saving review:', error);
    });
}

function changeImage(src) {
  const mainImage = document.querySelector('.image');
  mainImage.src = src; // Update the main image source to the clicked thumbnail's source
}
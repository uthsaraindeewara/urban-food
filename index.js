function toggleDropdown() {
    const dropdownContent = document.getElementById('dropdown-content');
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
}

// Close dropdown if clicked outside of it
window.onclick = function(event) {
    // Close the dropdown if the click is outside
    if (!event.target.matches('.img-2')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            const openDropdown = dropdowns[i];
            if (openDropdown.style.display === 'block' || openDropdown.classList.contains("show")) {
                openDropdown.style.display = 'none';
                openDropdown.classList.remove("show");
            }
        }
    }
}

function toggleDropdownnotification() {
    const dropdownContent = document.getElementById('notification-dropdown-content');
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
}

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');
    const suggestionsList = document.getElementById('suggestions');

    // Add event listener for keyup event on the search input
    searchInput.addEventListener('keyup', function () {
        const searchTerm = searchInput.value.trim();

        if (searchTerm !== '') {
            // Make a fetch request to PHP file
            fetch('search-suggestions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'search=' + encodeURIComponent(searchTerm)
            })
            .then(response => response.text())
            .then(data => {
                // Populate the suggestions list with the response data
                suggestionsList.innerHTML = data;

                // Add event listeners to each suggestion item
                const items = suggestionsList.querySelectorAll('li');
                items.forEach(item => {
                    item.addEventListener('click', function() {
                        // Set the search input value to the clicked suggestion
                        searchInput.value = item.textContent;

                        // Clear the suggestions list
                        suggestionsList.innerHTML = '';
                    });
                });
            })
            .catch(error => console.error('Error:', error));
        } else {
            // Clear the suggestions if the search input is empty
            suggestionsList.innerHTML = '';
        }
    });
});
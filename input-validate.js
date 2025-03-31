function validateForm() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const terms = document.getElementById("terms").checked;

    // Check if all fields are filled
    if (!username || !password || !confirmPassword) {
        alert("Please fill out all fields.");
        return false; // Prevent form submission
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false; // Prevent form submission
    }

    // Check if terms are accepted
    if (!terms) {
        alert("You must agree to the Terms of Use and Privacy Policy.");
        return false; // Prevent form submission
    }

    // Optionally, you can add more checks (e.g., email format)
    
    return true; // Allow form submission
}

function togglePasswordVisibility() {
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirm-password");
    const type = passwordField.type === "password" ? "text" : "password";
    passwordField.type = type;
    confirmPasswordField.type = type;
}
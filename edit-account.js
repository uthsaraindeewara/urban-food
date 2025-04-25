function togglePasswordVisibility() {
    var passwordField = document.getElementById("password");
    var toggleText = document.querySelector(".password-toggle");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleText.innerText = "Hide";
    } else {
        passwordField.type = "password";
        toggleText.innerText = "Show";
    }
}
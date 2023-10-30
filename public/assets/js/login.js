function validateForm() {
    var password = document.getElementById("password").value;
    if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
    }
    var email = document.getElementById("email").value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Invalid email address");
        return false;
    }
    return true;

}

document.getElementById("loginForm").onsubmit = function(event) {
    if (!validateForm()) {
        event.preventDefault();
    }
};

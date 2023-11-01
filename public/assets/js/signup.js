function load(id){
    document.getElementById(id).style.display = "flex";
    document.getElementById('select').style.display = "none";
   
}

// Get Url
var urlFragment = window.location.hash;

if (urlFragment.startsWith('#')) {
urlFragment = urlFragment.slice(1);
load(urlFragment)
}

console.log(urlFragment);




// File input 
var fileInput = document.getElementById("verification_document");

var fileLabel = document.querySelector(".file-label");

fileInput.addEventListener("change", function () {
    if (fileInput.files.length > 0) {
        fileLabel.textContent = "File Selected: " + fileInput.files[0].name;
    } else {
        fileLabel.textContent = "Choose Verification Document";
    }
});


// JavaScript code for form validation
function validateForm(formId) {
    var form = document.getElementById(formId);
    // var errorDiv = document.createElement('div');
    // errorDiv.className = 'error-message';
    // form.appendChild(errorDiv);
    var errorDiv = document.getElementById('error-message');
    
    var name = form.querySelector('input[name="name"]');
    var address = form.querySelector('input[name="address"]');
    var email = form.querySelector('input[name="email"]');
    var number = form.querySelector('input[id="number"]');
    var nic = form.querySelector('input[name="nic"]');
    var password = form.querySelector('input[name="password"]');
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    var phoneNumberRegex = /^(\+\d{1,3})?\d{10,14}$/; // 10-14 digits with or without '+'
    var nicRegex = /^[0-9]{9}[vVxX]$/; // 10 digits ending with 'v' or 'x'


    // Clear previous error messages
    errorDiv.innerHTML = '';

    // Basic validation for non-empty fields
    if (name.value === '' || address.value === '' || number.value === '' || nic.value === '' || password.value === '') {
        errorDiv.innerHTML = 'All fields are required.';
        console.log('All fields are required.');
        return false;
    }

    // Email format validation
    if (!emailRegex.test(email.value)) {
        errorDiv.innerHTML = 'Invalid email address.';
        console.log('Invalid email address.');
        return false;
    }

    if (!phoneNumberRegex.test(number.value)) {
        errorDiv.innerHTML = 'Invalid phone number. Please enter a valid phone number';
        console.log('Invalid phone number. Please enter a valid phone number with or without "+".');
        return false;
    }

    // NIC number validation
    // if (!nicRegex.test(nic.value)) {
    //     errorDiv.innerHTML = 'Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".';
    //     console.log('Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".');
    //     // return false;
    // }


    // Password strength check
    if (!passwordRegex.test(password.value)) {
        errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
        console.log('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.');
        return false;
    }

    return true;
}

// Event listener for form submission
document.getElementById('customer').onsubmit = function(event) {
    if (!validateForm('customer')) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
};

document.getElementById('rental-service').onsubmit = function(event) {
    if (!validateForm('rental-service')) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
};

document.getElementById('guide').onsubmit = function(event) {
    if (!validateForm('guide')) {
        event.preventDefault(); // Prevent form submission if validation fails
    }
};

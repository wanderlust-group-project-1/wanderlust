// function load(id){
//     document.getElementById(id).style.display = "flex";
//     document.getElementById('select').style.display = "none";
   
// }

// function load(formId) {
//     var form = document.getElementById(formId + '-form');
//     // Rest of the function logic...
// }


//showpassowrd
// let eyeicon = document.getElementById("eyeicon");
// let password = document.getElementById("password");

// eyeicon.onclick = function() {
//     if(password.type == "password"){
//         password.type = "text";
//     }else{
//         password.type = "password";
//     }
// }
function signupToggle(){
    console.log("gamindu");
    var element1;
    element1 = document.querySelector('.popupForm');
    element1.classList.toggle("popupForm-active");
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


// Location modal

var modal = document.getElementById("location-modal");

var span = document.getElementsByClassName("close")[0];

// Get all view buttons
var viewButton = document.getElementById('select-location');

var confirmButton = document.getElementById('confirm-location');
// Function to handle modal display
function openModal() {
    modal.style.display = "block";
}

// // Add click event listener to view buttons
viewButton.addEventListener('click', function() {
//    alertmsg('View button clicked');

    openModal();
    initialize();
});




// Close the modal when the close button is clicked
span.onclick = function() {
    modal.style.display = "none";
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

confirmButton.onclick = function(event) {
    event.preventDefault();
    modal.style.display = "none";
    var lat = document.getElementById('latitude').value;
    var lng = document.getElementById('longitude').value;
    console.log(lat + " " + lng);
}






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
       alertmsg('All fields are required.');
        return false;
    }

    // Email format validation
    if (!emailRegex.test(email.value)) {
        errorDiv.innerHTML = 'Invalid email address.';
       alertmsg('Invalid email address.');
        return false;
    }

    if (!phoneNumberRegex.test(number.value)) {
        errorDiv.innerHTML = 'Invalid phone number. Please enter a valid phone number';
       alertmsg('Invalid phone number. Please enter a valid phone number with or without "+".');
        return false;
    }

    // NIC number validation
    // if (!nicRegex.test(nic.value)) {
    //     errorDiv.innerHTML = 'Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".';
    //    alertmsg('Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".');
    //     // return false;
    // }


    // Password strength check
    if (!passwordRegex.test(password.value)) {
        errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
       alertmsg('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.');
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


var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
var phoneNumberRegex = /^(\+\d{1,3})?\d{10,14}$/; // 10-14 digits with or without '+'
var nicRegex = /^[0-9]{9}[vVxX]$|^[0-9]{12}$/; // 10 digits ending with 'v' or 'x'





document.getElementById("customer-signup").onclick =  function(event){
    event.preventDefault();
    // get input fields values inside on "customer" id  form
    var form = document.getElementById("customer");

    var name = form.querySelector('input[name="name"]').value;
    var address = form.querySelector('textarea[name="address"]').value;
    var email = form.querySelector('input[name="email"]').value;
    var number = form.querySelector('input[id="number"]').value;
    var nic = form.querySelector('input[name="nic"]').value;
    var password = form.querySelector('input[name="password"]').value;



    // validate

    if (name === '' || address === '' || number === '' || nic === '' || password === '') {
        // errorDiv.innerHTML = 'All fields are required.';
       alertmsg('All fields are required.',"error");
        return false;
    }
    if (!emailRegex.test(email)) {
        // errorDiv.innerHTML = 'Invalid email address.';
       alertmsg('Invalid email address.',"error");
        return false;
    }
    if (!phoneNumberRegex.test(number)) {
        // errorDiv.innerHTML = 'Invalid phone number. Please enter a valid phone number';
       alertmsg('Invalid phone number. Please enter a valid phone number with or without "+".',"error");
        return false;
    }
    if (!nicRegex.test(nic)) {
        // errorDiv.innerHTML = 'Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".';
       alertmsg('Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".',"error");
        // return false;
    }
    if (!passwordRegex.test(password)) {
        // errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
       alertmsg('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',"error");
        return false;
    }



    // create a user object
    var user = {
        name: name,
        address: address,
        email: email,
        number: number,
        nic: nic,
        password: password
    }

    console.log(user);

    const api = new ApiClient('/api/signup/customer');
    api.sendJSONRequest('', 'POST', user)
        .then(response => {
            if (response.success) {
                alertmsg("success", "success");
                setTimeout(function() {
                    window.location.href = "/";
                }, 1000);
            } else {
                alertmsg(response.message,"error");
            }
        })
        .catch(error => {
            console.error(error);
        });

    














}

// document.getElementById("rental-service-signup").onclick =  function(event){
function rentalServiceSignup(event){
    event.preventDefault();
    // get input fields values inside on "customer" id  form
    var form = document.getElementById("rental-service");

    var businessname = form.querySelector('input[name="name"]').value;
    var address = form.querySelector('textarea[name="address"]').value;
    var regNo = form.querySelector('input[name="regNo"]').value;
    var mobile = form.querySelector('input[name="mobile"]').value;
    var email = form.querySelector('input[name="email"]').value;
    var password = form.querySelector('input[name="password"]').value;


    var latitude = document.getElementById('latitude').value;
    var longitude = document.getElementById('longitude').value;

    console.log(latitude + " " + longitude);


    var verification_document = form.querySelector('input[name="verification_document"]').files[0];


    // validate

    if (businessname === '' || address === '' || regNo === '' || mobile === '' || email === '' || password === '') {
        // errorDiv.innerHTML = 'All fields are required.';
       alertmsg('All fields are required.',"error");
        return false;
    }
    if (!emailRegex.test(email)) {
        // errorDiv.innerHTML = 'Invalid email address.';
       alertmsg('Invalid email address.',"error");
        return false;
    }
    if (!phoneNumberRegex.test(mobile)) {
        // errorDiv.innerHTML = 'Invalid phone number. Please enter a valid phone number';
       alertmsg('Invalid phone number. Please enter a valid phone number with or without "+".',"error");
        return false;
    }
    // if (!nicRegex.test(regNo)) {
    //     // errorDiv.innerHTML = 'Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".';
    //    alertmsg('Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".',"error");
    //     // return false;
    // }
    if (!passwordRegex.test(password)) {
        // errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
       alertmsg('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',"error");
        return false;
    }
    
    // if (verification_document === undefined) {
    //     // errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
    //    alertmsg('Verification document is required.',"error");
    //     return false;
    // }



    // create a user object

    var user = {
        name: businessname,
        address: address,
        regNo: regNo,
        mobile: mobile,
        email: email,
        password: password,
        latitude: latitude,
        longitude: longitude
        // verification_document: verification_document
    }

    const api = new ApiClient('/api/signup/rentalservice');

    // file data with key value pair
    var filesData = {
        verification_document: verification_document
    }

    api.uploadFilesWithJSON('', filesData, user)
        .then(response => {

            // console.log(response);
            if (response.success) {
                alertmsg("success", "success");
                setTimeout(function() {
                    window.location.href = "/";
                }, 1000);
            } else {
                alertmsg(response.message,"error");
            }
        })
        .catch(error => {
            console.error(error);
        });







    

    console.log(user);


    


    




}


function guideSignup(event){
    event.preventDefault();
    // get input fields values inside on "customer" id  form
    var form = document.getElementById("guide");

    var name = form.querySelector('input[name="name"]').value;
    var address = form.querySelector('textarea[name="address"]').value;
    var nic = form.querySelector('input[name="nic"]').value;
    var mobile = form.querySelector('input[name="mobile"]').value;
    var email = form.querySelector('input[name="email"]').value;
    var password = form.querySelector('input[name="password"]').value;
    // id = "gender"
    var gender = form.querySelector('select[name="gender"]').value;


    var verification_document = form.querySelector('input[name="verification_document"]').files[0];


    // validate

    if (name === '' || address === '' || nic === '' || mobile === '' || email === '' || password === '') {
        // errorDiv.innerHTML = 'All fields are required.';
       alertmsg('All fields are required.',"error");
        return false;
    }
    if (!emailRegex.test(email)) {
        // errorDiv.innerHTML = 'Invalid email address.';
       alertmsg('Invalid email address.',"error");
        return false;
    }
    if (!phoneNumberRegex.test(mobile)) {
        // errorDiv.innerHTML = 'Invalid phone number. Please enter a valid phone number';
       alertmsg('Invalid phone number. Please enter a valid phone number with or without "+".',"error");
        return false;
    }
    if (!nicRegex.test(nic)) {
        // errorDiv.innerHTML = 'Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".';
       alertmsg('Invalid NIC number. Please enter a valid NIC number ending with "v" or "x".',"error");
        // return false;
    }
    if (!passwordRegex.test(password)) {
        // errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
       alertmsg('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',"error");
        return false;
    }

    if (verification_document === undefined) {
        // errorDiv.innerHTML = 'Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.';
       alertmsg('Verification document is required.',"error");
        return false;
    }



    // create a user object

    var user = {
        name: name,
        address: address,
        nic: nic,
        mobile: mobile,
        email: email,
        gender: gender,
        password: password,

        // verification_document: verification_document
    }

    const api = new ApiClient('/api/signup/guide');

    // file data with key value pair
    var filesData = {
        verification_document: verification_document
    }

    api.uploadFilesWithJSON('', filesData, user)
        .then(response => {

            // console.log(response);
            if (response.success) {
                alertmsg("success", "success");
                setTimeout(function() {
                    window.location.href = "/";
                }, 1000);
            } else {
                alertmsg(response.message,"error");
            }
        })
        .catch(error => {
            console.error(error);
        });
        


}

    






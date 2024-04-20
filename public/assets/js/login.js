function validateForm() {
    var password = document.getElementById("password").value;
    if (password.length < 8) {
        alertmsg("Password must be at least 8 characters long","error");
        return false;
    }
    var email = document.getElementById("email").value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        // alert("Invalid email address");
        alertmsg("Invalid email address","error");
        return false;
    }
    return true;

}

document.getElementById("submit").onclick = function(event) {
    event.preventDefault();
    if (!validateForm()) {
        return false;
    }
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;

    var data = {
        email: email,
        password: password
    };
    console.log(data);
    showLoader();
    var xhr = new XMLHttpRequest();

    // xhr.open('POST', '/api/login', true);
    // xhr.setRequestHeader('Content-Type', 'application/json');
    // xhr.onload = function() {
    //     if (this.status == 200) {
    //         console.log(this.responseText);
    //         var response = JSON.parse(this.responseText);
    //         if (response.success) {
    //             alertmsg("success", "success");
            
    //             // window.location.href = "/";
    //             // time out
    //             setTimeout(function() {
    //                 window.location.href = "/";
    //             }, 1000);
    //         } else {
    //             alertmsg(response.message);
    //         }
    //     } else {
    //         alertmsg("Something went wrong","error");
    //     }
    // };
    // xhr.send(JSON.stringify(data));

    const api = new ApiClient('/api/login');
    api.sendJSONRequest('', 'POST', data)
        .then(response => {
            if (response.success) {
                alertmsg("Login Successful", "success");
                setTimeout(function() {
                    window.location.href = "/";
                }, 1000);
            } else {
                console.log(response.message);
                alertmsg(response.message, "error");
            }
            hideLoader();
        })
        .catch(error => {
            console.error(error);
            // alertmsg("Something went wrong","error");
            
            alertmsg(error.message,"error");
            // alertmsg("Wrong Email or Password","error");
            hideLoader();
        });


};



function alertmsg(message, type="notice") {
    var alert = document.createElement("div");
    alert.className = "alert alert-" + type;
    alert.textContent = message;
    alert.id = "alert";
  
    document.body.appendChild(alert);
  
    setTimeout(function () {
      alert.parentNode.removeChild(alert);
    }, 3500);
  }
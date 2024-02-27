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



class ApiClient {

    headers = { };



    constructor(baseURL) {
        this.baseURL = baseURL;
    }

    setHeaders(headers) {
        this.headers = headers;
    }

    handleJSONResponse(response) {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    }

    // Helper function to create common fetch options
    createFetchOptions(method, body) {
        const headers = {
            'Content-Type': 'application/json',
        };

        const options = {
            method,
            headers,
        };

        if (body) {
            options.body = body;
        }

        return options;
    }

    // Function to send a JSON request
    sendJSONRequest(endpoint, method = 'GET', jsonData) {
        const options = this.createFetchOptions(method, JSON.stringify(jsonData));

        return fetch(this.baseURL + endpoint, options)
            .then(this.handleJSONResponse)
            .catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
                throw error;
            });
    }

    // Function to send a FormData request with file upload
    sendFormDataRequest(endpoint, method = 'POST', formData) {
        const options = {
            method,
            // entype: 'multipart/form-data',
            body: formData,
            
        };

        // return fetch(this.baseURL + endpoint, options)
        //     .then(this.handleJSONResponse)
        //     .catch(error => {
        //         console.error('There has been a problem with your fetch operation:', error);
        //         throw error;
        //     });
        return fetch(this.baseURL + endpoint, options)
    .then(response => {
        console.log('Response:', response); // Add this line for logging
        return this.handleJSONResponse(response);
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
        throw error;
    });

    }



    // Function to upload an image along with JSON data
    uploadImageWithJSON(endpoint, imageData, jsonData) {
        const formData = new FormData();

        // Append the image data to the form data
        formData.append('image', imageData, 'image.jpg');

        // Append JSON data as a string
        formData.append('json', JSON.stringify(jsonData));

        // Add auth headers for form data




        return this.sendFormDataRequest(endpoint, 'POST', formData);
    }

    uploadFilesWithJSON(endpoint, filesData, jsonData) {
        const formData = new FormData();

        // filesData is key value pair of filename and file data

        // Append the image data to the form data
        for (const [key, value] of Object.entries(filesData)) {
            formData.append(key, value);
        }

        // Append JSON data as a string
        formData.append('json', JSON.stringify(jsonData));

        return this.sendFormDataRequest(endpoint, 'POST', formData);
    }

    // Example of another useful method (GET request)
    get(endpoint) {
        return this.sendJSONRequest(endpoint, 'GET');
    }

// Add more methods for other HTTP methods as needed (PUT, DELETE, etc.)
}


function getCookie(name) {
    var cookieValue = null;
    if (document.cookie && document.cookie !== '') {
        var cookies = document.cookie.split(';');

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();

            // Does this cookie string begin with the name we want?
            if (cookie.substring(0, name.length + 1) === name + '=') {

                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));

                break;
            }
        }
    }
    return cookieValue;
}



function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}



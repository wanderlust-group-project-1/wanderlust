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
    constructor(baseURL) {
        this.baseURL = baseURL;
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
            body: formData,
        };

        return fetch(this.baseURL + endpoint, options)
            .then(this.handleJSONResponse)
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

        return this.sendFormDataRequest(endpoint, 'POST', formData);
    }

    // Example of another useful method (GET request)
    get(endpoint) {
        return this.sendJSONRequest(endpoint, 'GET');
    }

// Add more methods for other HTTP methods as needed (PUT, DELETE, etc.)
}
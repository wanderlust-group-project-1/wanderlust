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

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
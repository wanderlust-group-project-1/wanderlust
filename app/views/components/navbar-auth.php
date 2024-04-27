<nav class="auth-nav">
    <!-- <div class="auth-nav">  -->
        <a href="<?= ROOT_DIR ?>/">
    
    <div class="logo" style="text-align: center;">
    <img src="<?= ROOT_DIR ?>/assets/images/logo.png" alt="logo" style="display: block; margin: auto;">
    <!-- </div> -->
</div>
</a>


</nav>

<script>
// Get the profile avatar element and the dropdown menu
var profileAvatar = document.querySelector('.profile-avatar');
var dropdownMenu = document.getElementById('nav-dropdown');

// Toggle the dropdown menu when clicking on the profile avatar
if (profileAvatar) {
profileAvatar.addEventListener('click', function(event) {
    // Prevent the default behavior of the anchor tags
    event.preventDefault();

    // Toggle the display style of the dropdown menu
    if (dropdownMenu.style.display === 'block') {
        dropdownMenu.style.display = 'none';
    } else {
        dropdownMenu.style.display = 'block';
    }
});

// Close the dropdown menu if the user clicks outside of it
document.addEventListener('click', function(event) {
    if (!profileAvatar.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
    }
});

// Prevent event propagation when clicking on dropdown links
dropdownMenu.addEventListener('click', function(event) {
    event.stopPropagation();
});

}


    </script>
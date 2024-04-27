<?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
  $user = $_SESSION['USER'];
}
?>

<div class="toggle-button" onclick="toggleSidebar()">☰</div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="guide-dash-prof mt-3">
    <div class="details flex-d-c">

      <div class="user-image">
        <img src="<?php echo ROOT_DIR ?>/assets/images/7.png" alt="">
      </div>
    </div>

    <div class="options flex-d-c">
      <h1 class="name"> <?php echo $user->name; ?></h1>
      <p class="email"> <?php echo $user->email; ?></p>
      <p class="number"> <?php echo $user->mobile; ?></p>
    </div>

    <div class="">
      <button type="submit" class="btn-edit mt-2 edit-profile">
        Edit Profile
      </button>
    </div>
  </div>

  <ul class="nav">

    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/dashboard">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/guideprofile" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">My Guide Profile</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>


    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/packages" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">My Packages</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/guideavailability" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Availability</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/guidebookings" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Bookings</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/complaints" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Complaints</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/guidestatistics" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Statistics</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li>

    <div class="guide-dash-prof">
      <div class="">
        <a href="<?php echo ROOT_DIR ?>/settings" class="ml-7 mr-2">
          <i class="fas fa-cog" aria-hidden="true"></i>
        </a>
        <a href="<?php echo ROOT_DIR ?>/logout"> <button type="submit" class="btn-edit edit-profile">
          Logout
        </button>
      </div>
    </div>
  </ul>
</nav>





<!-- Modal Box Profile Edit -->
<div class="profile-editor" id="profile-editor">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="profile-info">
      <img src="<?php echo ROOT_DIR ?>/assets/images/7.png" alt="Profile Image" class="profile-image">

      <form id="guide" action="<?= ROOT_DIR ?>/guide/update" method="post">
        <h2>Update Guides Details</h2>
        <?php if (isset($errors)) : ?>
          <div> <?= implode('<br>', $errors) ?> </div>
        <?php endif; ?>

        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

        <label for="address">Address</label>
        <input type="text" name="address" id="address" value="<?= $user->address ?>" required>

        <label for="email">Email</label>
        <input type="text" name="email" id="email" value="<?= $user->email ?>" required>

        <label for="nic">NIC</label>
        <input type="text" name="nic" id="nic" value="<?= $user->nic ?>" required>

        <label for="mobile">Mobile No</label>
        <input type="text" name="mobile" id="mobile" value="<?= $user->mobile ?>" required>

        <label for="gender">Gender</label>
        <input type="text" name="gender" id="gender" value="<?= $user->gender ?>" required>

        <input type="submit" class="btn mt-4" name="submit" value="Update">
      </form>



    </div>
  </div>
</div>
<!-- Modal Box Profile Edit End -->

<script>
  function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  }

  // function toggleSidebar() {
  //     var sidebar = document.getElementById("sidebar");
  //     sidebar.classList.toggle("sidebar-offcanvas");
  // }
</script>

<script>
  var modal = document.getElementById("profile-editor");

  var span = document.getElementsByClassName("close")[0];

  // Get all view buttons
  var viewButton = document.querySelector('.edit-profile');

  // Function to handle modal display
  function openModal() {
    // document.getElementById("modal-content").innerHTML = content;
    modal.style.display = "block";
  }


  // Add click event listener to view buttons
  viewButton.addEventListener('click', function() {

    // var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
    // var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
    openModal();
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
</script>
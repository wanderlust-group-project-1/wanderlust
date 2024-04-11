<?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
  $user = $_SESSION['USER'];
}
?>

<div class="toggle-button" onclick="toggleSidebar()">☰</div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="guide-dash-prof  mt-3 justify-content-center">
    <div class="details flex-d-c justify-content-center">

      <div class="user-image">
        <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="">
      </div>
    </div>

    <div class="options flex-d-c">
      <h2 class="name"> <?php echo $user->name; ?></h2>
      <!-- <p class="email"> <?php echo $user->email; ?></p> -->
      <!-- <p class="number"> <?php echo $user->mobile; ?></p> -->
    </div>

    <div class="">
      <button type="submit" class="btn-edit mt-4" id="edit-profile">
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/equipments" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Equipments</span>
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/orders" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Orders</span>
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

    <!-- <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/customers" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Customers</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li> -->



    <!-- <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/tips">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Tips and Know-hows</span>
      </a>
    </li> -->

    <!-- <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/rentalServices/item" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Items</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li> -->
    <!-- 
    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/blogs">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Complaints</span>
      </a>
    </li> -->

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="charts" aria-expanded="false" aria-controls="ui-basic">
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
  </ul>
</nav>

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
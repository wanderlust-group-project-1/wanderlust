<?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
        $user = $_SESSION['USER'];  
}
        ?>

<div class="toggle-button" onclick="toggleSidebar()">☰</div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/dashboard">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/availability" aria-expanded="false" aria-controls="ui-basic">
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/packages" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Orders</span>
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/bookings" aria-expanded="false" aria-controls="ui-basic">
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

    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/approve-blogs">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Approve Blogs</span>
      </a>
    </li>

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
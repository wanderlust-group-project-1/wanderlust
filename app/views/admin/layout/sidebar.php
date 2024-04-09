<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/admin/dashboard">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/admin/rentalServices" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Users</span>
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/admin/rentalServices" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Rental Services</span>
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/admin/guides" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Guides</span>
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
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/admin/customers" aria-expanded="false" aria-controls="ui-basic">
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
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/admin/blogs">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Blogs</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/admin/tips">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Tips and Knowhows</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/admin/rentalServices/item" aria-expanded="false" aria-controls="ui-basic">
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
    </li>

    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/admin/blogs">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Complaints</span>
      </a>
    </li>

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
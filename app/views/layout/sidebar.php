<?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
  $user = $_SESSION['USER'];
}
?>

<div class="toggle-button" onclick="toggleSidebar()">☰</div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="div-1">
    <div class="div-12">
      <div class="text-wrapper">Hello <?php echo $user->name ?>!</div>
      <div class="img-1">
        <img src="<?php echo ROOT_DIR ?>/assets/images/1.png" alt="">
      </div>
    </div>

    <form class="div-3">

      <div class="div-4">
        <div class="div-wrapper">
          <div class="text-wrapper-2">Name : <?php echo $user->name ?></div>
        </div>
        <div class="div-wrapper">
          <div class="text-wrapper-2">NIC : <?php echo $user->nic ?></div>
        </div>
        <div class="div-wrapper">
          <div class="text-wrapper-2">Role : Customer</div>
        </div>
      </div>

      <div class="div-4">
        <div class="div-wrapper">
          <div class="text-wrapper-2">Email : <?php echo $user->email ?></div>
        </div>
        <div class="div-wrapper">
          <div class="text-wrapper-2">Mobile : <?php echo $user->number ?></div>
        </div>
        <div class="div-wrapper">
          <div class="text-wrapper-2">Address : <?php echo $user->address ?></div>
        </div>
      </div>

    </form>

  </div>


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
<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/admin/components/navbar.php');

// require_once('../app/views/admin/layout/sidebar.php');

?>

<div class="dashboard">
  <?php require_once('../app/views/admin/layout/sidebar.php');
  ?>

  <div class="sidebar-flow"></div>


  <!-- <?php
        require_once('../app/views/admin/layout/header.php');
        require_once('../app/views/admin/components/navbar.php');

        require_once('../app/views/admin/layout/sidebar.php');
        ?> -->

  <div class="table-container">
    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
  </div>

  <script>
    var xValues = ["Rental Services", "Guides", "Customers"];
    var yValues = [15, 27, 32];
    var barColors = ["#8D9E6F", "#526534", "#8D9E6F"];

    new Chart("myChart", {
      type: "bar",
      data: {
        labels: xValues,
        datasets: [{
          backgroundColor: barColors,
          data: yValues
        }]
      },
      options: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: "Wanderlust Users"
        }
      }
    });
  </script>

  <?php
  require_once('../app/views/admin/layout/footer.php');
  ?>
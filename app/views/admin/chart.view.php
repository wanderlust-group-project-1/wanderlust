<?php
require_once('../app/views/admin/layout/header.php');
require_once('../app/views/admin/layout/sidebar.php');
?>

<div class="table-container">
    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
</div>

<script>
var xValues = ["Rental Services", "Guides", "Registered Customers"];
var yValues = [192, 89, 369];
var barColors = ["green","blue","orange"];

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
    legend: {display: false},
    title: {
      display: true,
      text: "Wanderlust Users - 2023"
    }
  }
});
</script>

<?php
require_once('../app/views/admin/layout/footer.php');
?>

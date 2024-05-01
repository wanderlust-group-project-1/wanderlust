<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/admin/components/navbar.php');

// require_once('../app/views/admin/layout/sidebar.php');

?>

<div class="dashboard">
  <?php require_once('../app/views/admin/layout/sidebar.php');
  ?>

  <div class="sidebar-flow"></div>

  <div class="guide-dash-main">
    <h1 class="title mb-2">Statistics</h1>
    <ul class="breadcrumbs">
      <li><a href="<?= ROOT_DIR ?>/admin/dashboard">Dashboard</a></li>
      <li class="divider">/</li>
      <li><a href="#" class="active">Statistics</a></li>
    </ul>

    <div class="guide-dash-main">


      <div class="data">
        <div class="content-data">
          <div class="head">
            <h3>Wanderlust Users</h3>
          </div>

          <div class="table-container">
            <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
          </div>

          <script>
            var guides = <?php if ($guides && $guides[0] && $guides[0]->count > 0) : ?>
            <?php echo $guides[0]->count; ?>
            <?php else : ?>
              0
            <?php endif; ?>


            var customers = <?php if ($customers && $customers[0] && $customers[0]->count > 0) : ?>
            <?php echo $customers[0]->count; ?>
            <?php else : ?>
              0
            <?php endif; ?>

            var rentals = <?php if ($rentalServices && $rentalServices[0] && $rentalServices[0]->count > 0) : ?>
            <?php echo $rentalServices[0]->count; ?>
            <?php else : ?>
              0
            <?php endif; ?>



            var xValues = ["Rental Services", "Guides", "Customers"];
            var yValues = [rentals, guides, customers];
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
                // title: {
                //   display: true,
                //   text: "Wanderlust Users"
                // }
              }
            });
          </script>
        </div>
      </div>

      <!-- -----Rental Service----- -->
      <div class="data">
        <div class="content-data">
          <div class="head">
            <h3>Rental Services Bookings</h3>
            <div class="menu">
              <ul class="menu-link">
                <li><a href="#">Edit</a></li>
                <li><a href="#">Save</a></li>
                <li><a href="#">Remove</a></li>
              </ul>
            </div>
          </div>
          <div class="chart">
            <div id="chart2"></div>
          </div>
        </div>
      </div>




      <?php
      require_once('../app/views/admin/layout/footer.php');
      ?>


      <!-- Include the ApexCharts library -->
      <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

      <script>
        $(document).ready(function() {


          // get monthly completed rental count

          $.ajax({
            headers: {
              'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/statistics/adminRental',
            type: 'GET',
            success: function(response) {
              console.log(response);
              console.log(response.data);
              console.log(response.data.rentalCount);
              console.log(response.data.itemCount);

              var options = {
                series: [{
                    name: 'Completed Rentals',
                    data: response.data.rentalCount
                  },
                  {
                    name: 'Items Rented',
                    data: response.data.itemCount
                  }


                ],
                chart: {
                  height: 300,
                  type: 'area',
                  fontFamily: 'Poppins, sans-serif'
                },
                dataLabels: {
                  enabled: false
                },
                stroke: {
                  curve: 'smooth'
                },
                xaxis: {
                  type: 'category',
                  categories: response.data.months
                },
                tooltip: {
                  x: {
                    format: 'dd/MM/yy HH:mm'
                  },
                },
                colors: ['#B2BDA0', '#2F3B1C']
              };

              var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
              chart2.render();


            }
          });


          // MENU
          $('main .content-data .head .menu').each(function() {
            var $icon = $(this).find('.icon');
            var $menuLink = $(this).find('.menu-link');

            $icon.click(function(e) {
              $menuLink.toggleClass('show');
              e.stopPropagation(); // Prevents the event from bubbling up the DOM tree
            });

            // Close menu when clicking outside
            $(window).click(function(e) {
              if (!$(e.target).is($icon) && !$(e.target).is($menuLink) && $menuLink.hasClass('show')) {
                $menuLink.removeClass('show');
              }
            });
          });

          // PROGRESSBAR
          $('main .card .progress').each(function() {
            var value = $(this).data('value');
            $(this).css('--value', value);
          });

          // APEXCHART
          var options = {
            series: [{
                name: 'You',
                data: [5, 12, 15, 10, 8, 14, 11]
              },
              {
                name: 'Rank #1',
                data: [8, 10, 7, 12, 10, 20, 18]
              }
            ],
            chart: {
              height: 300,
              type: 'area',
              fontFamily: 'Poppins, sans-serif'
            },
            dataLabels: {
              enabled: false
            },
            stroke: {
              curve: 'smooth'
            },
            xaxis: {
              type: 'category',
              categories: ["Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar"]
            },
            tooltip: {
              x: {
                format: 'dd/MM/yy HH:mm'
              },
            },
            colors: ['#B2BDA0', '#2F3B1C']
          };

          // var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
          // chart2.render();
        });
      </script>





      <!-- --------Guide booking-------- -->


      <div class="data">
        <div class="content-data">
          <div class="head">
            <h3>Guide Bookings</h3>
            <div class="menu">
              <ul class="menu-link">
                <li><a href="#">Edit</a></li>
                <li><a href="#">Save</a></li>
                <li><a href="#">Remove</a></li>
              </ul>
            </div>
          </div>
          <div class="chart">
            <div id="chart1"></div>
          </div>
        </div>
      </div>

      <?php
      require_once('../app/views/layout/footer.php');

      ?>
      <!-- Include the ApexCharts library -->
      <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

      <script>
        // MENU
        const allMenu = document.querySelectorAll('main .content-data .head .menu');

        allMenu.forEach(item => {
          const icon = item.querySelector('.icon');
          const menuLink = item.querySelector('.menu-link');

          icon.addEventListener('click', function() {
            menuLink.classList.toggle('show');
          });

          // Close menu when clicking outside
          window.addEventListener('click', function(e) {
            if (e.target !== icon && e.target !== menuLink) {
              if (menuLink.classList.contains('show')) {
                menuLink.classList.remove('show');
              }
            }
          });
        });

        // PROGRESSBAR
        const allProgress = document.querySelectorAll('main .card .progress');

        allProgress.forEach(item => {
          item.style.setProperty('$value', item.dataset.value);
        });

        // APEXCHART
        var options = {
          series: [{
            //   name: 'You',
            //   data: [5, 12, 15, 10, 8, 14, 11] // Adjusted data values, total less than 100
            // },
            // {
            name: 'Rank #1',
            data: [8, 10, 7, 12, 10, 20, 18] // Adjusted data values, total less than 100
          }],
          chart: {
            height: 300,
            type: 'area',
            fontFamily: 'Poppins, sans-serif'
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          xaxis: {
            type: 'category', // Change type to 'category' for non-datetime values
            categories: ["Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar"] // Use month names instead of date strings
          },
          tooltip: {
            x: {
              format: 'dd/MM/yy HH:mm'
            },
          },
          colors: ['#526534'] // Specify the colors you want here
        };

        var chart1 = new ApexCharts(document.querySelector("#chart1"), options);
        chart1.render();
      </script>


      <!-- ------complains--------- -->


      <div class="data">
        <div class="content-data">
          <div class="head">
            <h3>Complaints from Users</h3>
          </div>

          <div class="chart-container" style="position: relative; height:400px; width:80vw">
            <canvas id="myPieChart"></canvas>
          </div>

          <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
          <script>
            var labels = ["Rental Services", "Guides", "Customers"];
            var data = [2, 1, 3];
            var backgroundColors = ["#8D9E6F", "#526534", "#8D9E6F"];

            var ctx = document.getElementById("myPieChart").getContext('2d');
            var myPieChart = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: labels,
                datasets: [{
                  backgroundColor: backgroundColors,
                  data: data
                }]
              },
              options: {
                legend: {
                  display: true,
                  position: 'right',
                  labels: {
                    fontSize: 14,
                    fontColor: '#333',
                    boxWidth: 20,
                    padding: 20
                  }
                },
                title: {
                  display: true,
                  text: 'Wanderlust Users',
                  fontSize: 20,
                  fontColor: '#333',
                  padding: 20
                }
              }
            });
          </script>

        </div>
      </div>
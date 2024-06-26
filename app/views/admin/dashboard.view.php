<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/admin/components/navbar.php');
?>
      <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="dashboard">
      <?php require_once('../app/views/admin/layout/sidebar.php');
      ?>

      <div class="sidebar-flow"></div>

      <div class="admin-dash-main">
            <h1 class="title mb-2">Dashboard</h1>
            <ul class="breadcrumbs">
                  <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
                  <li class="divider">/</li>
                  <li><a href="#" class="active">Dashboard</a></li>
            </ul>

            <div class="info-data mt-5">
                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Rental Services</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <?php if ($rentalServices && $rentalServices[0] && $rentalServices[0]->count > 0) : ?>
                                                      <?php echo $rentalServices[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?>


                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/rentalServices">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Guides</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <?php if ($guides && $guides[0] && $guides[0]->count > 0) : ?>
                                                      <?php echo $guides[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?>
                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/guides">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Customers</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <?php if ($customers && $customers[0] && $customers[0]->count > 0) : ?>
                                                      <?php echo $customers[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?>

                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/customers">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

            </div>

            <div class="info-data mt-5">
                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Orders</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <?php if ($orders && $orders[0] && $orders[0]->count > 0) : ?>
                                                      <?php echo $orders[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?>
                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/charts">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Complaints</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <?php if ($rentComplaints && $rentComplaints[0] && $rentComplaints[0]->count > 0) : ?>
                                                      <?php echo $rentComplaints[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?>
                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/complains">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                  </div>

                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Equipments</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>
                                                <!-- <?php if ($tips && $tips[0] && $tips[0]->count > 0) : ?>
                                                      <?php echo $tips[0]->count; ?>
                                                <?php else : ?>
                                                      0
                                                <?php endif; ?> -->
                                                <?php echo $equipments; ?>
                                          </h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <!-- <a href="<?php echo ROOT_DIR ?>/admin/tips">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a> -->
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>




            <!-- Rental Services Bookings -->

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












            <div class="info-data mt-5">
                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Users </h3>
                                          </div>
                                    </div>

                              </div>
                              <div class="flex-d-c">
                                    <!-- <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/charts">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div> -->
                              </div>
                        </div>

                        <!-- <div class="flex-d-c"> -->


                        <div class="table-container mb-5 ">
                              <canvas id="myPieChart" style="width:50%;max-width:300px"></canvas>
                        </div>

                        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
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


                              var labels1 = ["Rental Services", "Guides", "Customers"];
                              var data = [rentals, guides, customers];
                              var backgroundColors = ["#8D9E6F", "#526534", "#8D9E6F"];

                              var ctx = document.getElementById("myPieChart").getContext('2d');
                              var myPieChart = new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                          labels: labels1,
                                          datasets: [{
                                                backgroundColor: backgroundColors,
                                                data: data
                                          }]
                                    },
                                    options: {
                                          legend: {
                                                display: true,
                                                position: 'top',
                                                labels: {
                                                      fontSize: 14,
                                                      fontColor: '#333',
                                                      boxWidth: 9,
                                                      margin: 1,
                                                      padding: 10,
                                                }
                                          }
                                    }
                              });
                        </script>



                        <!-- </div> -->


                  </div>









                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Statistics</h3>
                                          </div>
                                    </div>

                                    <!-- <div class="flex-d-r">
                                          <h1>13</h1>

                                    </div> -->
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/charts">
                                                <button type=" submit" class="btn-text-green   border " id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>
                        <div class="flex-d-c">
                              <!-- <div class="flex-d-c"> -->
                              <!-- <div class="content-data">
                                          <div class="head"> -->
                              <p>Equipment Rentals</p>
                              <!-- </div> -->

                              <div class="table-container mb-5 ">
                                    <canvas id="myChart" style="width:50%;max-width:300px"></canvas>
                              </div>

                              <script>
                                    var xValues = ["Jan", "Feb", "Mar", "Apr"];
                                    var yValues = [0, 8, 1, 9];
                                    var barColors = ["#8D9E6F", "#526534", "#8D9E6F", "#526534"];

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
                                                      display: false,
                                                },

                                          }
                                    });
                              </script>


                              <p>Guide Booking</p>
                              <div class="table-container mb-5 ">
                                    <canvas id="myChart3" style="width:50%;max-width:300px"></canvas>
                              </div>

                              <script>
                                    var xValues = ["Jan", "Feb", "Mar", "Apr"];
                                    var yValues = [1, 6, 5, 10];
                                    var barColors = ["#8D9E6F", "#526534", "#8D9E6F", "#526534"];

                                    new Chart("myChart3", {
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
                                                      display: false,
                                                },

                                          }
                                    });
                              </script>
                        </div>
                        <!-- </div>

            </div> -->

                  </div>
            </div>





            <div class="info-data mt-5">

                  <div class="card">

                        <!-- Income report generation -->
                        <!-- Select Duration start and end dates -->

                        <div class="head justify-content-center align-items-center">
                              <div>
                                    <h3>Rental Services Income Report</h3>
                                    <!-- <p>Generate Income Report</p> -->
                              </div>

                              <form class="w-100 justify-content-center align-items-center flex-d ">
                                    <div class="row gap-2 ">

                                          <div class="col-lg-4 gap-2 flex-d">
                                                <input type="date" id="start-date" name="start-date">
                                                <input type="date" id="end-date" name="end-date">

                                          </div>
                                          <div class="col-lg-4">
                                                <button class="btn-text-green   border " id="income-report">Generate</button>
                                          </div>


                                    </div>




                              </form>



                        </div>

                  </div>
            </div>
            <div class="info-data mt-5">
                  <div class="card">

                        <!-- Income report generation -->
                        <!-- Select Duration start and end dates -->

                        <div class="head justify-content-center align-items-center">
                              <div class="mr-6">
                                    <h3>Guides Income Report</h3>
                                    <!-- <p>Generate Income Report</p> -->
                              </div>

                              <form class="w-100 justify-content-center align-items-center flex-d ">
                                    <div class="row gap-2 ml-6">

                                          <div class="col-lg-4 gap-2 flex-d">
                                                <input type="date" id="start-date" name="start-date">
                                                <input type="date" id="end-date" name="end-date">

                                          </div>
                                          <div class="col-lg-4">
                                                <button class="btn-text-green   border " id="income-report">Generate</button>
                                          </div>


                                    </div>




                              </form>



                        </div>

                  </div>
            </div>


            <script>
                  $(document).on('click', '#income-report', function() {
                        // prevent default form submission
                        event.preventDefault();
                        // var startDate = $('#start-date').val();
                        // var endDate = $('#end-date').val();

                        var startDate = $(this).closest('form').find('#start-date').val();
                        var endDate = $(this).closest('form').find('#end-date').val();

                        // validate the input
                        if (startDate == '' || endDate == '') {
                              alertmsg('Please select a date range', 'error');
                              return;
                        }

                        // start data < end date

                        if (startDate > endDate) {
                              alertmsg('Start date should be less than end date', 'error');
                              return;
                        }

                        $.ajax({
                              headers: {
                                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                              },
                              url: '<?= ROOT_DIR ?>/api/report/allRentalIncome',
                              type: 'POST',
                              data: JSON.stringify({
                                    from: startDate,
                                    to: endDate
                              }),
                              success: function(response) {
                                    console.log(response);
                                    // open the report in a new tab

                                    var url = '<?= ROOT_DIR ?>/reports/' + response.data.report
                                    window.open(url, '_blank');
                              },
                              error: function(err) {
                                    console.log(err);
                              }
                        });

                  });
            </script>

            <!-- </div>
</div> -->
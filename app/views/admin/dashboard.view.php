<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/admin/components/navbar.php');
?>

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
                                          <h1>15</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/rentalServices">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                          <h1>27</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/guides">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                          <h1>32</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/customers">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                                <h3>Blogs</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>66</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/users">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                          <h1>06</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/customers">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                                <h3>Items</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>135</h1>

                                    </div>
                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/rentalServices/item">
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                                <h3>Users </h3>
                                          </div>
                                    </div>

                              </div>
                              <div class="flex-d-c">
                                    <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/charts">
                                                <button type=" submit" class="btn-success" id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div>
                              </div>
                        </div>

                        <div class="flex-d-c">


                              fwfw
                        </div>


                  </div>








                  <div class="table-container mb-5 ">
                        <canvas id="myPieChart" style="width:50%;max-width:300px"></canvas>
                  </div>

                  <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
                  <script>
                        var labels1 = ["Rental Services", "Guides", "Customers"];
                        var data = [15, 27, 32];
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
                                          position: 'right',
                                          labels: {
                                                fontSize: 14,
                                                fontColor: '#333',
                                                boxWidth: 3,
                                                padding: 20
                                          }
                                    }
                              }
                        });
                  </script>



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
                                                <button type=" submit" class="btn-success" id="see-more">
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
                                    var yValues = [0, 8, 0, 9];
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
                                                      display: true
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
                                    <h3>Income Report</h3>
                                    <!-- <p>Generate Income Report</p> -->
                              </div>

                              <form class="w-100 justify-content-center align-items-center flex-d ">
                                    <div class="row gap-2 ">

                                          <div class="col-lg-4 gap-2 flex-d">
                                                <input type="date" id="start-date" name="start-date">
                                                <input type="date" id="end-date" name="end-date">

                                          </div>
                                          <div class="col-lg-4">
                                                <button class="btn-success" id="income-report">Generate</button>
                                          </div>


                                    </div>




                              </form>



                        </div>

                  </div>
            </div>
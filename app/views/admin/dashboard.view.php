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
                                          <h1>14</h1>

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
                                          <h1>26</h1>

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
                                          <a href="<?php echo ROOT_DIR ?>/admin/    rentalServices/item">
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

                                    <span class="user-progress" data-value="14%"></span>
                                    <span class="label">Rental Services</span>
                                    <span class="progress" data-value="26%"></span>
                                    <span class="label">Guides</span>
                                    <span class="progress" data-value="32%"></span>
                                    <span class="label">Customers</span>

                              </div>
                              <div class="flex-d-c">
                                    <!-- <div class="flex-d-r justify-content-end">
                                          <a href="<?php echo ROOT_DIR ?>/admin/charts">
                                                <button type=" submit" class="btn-success" id="see-more">
                                                      See More >>
                                                </button>
                                          </a>
                                    </div> -->
                              </div>
                        </div>
                  </div>

                  <div class="card">
                        <div class="head">
                              <div class="flex-d-c">
                                    <div class="flex-d-r">
                                          <div>
                                                <h3>Statistics</h3>
                                          </div>
                                    </div>

                                    <div class="flex-d-r">
                                          <h1>13</h1>

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
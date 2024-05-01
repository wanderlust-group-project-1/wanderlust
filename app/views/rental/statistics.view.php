<?php
require_once('../app/views/layout/header.php');



?>




<div class="dashboard">
    <?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Statistics</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Statistics</a></li>
        </ul>


        
        <div class="info-data mt-5">
            <div class="guide-card-new">
                <span class="label">Rents</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p><?php echo $stat->successful_rental_count ?></p>
                </div>
                <span class="label"><?php echo $stat->last_month_rental_count ?>: Per Month</span>
            </div>

            <div class="guide-card-new">
                <span class="label">Total Earning</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p><?php echo $stat->total_earnings ?></p>
                </div>
                <span class="label"><?php echo $stat->current_month_earnings ?>: Per Month</span>
            </div>
            <div class="guide-card-new">
                <span class="label">Equipment Quantity</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p><?php echo $stat->equipment_count ?></p>
                </div>
            </div>
        </div>





        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Monthly Bookings</h3>
                    <div class="menu">
                        <ul class="menu-link">
                            <li><a href="#">Edit</a></li>
                            <li><a href="#">Save</a></li>
                            <li><a href="#">Remove</a></li>
                        </ul>
                    </div>
                </div>
                <div class="chart">
                    <div id="chart"></div>
                </div>
            </div>
        </div>


        <!-- Report  -->


        <div class="info-data mt-5">

            <div class="card">

                <!-- Income report generation -->
                <!-- Select Duration start and end dates -->

                <div class="head justify-content-center align-items-center flex-md-c">
                    <div class="col-lg-4 col-md-12 mw-300px">
                        <h3>Income Report</h3>
                        <!-- <p>Generate Income Report</p> -->
                    </div>

                    <form class="col-lg-7 w-100 justify-content-center align-items-center flex-d ">
                        <div class="row gap-2 justify-content-between report-form">

                            <div class="col-lg-4  gap-2 flex-d">
                                <input type="date" id="start-date" name="start-date">
                                <input type="date" id="end-date" name="end-date">

                            </div>
                            <div class="col-lg-3">
                                <button class="btn-text-green border" id="income-report">Generate</button>
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

                <div class="head justify-content-center align-items-center flex-md-c">
                    <div class="col-lg-4 col-md-12 mw-300px">
                        <h3>Equipment Report</h3>
                        <!-- <p>Generate Equipment Report</p> -->
                    </div>

                    <form class="col-lg-7 w-100 justify-content-center align-items-center flex-d ">
                        <div class="row gap-2 justify-content-between">

                            <div class="col-lg-4  gap-2 flex-d">
                                <input type="date" id="start-date" name="start-date">
                                <input type="date" id="end-date" name="end-date">

                            </div>
                            <div class="col-lg-3">
                                <button class="btn-text-green border" id="equipment-report">Generate</button>
                            </div>


                        </div>




                    </form>



                </div>

            </div>




        </div>




    </div>

    <?php
    require_once('../app/views/layout/footer.php');

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
                url: '<?= ROOT_DIR ?>/api/statistics/monthlyCompletedRentalCount',
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

                    var chart = new ApexCharts(document.querySelector("#chart"), options);
                    chart.render();


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

            // var chart = new ApexCharts(document.querySelector("#chart"), options);
            // chart.render();
        });



        // Report Generation

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
                url: '<?= ROOT_DIR ?>/api/report/rentalIncome',
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




        $(document).on('click', '#equipment-report', function() {
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
                url: '<?= ROOT_DIR ?>/api/report/GetEquipmentRentalCountByRentalService',
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
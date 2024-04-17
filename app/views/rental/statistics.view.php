
<?php
require_once('../app/views/layout/header.php');
require_once('../app/views/admin/components/navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
<?php require_once('../app/views/rental/layout/sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main">
        <h1 class="title mb-2">Dashboard</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Dashboard</a></li>
        </ul>
        <div class="info-data mt-5">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>96</h2>
                        <p>No of Tours</p>
                    </div>
                </div>
                <span class="progress" data-value="12.5%"></span>
                <span class="label">12 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>Rs.139000</h2>
                        <p>Income</p>
                    </div>
                </div>
                <span class="progress" data-value="60%"></span>
                <span class="label">Rs.60 000 : Per Month</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>1st March</h2>
                        <p>Upcoming Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p>Micheal Julius</p>
                </div>
                <!-- <span class="progress" data-value="30%"></span>
                <span class="label">30%</span> -->
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>21st February</h2>
                        <p>Recent Booking</p>
                    </div>
                </div>
                <div class="booking-bar .flex-d mt-4 mb-2 ml-2">
                    <p>Julius John</p>
                </div>
                <!-- <span class="progress" data-value="80%"></span>
                <span class="label">80%</span> -->
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

            <div class="head justify-content-center align-items-center">
                <div>
                    <h2>Income Report</h2>
                    <!-- <p>Generate Income Report</p> -->
                </div>

                <form class="w-100 justify-content-center align-items-center flex-d ">
                    <div class="row gap-2 ">

                    <div class="col-lg-4 gap-2 flex-d">
                        <input type="date" id="start-date" name="start-date">
                        <input type="date" id="end-date" name="end-date">

                    </div>
                    <div class="col-lg-4">
                        <button class="btn btn-primary" id="income-report">Generate</button>
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
        var startDate = $('#start-date').val();
        var endDate = $('#end-date').val();

        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/report/rentalIncome',
            type: 'POST',
            data:JSON.stringify({
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
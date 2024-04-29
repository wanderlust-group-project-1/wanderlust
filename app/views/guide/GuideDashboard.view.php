<?php
require_once('../app/views/layout/header.php');
// require_once('../app/views/navbar/rental-navbar.php');

?>

<!-- <link rel="stylesheet" type="text/css" href="<?= ROOT_DIR ?>/assets/css/RentalDashboard.css"> -->



<div class="dashboard">
    <?php require_once('../app/views/guide/layout/guide-sidebar.php');
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
            <div class="guide-card-new">
                <span class="label">No of Tours</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p id="guide-dash-1"></p>
                </div>
            </div>
            <div class="guide-card-new">
                <span class="label">Income</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p id="guide-dash-2"></p>
                </div>
            </div>
            <div class="guide-card-new">
                <span class="label">Upcoming Booking</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p id="guide-dash-3"></p>
                </div>
            </div>
            <div class="guide-card-new">
                <span class="label">Recent Booking</span>
                <div class="booking-bar .flex-d mt-4 mb-2">
                    <p id="guide-dash-4"></p>
                </div>
            </div>
        </div>


        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3 class="Guide-topics">Monthly Bookings</h3>
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


    // APEXCHART
    var options = {
        series: [{
                name: 'You',
                data: [5, 12, 15, 10, 8, 14, 11] // Adjusted data values, total less than 100
            },
            {
                name: 'Rank #1',
                data: [8, 10, 7, 12, 10, 20, 18] // Adjusted data values, total less than 100
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
            type: 'category', // Change type to 'category' for non-datetime values
            // categories: ["Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar"] // Use month names instead of date strings
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
        colors: ['#B2BDA0', '#2F3B1C'] // Specify the colors you want here
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
</script>

<script>
    $(document).ready(function() {
        // console.log(currentDate);
        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/guideBookings/GetUserBookingDetails/',
            type: 'GET',
            success: function(data) {
                console.log(data.data[0]);
                $('#guide-dash-1').text(data.data[0].num_completed_tours);
                $('#guide-dash-2').text(data.data[0].total_income);
                $('#guide-dash-3').text(data.data[0].upcoming_booking_date + ' : ' + data.data[0].upcoming_booking_location);
                $('#guide-dash-4').text(data.data[0].recent_booking_date + ' : ' + data.data[0].recent_booking_location);

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        // console.log(currentDate);
        $.ajax({
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/api/guideBookings/GetMonthlyCompletedBookings/',
            type: 'GET',
            success: function(data) {
                console.log(data.data);


                // Assuming data.data contains the result from your AJAX call

                // Extracting num_bookings data from the response
                const numBookingsData = data.data.map(entry => entry.num_bookings);
                const completedBookingData = data.data.map(entry => entry.num_completed_bookings);

                // Update the series name and data
                options.series[0].name = 'All bookings';
                options.series[0].data = numBookingsData;

                options.series[1].name = 'Completed bookings';
                options.series[1].data = completedBookingData;

                const xaxisCategories = data.data.map(entry => entry.month_year);

                // Update the x-axis categories
                options.xaxis.categories = xaxisCategories;
                


                // Rendering the chart
                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
</script>



<?php
require_once('../app/views/layout/footer.php');

?>
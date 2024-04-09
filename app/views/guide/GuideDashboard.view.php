
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
        series: [
            {
                name: 'You',
                data:  [5, 12, 15, 10, 8, 14, 11] // Adjusted data values, total less than 100
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
            categories: ["Sep", "Oct", "Nov", "Dec", "Jan", "Feb", "Mar"] // Use month names instead of date strings
        },
        tooltip: {
            x: {
                format: 'dd/MM/yy HH:mm'
            },
        },
        colors: ['#B2BDA0', '#2F3B1C'] // Specify the colors you want here
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
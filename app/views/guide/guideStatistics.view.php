<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">Statistics</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Statistics</a></li>
        </ul>

        <div class="guide-profile-content mt-5 tiny-topic">
            <p>View you statistics here</p>

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


        <!-- Report  -->

        <div class="info-data mt-5">

            <div class="guide-card-new">

                <!-- Income report generation -->
                <!-- Select Duration start and end dates -->

                <div class="head justify-content-center align-items-center flex-md-c">
                    <div class="col-lg-4 col-md-12 mw-300px">
                        <h3 class="guide-topics">Income Report</h3>
                        <!-- <p>Generate Income Report</p> -->
                    </div>

                    <form class="col-lg-7 w-100 justify-content-center align-items-center flex-d ">
                        <div class="row gap-2 justify-content-between report-form">

                            <div class="col-lg-4  gap-2 flex-d">
                                <input type="date" id="start-date" name="start-date">
                                <input type="date" id="end-date" name="end-date">

                            </div>
                            <div class="col-lg-3">
                                <button class="btn btn-primary" id="income-report">Generate</button>
                            </div>


                        </div>




                    </form>



                </div>

            </div>

        </div>

    </div>
</div>


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
    const allProgress = document.querySelectorAll('main .guide-card-new .progress');

    allProgress.forEach(item => {
        item.style.setProperty('$value', item.dataset.value);
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



<script>
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
            url: '<?= ROOT_DIR ?>/api/report/bookingIncome',
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


<?php
require_once('../app/views/layout/footer.php');

?>
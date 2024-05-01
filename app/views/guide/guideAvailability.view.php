<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">Availability</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Availability</a></li>
        </ul>
        <div class="guide-profile-content mt-5 tiny-topic">
            <p>Update your Availability</p>

            <div class="calendarandlegend">
                <div class="guide-calendar">
                    <div class="cal_header" data-id="">
                        <button class="cal_prev" onclick="prevMonth()">&#10094;</button>
                        <h4 id="month-year">April 2024</h4>
                        <button class="cal_next" onclick="nextMonth()">&#10095;</button>
                    </div>
                    <div class="cal_weekdays">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>
                    <div class="cal_days" id="days">

                    </div>
                </div>
                <!-- <div class="info-data mt-5">
                <div class="guide-card-new legend">
                    <span class="label">Legend</span>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p>Available Days</p>
                    </div>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p>Today</p>
                    </div>
                </div>
            </div> -->
            </div>
        </div>
    </div>

    <script>
        const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

        const calendar = document.querySelector(".cal_days");
        const monthYearText = document.getElementById("month-year");

        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        document.querySelector('.cal_header').setAttribute('data-id', currentMonth + ' ' + currentYear);

        function displayCalendar() {
            const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
            const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);
            const numDaysInMonth = lastDayOfMonth.getDate();

            const firstDayOfWeek = firstDayOfMonth.getDay();

            monthYearText.textContent = `${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(firstDayOfMonth)} ${currentYear}`;

            calendar.innerHTML = "";

            // set cal_header data-id to current month and year
            $('.cal_header').attr('data-id', currentYear + '-' + (currentMonth + 1));



            // Fill in previous month's days
            for (let i = firstDayOfWeek; i > 0; i--) {
                const prevDate = new Date(currentYear, currentMonth, -i + 1);
                const div = document.createElement("div");
                div.classList.add("cal_prev-date");
                div.textContent = prevDate.getDate();
                calendar.appendChild(div);
            }

            // Fill in current month's days
            for (let i = 1; i <= numDaysInMonth; i++) {
                const button = document.createElement("button"); // Create button element
                button.textContent = i; // Set day number as button text
                //add data-id to each button
                button.setAttribute('data-id', i);
                button.classList.add("cal_day-button"); // Add class to style the button
                if (new Date(currentYear, currentMonth, i) >= currentDate) { // Check if the date is after or equal to current date
                    button.addEventListener("click", () => openModalSchedule(i));
                } else {
                    button.disabled = true;
                }
                if (currentDate.getDate() === i && currentDate.getMonth() === currentMonth && currentDate.getFullYear() === currentYear) {
                    button.classList.add("cal_current-date");
                }
                calendar.appendChild(button);
            }
            // Fill in next month's days
            const numDaysToFill = 42 - numDaysInMonth - firstDayOfWeek;
            for (let i = 1; i <= numDaysToFill; i++) {
                const nextDate = new Date(currentYear, currentMonth + 1, i);
                const div = document.createElement("div");
                div.classList.add("cal_next-date");
                div.textContent = nextDate.getDate();
                calendar.appendChild(div);
            }


            // mark availabie date with green color

            //  day 14 is available

            //  $('.cal_day-button').each(function(){
            //     if($(this).text() == 14){
            //         $(this).addClass('cal_day-available');
            //     }
            // });
            availabilityDays(currentMonth, currentYear);


        }

        function availabilityDays(month, year) {
            var jsonData = {
                currentMonth: month + 1,
                currentYear: year
            };
            console.log(jsonData);

            // Get available days for the current month
            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/guideAvailability/getDays',
                method: 'POST',
                data: JSON.stringify(jsonData),
                contentType: 'application/json',
                processData: false,
                success: function(data) {
                    // Append to a list of available days
                    console.log(data);

                    data.data.forEach(function(day) {
                        // <button data-id="10" class="cal_day-button">10</button>
                        // console.log(day);
                        // data-id =day
                        $('.cal_day-button').each(function() {
                            if ($(this).text() == day.available_day) {
                                $(this).addClass('cal_day-available');
                            }
                        });

                    });
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            displayCalendar();
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            displayCalendar();
        }

        displayCalendar();
    </script>




    <!-- Modal box in schedule -->

    <!-- Modal box to view package details -->
    <div class="modal view-schedule-modal" id="view-schedule-modal" style="display: none;">
        <div class="modal-content">
            <span class="modal-close close">&times;</span>
            <form id="availability-details">
                <h2 class="row">Availability</h2>
                <div class="schedule-details">
                    <table class="table-details">
                        <tr class="">
                            <td class="row"><strong>Date:</strong></td>
                            <td class="row" id="selected-day"></td>
                        </tr>
                        <tr class="row">
                            <td class="row"><strong>Availability:</strong></td>
                            <td class="row"><input type="checkbox" id="availability"></td>
                        </tr>
                    </table>
                </div>
                <div class="row">
                    <input type="submit" class="btn-text-green border" name="submit" value="Update">
                </div>
            </form>
        </div>
    </div>

    <script>
        const currentMonthName = currentDate.toLocaleString('en-US', {
            month: 'long'
        });
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var currentMonthIndex = months.indexOf(currentMonthName);

        $(document).on('click', '.cal_next', function() {
            currentMonthIndex++;
            if (currentMonthIndex >= 12) {
                currentMonthIndex = 0; // Reset to January if it goes beyond December
                currentYear++;
            }
            console.log(currentMonthIndex);
        });

        $(document).on('click', '.cal_prev', function() {
            currentMonthIndex--;
            if (currentMonthIndex < 0) {
                currentMonthIndex = 11; // Reset to December if it goes beyond January
                currentYear--;
            }
        });

        function openModalSchedule(day) {
            const modal = document.getElementById("view-schedule-modal");
            modal.style.display = "block";

            const currentDate = new Date();
            const currentYear = currentDate.getFullYear();

            document.getElementById("selected-day").textContent = `${months[currentMonthIndex]} ${day}, ${currentYear}`;
        }
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // const closeButton = document.querySelector("#view-schedule-modal .close-button");
            // closeButton.addEventListener("click", function() {
            //     const modal = document.getElementById("view-schedule-modal");
            //     modal.style.display = "none";
            // });

            var guideAvailabilityForm = document.getElementById("availability-details");
            guideAvailabilityForm.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submission
                // var formData = new FormData(this);
                var available = $('#availability').is(':checked') ? 1 : 0

                // Assuming $('#selected-day').text() contains the date string in the format 'YYYY-MM-DD'
                var currentDate = new Date($('#selected-day').text());
                currentDate.setDate(currentDate.getDate() + 1);
                var increasedDate = currentDate.toISOString().split('T')[0];

                var jsonData = {
                    // date: $('#selected-day').text(), 'April 4, 2024 converted yyyy-mm-dd'
                    date: increasedDate,
                    availability: available
                }
                console.log(jsonData);

                $.ajax({
                    headers: {
                        'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                    },
                    url: "<?= ROOT_DIR ?>/api/guideAvailability/update",
                    method: "POST",
                    data: JSON.stringify(jsonData),
                    contentType: 'application/json',
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            alertmsg("Availability updated successfully");
                            // Add CSS color if available
                        } else {
                            alertmsg("Failed to update availability");
                        }
                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>

    <?php require_once('../app/views/layout/footer.php'); ?>
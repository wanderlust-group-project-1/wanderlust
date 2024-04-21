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

        <div class="guide-calendar">
            <div class="cal_header">
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
    </div>
</div>

<script>
    const weekdays = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    const calendar = document.querySelector(".cal_days");
    const monthYearText = document.getElementById("month-year");

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    function displayCalendar() {
        const firstDayOfMonth = new Date(currentYear, currentMonth, 1);
        const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);
        const numDaysInMonth = lastDayOfMonth.getDate();

        const firstDayOfWeek = firstDayOfMonth.getDay();

        monthYearText.textContent = `${new Intl.DateTimeFormat('en-US', { month: 'long' }).format(firstDayOfMonth)} ${currentYear}`;

        calendar.innerHTML = "";

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
            button.classList.add("cal_day-button"); // Add class to style the button
            button.addEventListener("click", () => openModalSchedule(i)); // Add click event listener to open modal
            if (currentDate.getDate() === i && currentDate.getMonth() === currentMonth && currentDate.getFullYear() === currentYear) {
                button.classList.add("cal_current-date"); // Add class to highlight current date
            }
            calendar.appendChild(button); // Append button to calendar
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
        <span class="close-button">&times;</span>
        <form id="availability-details">
            <h2>Availability</h2>
            <div class="schedule-details">
                <table class="table-details">
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td id="selected-day"></td>
                    </tr>
                    <tr>
                        <td><strong>Availability:</strong></td>
                        <td><input type="checkbox" id="availability"></td>
                    </tr>
                </table>
            </div>
            <input type="submit" class="btn mt-4" name="submit" value="Update">
        </form>
    </div>
</div>


<script>
    function openModalSchedule(day) {
    // Get the modal element
    const modal = document.getElementById("view-schedule-modal");
    // Open the modal
    modal.style.display = "block";
    
    // Get the current month and year
    const currentDate = new Date();
    const currentMonth = currentDate.toLocaleString('en-US', { month: 'long' });
    const currentYear = currentDate.getFullYear();
    
    // Update the modal content with the selected day, current month, and year
    document.getElementById("selected-day").textContent = `${currentMonth} ${day}, ${currentYear}`;
}
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Close modal button functionality
        const closeButton = document.querySelector("#view-schedule-modal .close-button");
        closeButton.addEventListener("click", function() {
            const modal = document.getElementById("view-schedule-modal");
            modal.style.display = "none";
        });

        var guideAvailabilityForm = document.getElementById("availability-details");
        guideAvailabilityForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission
            // var formData = new FormData(this);
            var available = $('#availability').is(':checked') ? 1 : 0

            var jsonData = {
                // date: $('#selected-day').text(), 'April 4, 2024 converted yyyy-mm-dd'
                date: new Date($('#selected-day').text()).toISOString().split('T')[0],
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
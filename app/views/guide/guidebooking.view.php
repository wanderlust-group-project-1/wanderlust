<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">Bookings</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Bookings</a></li>
        </ul>
        <div class="guide-profile-content mt-5 tiny-topic">
            <p>View your Recent and Upcoming Bookings</p>
            <div class="calendarandbooking">

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
                        <p>Booked Days</p>
                    </div>
                    <div class="booking-bar .flex-d mt-4 mb-2">
                        <p>Today</p>
                    </div>
                </div>
            </div> -->
                <div class="booking-list mt-5" id="booking-list">
                    <!-- Booking list will be inserted here -->
                </div>
                <!-- <div class="info-data mt-5">
                    <div class="guide-card-new booking-history">
                        <span class="label">Upcoming Bookings</span>
                        <div class="booking-bar .flex-d mt-4 mb-2">
                            <p>2024-04-25 - Kandy</p>
                        </div>
                        <div class="booking-bar .flex-d mt-4 mb-2">
                            <p>2024-04-25 - Kandy</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>



    <script>
        function bookingList() {
            $.ajax({
                headers: {
                    Authorization: "Bearer " + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/guideBookings/getAllBookings/',
                type: 'GET',
                success: function(data) {
                    console.log(data.data)
                    bookingHTML(data.data);
                }

            });
        }

        function bookingHTML(bookingDetails) {
            const modalContent = document.getElementById("booking-list");
            modalContent.innerHTML = ""; // Clear previous content

            const currentDate = new Date();

            const recentBookings = [];
            const upcomingBookings = [];

            // Divide bookings into recent and upcoming based on date
            bookingDetails.forEach(booking => {
                const bookingDate = new Date(booking.date);
                if (bookingDate > currentDate) {
                    upcomingBookings.push(booking);
                } else {
                    recentBookings.push(booking);
                }
            });

            // Function to generate HTML for booking details
            function generateBookingHTML(bookings) {
                return bookings.map(booking => `
            <div class="booking-bar .flex-d mt-4 mb-2">
                <p>${booking.date} : ${booking.location}</p>
            </div>
        `).join('');
            }

            const tableHTML = `
        <div class="info-data">
            <div class="guide-card-new booking-history">
                <span class="label">Recent Bookings</span>
                ${generateBookingHTML(recentBookings)}
            </div>
            <div class="guide-card-new booking-history">
                <span class="label">Upcoming Bookings</span>
                ${generateBookingHTML(upcomingBookings)}
            </div>  
        </div>`;
            modalContent.innerHTML = tableHTML;
        }


        bookingList();
    </script>

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
                // button.addEventListener("click", () => openModalSchedule(i)); // Add click event listener to open modal
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
            bookedDays(currentMonth, currentYear);

            // $('.cal_day-button').each(function() {
            //     if ($(this).text() == 25) {
            //         $(this).addClass('cal_day-booked');
            //     }
            // });
        }


        function bookedDays(month, year) {
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
                url: '<?= ROOT_DIR ?>/api/guideBookings/getDays',
                method: 'POST',
                data: JSON.stringify(jsonData),
                contentType: 'application/json',
                processData: false,
                success: function(data) {
                    // Append to a list of available days
                    console.log(data);

                    data.data.forEach(function(day) {
                        // <button data-id="10" class="cal_day-button">10</button>
                        console.log(day);
                        // data-id =day
                        $('.cal_day-button').each(function() {
                            if ($(this).text() === day.booked_day) {
                                $(this).addClass('cal_day-booked');

                                // Store a reference to the current button element
                                const $button = $(this);

                                // Attach a click event handler to the button
                                $button.click(function() {
                                    // Call the openModalSchedule function with the day as parameter
                                    openModalSchedule($button.text());

                                    // Call deleteBooking function only for the clicked button
                                    deleteBooking($button.text());
                                });

                                console.log($button.text()); // This line seems unnecessary now, you can remove it
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


    <script>
        function openModalSchedule(bookedDay) {
            $(document).on('click', '.delete-package-button', function() {
                var modal = document.getElementById("delete-booking-modal");
                modal.style.display = "block";
            });

            const modal = document.getElementById("view-booking-modal");
            modal.style.display = "block";

            // Make AJAX request to fetch booking details for the selected day
            $.ajax({
                headers: {
                    Authorization: "Bearer " + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/guideBookings/getBookingDetailsByDate/' + currentYear + '-' + (currentMonth + 1) + '-' + bookedDay,
                method: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    console.log(data.data['bookingDetails'], data.data['userDetails']);
                    // var modalContent = document.querySelector('#booking-details-container .modal-content');
                    // modalContent.innerHTML = data;
                    populateBookingDetails(data.data['bookingDetails'], data.data['userDetails']);
                    var closeBtn = document.querySelector('#view-booking-modal .close-button');
                    closeBtn.addEventListener('click', function() {
                        modal.style.display = "none";
                        console.log("Modal closed");
                    });
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }


        function populateBookingDetails(bookingDetails, userDetails) {
            // Access modal content container
            const modalContent = document.getElementById("booking-details-container");
            modalContent.innerHTML = ""; // Clear previous content

            // Create HTML to display booking details
            const tableHTML = `
                <h2 class="guide-h2-title">Booking Details</h2>
                <div class="booking-details">
                    <table class="table-details">
                        <tr>
                            <td><strong>Date:</strong></td>
                            <td>${bookingDetails.date}</td>
                        </tr>
                        <tr>
                            <td><strong>No of People:</strong></td>
                            <td>${bookingDetails.no_of_people}</td>
                        </tr>
                        <tr>
                            <td><strong>Place:</strong></td>
                            <td>${bookingDetails.location}</td>
                        </tr>
                        <tr>
                            <td><strong>Transport supply:</strong></td>
                            <td>${bookingDetails.transport_supply}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>${bookingDetails.status}</td>   
                        </tr>
                        <tr>
                            <td><strong>Customer Name:</strong></td>
                            <td>${userDetails.name}</td>
                        </tr>
                        <tr>
                            <td><strong>Customer Contact Number:</strong></td>
                            <td>${userDetails.number}</td>
                        </tr>
                    </table>
                </div>
            `;

            // Insert HTML into modal content container
            modalContent.innerHTML = tableHTML;
        }
    </script>

    <!-- Modal box to view booking details -->
    <div class="modal modal view-booking-modal" id="view-booking-modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <div id="booking-details-container">
                <!-- Booking details will be inserted here -->
            </div>
            <div class="flex-d gap-2 mt-5">
                <button class="btn btn-danger delete-package-button">Cancel Booking</button>
                <button class="btn btn-success edit-package-button">Completed</button>
            </div>
        </div>

        <div id="delete-booking-modal" class="delete-booking-modal modal">
            <div class="modal-content ">
                <span class="close ">&times;</span>
                <h2 class="guide-h2-title">Delete Booking</h2>
                <p>Are you sure you want to cancel this booking?</p>
                <div class="flex-d gap-2 mt-5">
                    <button id="delete-booking" class="btn btn-danger">Confirm</button>
                    <button id="cancel-delete" class="btn modal-close">Cancel</button>
                </div>

            </div>
        </div>

        <style>
            .delete-booking-modal {
                display: none;
                position: fixed;
                z-index: 200;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                background-color: rgba(0, 0, 0, 0.4);
                /* Unified background color */
                /* padding-top: 60px; */
            }
        </style>

        <script>
            function deleteBooking(bookedDay) {
                console.log('complete booking');
                $(document).on('click', '.edit-package-button', function() {
                    // Make AJAX request to delete the booking
                    $.ajax({
                        headers: {
                            'Authorization': "Bearer " + getCookie('jwt_auth_token')
                        },
                        url: '<?= ROOT_DIR ?>/api/guideBookings/completeBooking/' + currentYear + '-' + (currentMonth + 1) + '-' + bookedDay,
                        method: 'POST',
                        contentType: 'application/json',
                        success: function(data) {
                            console.log(data);
                            modal.style.display = "none";
                            alertmsg("Booking completion updated successfully");
                            location.reload();
                        },
                        error: function(err) {
                            console.log(err);
                        },
                        complete: function() {
                            var modal = document.getElementById("delete-booking-modal");
                            modal.style.display = "none";
                        }
                    });
                });

                console.log('delete booking');
                $(document).on('click', '#delete-booking', function() {
                    // Make AJAX request to delete the booking
                    $.ajax({
                        headers: {
                            'Authorization': "Bearer " + getCookie('jwt_auth_token')
                        },
                        url: '<?= ROOT_DIR ?>/api/guideBookings/deleteBooking/' + currentYear + '-' + (currentMonth + 1) + '-' + bookedDay,
                        method: 'POST',
                        contentType: 'application/json',
                        success: function(data) {
                            console.log(data);
                            modal.style.display = "none";
                            alertmsg("Booking deleted successfully");
                            location.reload();
                        },
                        error: function(err) {
                            console.log(err);
                        },
                        complete: function() {
                            var modal = document.getElementById("delete-booking-modal");
                            modal.style.display = "none";
                        }
                    });
                });

                $(document).on('click', '#cancel-delete', function() {
                    var modal = document.getElementById("delete-booking-modal");
                    modal.style.display = "none";
                });
            }
        </script>

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

            

        </script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="GuideMyBooking.css" rel="stylesheet">

</head>

<body>
    <div class="guide-booking">
        <div class="CalendarView">
            <img class="image" src="<?php echo ROOT_DIR?>/assets/images/6.png" />
            <button id=updateCal class="small-button">Update</button>
        </div>

        <script>
            document.getElementById("updateCal").addEventListener("click", function () {
                window.location.href = "calendar.html";
            });
        </script>

        <div class="frame2">
            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">Upcoming Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">Oct 02</div>
            </div>

            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">Upcoming Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">Sep 25</div>
            </div>

            <div class="yellow-card">
                <div class="upper-card-text">
                    <div class="text-card-topic">First Tour</div>
                    <div class="edit-prof-button">
                        <button type="submit" class="small-button-middle">
                            More &gt
                        </button>
                    </div>
                </div>
                <div class="number-card">June 22</div>
            </div>
        </div>

        <div class="frame2">
            <div class="sec2">
                <div class="uppersec2">
                    <div class="text-topic">Upcoming Tours</div>
                    <button class="small-button">Done</button>
                </div>

                <div class="sec3-booking">
                    <div class="sec3-booking-main">
                        <div class="img-2">
                            <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
                        </div>
                    </div>

                    <div class="formSet">
                        <div class="formContent">
                            <div class="formText">Place :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Customer :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Date :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Time :</div>
                        </div>
                    </div>

                </div>
       
                <div class="uppersec2">
                    <div class="text-topic">Notes</div>
                    <button class="small-button">Add Notes</button>
                </div>

        <div class="whiteBox">
        </div>

        <div class="frame2">
            <div class="sec2">
                <div class="uppersec2">
                    <div class="text-topic">Previous Tours</div>
                    <button class="small-button">Done</button>
                </div>

                <div class="sec3-booking">
                    <div class="sec3-booking-main">
                        <div class="img-2">
                            <img src="<?php echo ROOT_DIR?>/assets/images/2.png" alt="">
                        </div>
                    </div>

                    <div class="formSet">
                        <div class="formContent">
                            <div class="formText">Place :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Customer :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Date :</div>
                        </div>
                        <div class="formContent">
                            <div class="formText">Time :</div>
                        </div>
                    </div>

                </div>
       
                <div class="uppersec2">
                    <div class="text-topic">Notes</div>
                    <button class="small-button">Add Notes</button>
                </div>

        <div class="whiteBox">
        </div>
        
    </div>
    </div>

    </div>

</body>

</html>
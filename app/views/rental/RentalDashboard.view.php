<div class="rent-dash">
    <div class="frame">
        <div class="edit-prof-button">
            <button type="submit" class="small-button-middle">
                Edit Profile
            </button>
        </div>

        <div class="div-1">
            <div class="div-12">
                <div class="text-wrapper">Hello Glaze Camping!</div>
                <div class="img-1">
                    <img src="../imgs/2.png" alt="">
                </div>
            </div>

            <form class="div-3">

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Name : <?php echo $user->name ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Location : Nuwara Eliya</div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Role : Rental Service</div>
                    </div>
                </div>

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Address : <?php echo $user->address ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Mobile No : <?php echo $user->mobile ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Registration No : <?php echo $user->regNo ?></div>
                    </div>
                </div>

            </form>

        </div>

    </div>



    <!-- Modal Box -->
    <div class="profile-editor" id="profile-editor">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="profile-info">
                <img src="<?php echo ROOT_DIR ?>/assets/images/dp.jpg" alt="Profile Image" class="profile-image">


                <form id="customer" action="<?= ROOT_DIR ?>/customer/update" method="post">
                    <h2>Update Customer Details</h2>
                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>

                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?= $user->address ?>" required>

                    <!-- <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $user->email ?>" required> -->

                    <label for="mobile">Number</label>
                    <input type="text" name="mobile" id="mobile" value="<?= $user->number ?>" required>

                    <label for="regNo">NIC Number</label>
                    <input type="text" name="regNo" id="regNo" value="<?= $user->nic ?>" required>

                    <!-- <label for="password">Password</label>
    <input type="password" name="password" id="password" required> -->

                    <input type="submit" name="submit" value="Update">
                </form>



            </div>
        </div>
    </div>
</div>

<!-- Modal Box -->

<div class="frame2">
    <div class="yellow-card">
        <div class="upper-card-text">
            <div class="text-card-topic">Total Bookings</div>
            <div class="edit-prof-button">
                <button type="submit" class="small-button-middle">
                    More &gt
                </button>
            </div>
        </div>
        <div class="number-card">10</div>
    </div>

    <div class="yellow-card">
        <div class="upper-card-text">
            <div class="text-card-topic">Bookings per month</div>
            <div class="edit-prof-button">
                <button type="submit" class="small-button-middle">
                    More &gt
                </button>
            </div>
        </div>
        <div class="number-card">03</div>
    </div>

    <div class="yellow-card">
        <div class="upper-card-text">
            <div class="text-card-topic">Bookings in last month</div>
            <div class="edit-prof-button">
                <button type="submit" class="small-button-middle">
                    More &gt
                </button>
            </div>
        </div>
        <div class="number-card">03</div>
    </div>
</div>>



<div class="frame">
    <div class="edit-prof-button">
        <button type="submit" class="small-button-middle">
            More &gt
        </button>
    </div>

    <div class="sec3-booking">
        <div class="sec3-booking-main">
            <div class="text-topic">Recent Booking</div>
            <div class="img-2">
                <img src="../imgs/2.png" alt="">
            </div>
        </div>

        <div class="div-5">
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Name : Kamal Silva</div>
            </div>
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Price : Rs.5000</div>
            </div>
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Date : 20/08/2023</div>
            </div>
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Time : 10:00</div>
            </div>

        </div>
    </div>
</div>



<div class="frame">
    <div class="edit-prof-button">
        <button type="submit" class="small-button-middle">
            More &gt
        </button>
    </div>

    <div class="text-topic">Booking History</div>

    <div class="div-6">
        <div class="div-wrapper-3">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Price</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>

                <tr>
                    <td>Kamal Silva</td>
                    <td>Upcoming</td>
                    <td>Rs.5000</td>
                    <td>02/12/2023</td>
                    <td>10.00</td>
                </tr>

                <tr>
                    <td>Kumara Perera</td>
                    <td>Upcoming</td>
                    <td>Rs.3000</td>
                    <td>02/12/2023</td>
                    <td>10.00</td>
                </tr>

                <tr>
                    <td>Sarath</td>
                    <td>Done</td>
                    <td>Rs.4000</td>
                    <td>01/09/2023</td>
                    <td>10.00</td>
                </tr>

            </table>
        </div>
    </div>
</div>



<script>
    var modal = document.getElementById("profile-editor");

    var span = document.getElementsByClassName("close")[0];

    // Get all view buttons
    var viewButton = document.getElementById('edit-profile');

    // Function to handle modal display
    function openModal() {
        // document.getElementById("modal-content").innerHTML = content;
        modal.style.display = "block";
    }

    // Add click event listener to view buttons
    viewButton.addEventListener('click', function() {

        // var name = this.parentElement.parentElement.querySelector('td:first-child').textContent;
        // var email = this.parentElement.parentElement.querySelector('td:nth-child(2)').textContent;
        openModal();
    });


    // Close the modal when the close button is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close the modal if the user clicks outside of it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php
require_once('../app/views/layout/footer.php');

?>
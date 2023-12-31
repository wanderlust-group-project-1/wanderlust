<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo ROOT_DIR ?>GuideDashboard.css" rel="stylesheet">
</head>

<body> -->

<?php
require_once('../app/views/layout/header.php');

require_once('../app/views/components/navbar.php');
?>
<div class="guide-dash">
    <div class="frame">
        <div class="edit-prof-button">
            <button type="submit" class="small-button-middle" id="edit-profile">
                Edit Profile
            </button>
        </div>

        <div class="div-1">
            <div class="div-12">
                <div class="text-wrapper">Hello <?php echo $user->name ?>!</div>
                <div class="img-1">
                    <img src="<?php echo ROOT_DIR ?>/assets/images/7.png" alt="">
                </div>
            </div>

            <form class="div-3">

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Name : <?php echo $user->name ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">NIC : <?php echo $user->nic ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Role : Guide</div>
                    </div>
                </div>

                <div class="div-4">
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Email : <?php echo $user->email ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Mobile : <?php echo $user->mobile ?></div>
                    </div>
                    <div class="div-wrapper">
                        <div class="text-wrapper-2">Address : <?php echo $user->address ?></div>
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
                <img src="<?php echo ROOT_DIR ?>/assets/images/7.png" alt="Profile Image" class="profile-image">


                <form id="guide" action="<?= ROOT_DIR ?>/guide/update" method="post">
                    <h2>Update Guide Details</h2>
                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>

                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" value="<?= $user->name ?>" required>

                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" value="<?= $user->address ?>" disabled>

                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" value="<?= $user->email ?>" required>

                    <label for="number">Number</label>
                    <input type="text" name="number" id="number" value="<?= $user->mobile ?>" required>

                    <label for="nic">NIC Number</label>
                    <input type="text" name="nic" id="nic" value="<?= $user->nic ?>" required>

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
            <div class="text-card-topic">Total Tours</div>
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
            <div class="text-card-topic">Tours per month</div>
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
            <div class="text-card-topic">Tours in last month</div>
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
                <img src="<?php echo ROOT_DIR ?>/assets/images/../imgs/2.png" alt="">
            </div>
        </div>

        <div class="div-5">
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Place : Nuwara Eliya</div>
            </div>
            <div class="div-wrapper-2">
                <div class="text-wrapper-2">Name : Kamal Silva</div>
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
                    <th>Place</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>

                <tr>
                    <td>Kamal Silva</td>
                    <td>Upcoming</td>
                    <td>Nuwara Eliya</td>
                    <td>02/12/2023</td>
                    <td>10.00</td>
                </tr>

                <tr>
                    <td>Kumara Perera</td>
                    <td>Upcoming</td>
                    <td>Kandy</td>
                    <td>02/12/2023</td>
                    <td>10.00</td>
                </tr>

                <tr>
                    <td>Sarath</td>
                    <td>Done</td>
                    <td>Nuwara Eliya</td>
                    <td>01/09/2023</td>
                    <td>10.00</td>
                </tr>

            </table>
        </div>
    </div>
</div>
</div>



<!-- <form>
                <div class="div-3">
                    <div class="div-4">
                        <div class="div-wrapper">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" value="Jenny Fernando">
                        </div>
                        <div class="div-wrapper">
                            <label for="age">Age:</label>
                            <input type="number" id="age" name="age" value="20">
                        </div>
                        <div class="div-wrapper">
                            <label for="role">Role:</label>
                            <select id="role" name="role">
                                <option value="Customer" selected>Customer</option>
                                <option value="Employee">Employee</option>
                                <option value="Manager">Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="div-4">
                        <div class="div-wrapper">
                            <label for="name2">Name:</label>
                            <input type="text" id="name2" name="name2" value="Jenny Fernando">
                        </div>
                        <div class="div-wrapper">
                            <label for="age2">Age:</label>
                            <input type="number" id="age2" name="age2" value="20">
                        </div>
                        <div class="div-wrapper">
                            <label for="role2">Role:</label>
                            <select id="role2" name="role2">
                                <option value="Customer" selected>Customer</option>
                                <option value="Employee">Employee</option>
                                <option value="Manager">Manager</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit">Submit</button>
            </form> -->


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
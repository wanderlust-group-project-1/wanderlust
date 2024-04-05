<?php
require_once('../app/views/layout/header.php');
?>

<div class="dashboard">
    <?php require_once('../app/views/guide/layout/guide-sidebar.php');
    ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">My Packages</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Packages</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Package 01</h3>
                    <h2>Rs.10 000</h2>

                </div>

                <div class="info-data mt-5">
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>10</h2>
                                <p>Maximum Group Size</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>20km</h2>
                                <p>Maximum Distance</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>Yes</h2>
                                <p>Transport Needed</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>Nuwara Eliya, Ella</h2>
                                <p>Places</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card rounded-4">
                    <button type="submit" class="btn-edit rounded-6 edit-package">
                        Edit Package
                    </button>
                </div>
            </div>
        </div>

        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Package 02</h3>
                    <h2>Rs.20 000</h2>

                </div>

                <div class="info-data mt-5">
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>20</h2>
                                <p>Maximum Group Size</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>30km</h2>
                                <p>Max Distance</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>No</h2>
                                <p>Transport Needed</p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="head">
                            <div>
                                <h2>Nuwara Eliya, Ella</h2>
                                <p>Places</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card rounded-4">
                    <button type="submit" class="btn-edit rounded-6 edit-package">
                        Edit Package
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-d-r-end">
            <button type="submit" class="btn mt-4" id="edit-profile">
                        Add New Package
            </button>
        </div>
    </div>
</div>


<!-- Modal Box Pacakge Edit -->
<div class="package-editor" id="package-editor" style="display: block;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="package-info">
      <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="Profile Image" class="profile-image">

      <form id="packages" action="<?= ROOT_DIR ?>/package/update/<?= $package['id'] ?>" method="post">
          <h2>Update Package Details</h2>
          <?php if (isset($errors)) : ?>
            <div> <?= implode('<br>', $errors) ?> </div>
          <?php endif; ?>

        <label for="price">Price</label>
        <input type="text" name="price" id="price" value="<?= $package['price'] ?>" required>

        <label for="max_group_size">Maximum Group Size</label>
        <input type="text" name="max_group_size" id="max_group_size" value="<?= $package['max_group_size'] ?>" required>

        <label for="max_distance">Maximum Distance</label>
        <input type="text" name="max_distance" id="max_distance" value="<?= $package['max_distance'] ?>" required>

        <label for="transport_needed">Transport Needed</label>
        <select name="transport_needed" id="transport_needed" required>
            <option value="1" <?= !$package['transport_needed'] ? 'selected' : '' ?>>Yes</option>
            <option value="0" <?= !$package['transport_needed'] ? 'selected' : '' ?>>No</option>
        </select>

        <label for="places">Places</label>
        <input type="text" name="places" id="places" value="<?= $package['places'] ?>" required>

        <input type="submit" class="btn mt-4" name="submit" value="Update">    
      </form>

    <!-- <span class="close">&times;</span> -->
    </div>
</div>
<!-- Modal Box Package Edit End -->

<script>
  var modal = document.getElementById("package-editor");

  var span = document.getElementsByClassName("close")[0];

  // Get all view buttons
  var viewButton = document.querySelector('.edit-package');

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

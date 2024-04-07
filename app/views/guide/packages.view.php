<?php
require_once('../app/views/layout/header.php');
?>

<div class="dashboard">
    <?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">My Packages</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Packages</a></li>
        </ul>

        <?php
        $packages = [
            ['name' => 'Package 01', 'price' => 'Rs.10 000', 'max_group_size' => '10', 'max_distance' => '20km', 'transport_needed' => 'Yes', 'places' => 'Nuwara Eliya, Ella'],
            ['name' => 'Package 02', 'price' => 'Rs.20 000', 'max_group_size' => '20', 'max_distance' => '30km', 'transport_needed' => 'No', 'places' => 'Nuwara Eliya, Ella']
        ];
        ?>

        <?php foreach ($packages as $package) : ?>
            <div data-id="<?= htmlspecialchars($package['id']) ?>" class="data">
                <div class="content-data">
                    <div class="head">
                        <h3><?= $package['name'] ?></h3>
                        <h2><?= $package['price'] ?></h2>
                    </div>

                    <div class="info-data mt-5">
                        <?php
                        $details = [
                            ['label' => 'Maximum Group Size', 'value' => $package['max_group_size']],
                            ['label' => 'Max Distance', 'value' => $package['max_distance']],
                            ['label' => 'Transport Needed', 'value' => $package['transport_needed']],
                            ['label' => 'Places', 'value' => $package['places']]
                        ];
                        ?>

                        <?php foreach ($details as $detail) : ?>
                            <div class="card">
                                <div class="head">
                                    <div>
                                        <h2><?= $detail['value'] ?></h2>
                                        <p><?= $detail['label'] ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>
                    <div class="card rounded-4">
                        <button type="submit" class="btn-edit rounded-6 edit-package">
                            View Package
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex-d-r-end">
            <button type="submit" class="btn mt-4" id="edit-profile">
                Add New Package
            </button>
        </div>
    </div>
</div>

<!-- Modal for Package Details -->
<div id="view-package-modal" class="view-package-modal">
    <div class="modal-content" id="package-modal-content">
        <span class="close-button">&times;</span>
        <h2>Package Details</h2>
        <p><strong>Name:</strong> <span id="detail-name"></span></p>
        <p><strong>Price:</strong> <span id="detail-price"></span></p>
        <p><strong>Maximum Group Size:</strong> <span id="detail-max-group-size"></span></p>
        <p><strong>Maximum Distance:</strong> <span id="detail-max-distance"></span></p>
        <p><strong>Transport Needed:</strong> <span id="detail-transport-needed"></span></p>
        <p><strong>Places:</strong> <span id="detail-places"></span></p>
    </div>
</div>


<script>
    function viewPackage() {
        console.log('view package');

        var modal = document.getElementById("view-package-modal");
        var closeButton = document.querySelector(".close-button");

        closeButton.addEventListener("click", function() {
            modal.style.display = "none";
        });

        var viewButtons = document.querySelectorAll("#edit-profile");
        viewButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                console.log('view button clicked');
                var packageId = button.getAttribute('data-id');
                fetchPackageDetails(packageId);
                modal.style.display = "block";
            });
        });
    }

    function fetchPackageDetails(packageId) {
        $.ajax({
            headers: {
                Authorization: "Bearer " + getCookie('jwt_auth_token')
            },
            url: '<?= ROOT_DIR ?>/package/getPackage/' + packageId,
            method: 'GET',
            success: function(data) {
                var newDiv = document.createElement("div");
                newDiv.innerHTML = data;
                var js = newDiv.querySelector('script').innerHTML;

                $('#package-modal-content').html(data).promise().done(function() {
                    console.log('package loaded');
                    viewPackage();
                    eval(js);
                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    $(document).ready(function() {
        $('.show-filter').click(function() {
            $('.table-filter').slideDown();
            $(this).hide();
        });

        $('.hide-filter').click(function() {
            $('.table-filter').slideUp();
            $('.show-filter').show();
        });

        $('#package-name-filter').on('input', debounce(filterPackages, 300));

        $('#package-price-filter-min').on('input', function() {
            filterPackages();
        });

        $('#package-price-filter-max').on('input', function() {
            filterPackages();
        });

        function filterPackages() {
            var name = $('#package-name-filter').val();
            var minPrice = $('#package-price-filter-min').val();
            var maxPrice = $('#package-price-filter-max').val();

            $('.package-item').each(function() {
                var packageItem = $(this);
                var packageName = packageItem.find('.package-name').text();
                var packagePrice = parseFloat(packageItem.find('.package-price').text().replace('Rs', '').trim());

                if ((name && packageName.toLowerCase().indexOf(name.toLowerCase()) === -1) || (minPrice && packagePrice < minPrice) || (maxPrice && packagePrice > maxPrice)) {
                    packageItem.hide();
                } else {
                    packageItem.show();
                }
            });
        }
    });
</script>



<!-- Modal Box Package Edit End -->

<!-- <script>
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
</script> -->
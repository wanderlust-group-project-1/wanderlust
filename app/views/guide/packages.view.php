<?php
require_once('../app/views/layout/header.php');
?>

<?php require_once('../app/views/guide/layout/guide-sidebar.php'); ?>

<div class="dashboard">

    <div class="sidebar-flow"></div>

    <div class="guide-dash-main flex-d-c">
        <h1 class="title mb-2">My Packages</h1>
        <ul class="breadcrumbs">
            <li><a href="<?= ROOT_DIR ?>/home">Home</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">My Packages</a></li>
        </ul>

        <div class="package-details">
        </div>

        <div class="flex-d-r-end">
            <button type="submit" class="btn mt-4" id="add-package">
                Add New Package
            </button>
        </div>
    </div>

    <!--modal box add package-->

    <div class="modal" id="add-package-modal" style="display: none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <form id="add-package-form" class="form" method="POST" enctype="multipart/form-data">
                <h2>Add New Package</h2>

                <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">

                    <label for="price">Price</label>
                    <input type="text" id="price" class="form-control-lg" name="price" required>

                    <label for="max_group_size">Maximum Group Size</label>
                    <input type="number" id="max_group_size" class="form-control-lg" name="max_group_size" required>


                    <label for="max_distance">Maximum Distance</label>
                    <input type="number" id="max_distance" class="form-control-lg" name="max_distance" required>

                    <label for="transport_needed">Transport Needed</label>
                    <input type="checkbox" id="transport_needed" class="form-control-lg" name="transport_needed">

                    <label for="places">Places</label>
                    <textarea type="text" id="places" class="form-control-lg" name="places" required></textarea>
                </div>

                <div class="row">
                    <input type="submit" class="btn" value="Add Package">
                </div>
            </form>
        </div>
    </div>

</div>



<script>
    // Add new package modal
    var addPackageModal = document.getElementById("add-package-modal");
    var addPackageBtn = document.getElementById("add-package");
    var closeButton = document.querySelector(".close-button");


    addPackageBtn.onclick = function() {
        addPackageModal.style.display = "block";
    }

    closeButton.onclick = function() {
        addPackageModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == addPackageModal) {
            addPackageModal.style.display = "none";
        }
    }
</script>

<script>
    // Add new package form submission
    $(document).ready(function() {
        $("#add-package-form").trigger('reset');
        $('#add-package-form').submit(function(e) {
            e.preventDefault();

            var formData = new FormData();
            var transportNeeded = $('#transport_needed').is(':checked') ? 1 : 0

            var jsonData = {
                price: $('#price').val(),
                max_group_size: $('#max_group_size').val(),
                max_distance: $('#max_distance').val(),
                transport_needed: transportNeeded,
                places: $('#places').val()
            };
            console.log(transportNeeded)
            formData.append('json', JSON.stringify(jsonData));

            console.log(formData);
            console.log(jsonData);

            $.ajax({
                headers: {
                    'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/api/package/addPackage',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        alertmsg('Package added successfully', 'success');

                        addPackageModal.style.display = "none";
                        getPackages();
                        $("#add-package-form").trigger('reset');
                }

                
            },
            error: function(errors) {
                    console.log(errors);
            
                }

            });
        });
    });
</script>

<script>
    function getPackage() {

        $.ajax({
            url: '<?= ROOT_DIR ?>/guide/packages',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            },
            success: function(data) {

            }
        })
    }
</script>

<script>
    // get equipment list using ajax , get content and append to equipment list div

    function getPackages() {
        // use Authorization header to get data

        $.ajax({
            url: '<?= ROOT_DIR ?>/Guide/getPackages',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + getCookie('jwt_auth_token')
            },
            success: function(data) {
                // console.log(data);
                // Update the modal content with the fetched data
                // empty the equipment list and append new data
                $(".package-details").empty();
                $(".package-details").html(data).promise().done(function() {
                    console.log('Package list updated');
                    viewPackage();
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data: " + error);
                // Handle errors here
            }
        });
    }
    getPackages();
</script>

<script>
    function viewPackage() {
        console.log('view package');

        var modal = document.getElementById("view-package-modal");
        var closeButton = document.querySelector(".close-button");

        closeButton.addEventListener("click", function() {
            modal.style.display = "none";
        });

        var viewButtons = document.querySelectorAll("#edit-package");
        viewButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                console.log('view button clicked');
                var row = button.closest('.data');
                var packageId = row.getAttribute('data-id');
                fetchPackageDetails(packageId);
                
                // var row = button.closest('.tr');
                // var packageId = button.getAttribute('data-id');
                // fetchPackageDetails(packageId);
                // modal.style.display = "block";
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
                // var newDiv = document.createElement("div");
                // newDiv.innerHTML = data;
                // var js = newDiv.querySelector('script').innerHTML;

                $('#package-modal-content').empty();
                $('#package-modal-content').html(data).promise().done(function() {
                    console.log('package loaded');
                    // viewPackage();
                    // eval(js);
                });
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    // function filterPackages(name, type) {
    //     $('.data').each(function() {
    //         var package = $(this);
    //         var packageName = package.find('.head h3').text();
    //         var packageType = package.find('.info-data').text();

    //         if ((name && packageName.toLowerCase().indexOf(name.toLowerCase()) === -1) ||
    //             (type && packageType.toLowerCase().indexOf(type.toLowerCase()) === -1)) {
    //             package.hide();
    //         } else {
    //             package.show();
    //         }
    //     });
    // }

    // $(document).ready(function() {
    //     $('.show-filter').click(function() {
    //         $('.table-filter').slideDown();
    //         $('show-filter').hide();
    //     });

    //     $('.hide-filter').click(function() {
    //         $('.table-filter').slideUp();
    //         $('.show-filter').show();
    //     });

    //     $('#package-name-filter').on('input', debounce(filterPackages, 300));



    //     $('#package-price-filter-min').on('input', function() {
    //         filterPackages();
    //     });

    //     $('#package-price-filter-max').on('input', function() {
    //         filterPackages();
    //     });

    //     function filterPackages() {
    //         var name = $('#package-name-filter').val();
    //         var minPrice = parseFloat($('#package-price-filter-min').val());
    //         var maxPrice = parseFloat($('#package-price-filter-max').val());

    //         $('.data').each(function() {
    //             var packageItem = $(this);
    //             var packageName = packageItem.find('.head h3').text();
    //             var packagePriceText = packageItem.find('.head h2').text();
    //             var packagePrice = parseFloat(packagePriceText.replace('Rs.', '').trim());

    //             // Assuming the package name and price should always be present for filtering
    //             if ((name && packageName.toLowerCase().indexOf(name.toLowerCase()) === -1) ||
    //                 (minPrice && packagePrice < minPrice) || (maxPrice && packagePrice > maxPrice)) {
    //                 packageItem.hide();
    //             } else {
    //                 packageItem.show();
    //             }
    //         });
    //     }
    // });
</script>
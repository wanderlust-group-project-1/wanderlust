<?php if (isset($_SESSION['USER']) && is_object($_SESSION['USER']) &&    $_SESSION['USER']->role != 'admin') {
  $user = $_SESSION['USER'];
}
?>

<div class="toggle-button" onclick="toggleSidebar()">☰</div>


<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="guide-dash-prof  mt-3 justify-content-center">
    <div class="details flex-d-c justify-content-center">

      <div class="user-image">
        <img src="<?php echo ROOT_DIR ?>/uploads/images/rental_services/<?php echo $user->image; ?>" alt="Profile Image" class="profile-image">
      </div>
    </div>

    <div class="options flex-d-c">
      <h2 class="name"> <?php echo $user->name; ?></h2>
      <!-- <p class="email"> <?php echo $user->email; ?></p> -->
      <!-- <p class="number"> <?php echo $user->mobile; ?></p> -->
    </div>

    <div class="">
      <button type="submit" class="btn-edit mt-4" id="edit-profile">
        Edit Profile
      </button>
    </div>
  </div>

  <ul class="nav">

    <li data-id="dashboard" class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/dashboard">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li data-id="equipments" class="nav-item">

      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/equipments" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Equipments</span>
        <i class="menu-arrow"></i>
      </a>
      
    </li>

    <li data-id="orders" class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/orders" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Orders</span>
        <i class="menu-arrow"></i>
      </a>

    </li>



    <li data-id="complaints" class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/complaints" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Complaints</span>
        <i class="menu-arrow"></i>
      </a>

    </li>

    <!-- <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/customers" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Customers</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li> -->



    <!-- <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/tips">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Tips and Know-hows</span>
      </a>
    </li> -->

    <!-- <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/rentalServices/item" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Items</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="ui-basic">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/buttons.html">Buttons</a></li>
          <li class="nav-item"> <a class="nav-link" href="pages/ui-features/typography.html">Typography</a></li>
        </ul>
      </div>
    </li> -->
    <!-- 
    <li class="nav-item">
      <a class="nav-link" href="<?php echo ROOT_DIR ?>/blogs">
        <i class="ti-shield menu-icon"></i>
        <span class="menu-title">Complaints</span>
      </a>
    </li> -->

    <li data-id="statistics" class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="statistics" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Statistics</span>
        <i class="menu-arrow"></i>
      </a>

    </li>
    <!-- Settings -->
    <li data-id="settings" class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/settings" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Settings</span>
        <i class="menu-arrow"></i>
      </a>

    </li>


    <li data-id="logout" class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="<?php echo ROOT_DIR ?>/logout" aria-expanded="false" aria-controls="ui-basic">
        <i class="ti-palette menu-icon"></i>
        <span class="menu-title">Logout</span>
        <i class="menu-arrow"></i>
      </a>

    </li>



  </ul>

</nav>


<script>
  function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.classList.toggle("active");
  }

  // function toggleSidebar() {
  //     var sidebar = document.getElementById("sidebar");
  //     sidebar.classList.toggle("sidebar-offcanvas");
  // }
</script>



    <!-- Modal Box Profile Edit -->
    <div class="profile-editor" id="profile-editor">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="profile-info">
                <!-- <img src="<?php echo ROOT_DIR ?>/assets/images/2.png" alt="Profile Image" class="profile-image"> -->
                <!-- Image with hover camera icon on image -->
                <div class="profile-image mh-400px">

                <div class="profile-image-overlay">
                    <img src="<?php echo ROOT_DIR ?>/uploads/images/rental_services/<?php echo $user->image; ?>" alt="Profile Image" class="profile-image mh-200px" id = "profile-image">
                    <div class="camera-icon">
                        <i class="fa fa-camera" aria-hidden="true"></i>
                    </div>
                    </div>


                  
                </div>

              


                <form id="rentalservice" class="text-center" action="<?= ROOT_DIR ?>/rentalService/update" method="post">
                    <h2>Update Profile</h2>
                    <?php if (isset($errors)) : ?>
                        <div> <?= implode('<br>', $errors) ?> </div>
                    <?php endif; ?>
                    <div class="profile-update-element">
                      <label for="name">Name</label>
                    </div>
                    <div class="profile-update-element">
                      <input type="text" name="name" id="name" value="<?= $user->name ?>" required>
                    </div>
                    <div class="profile-update-element">
                      <label for="address">Address</label>
                    </div>
                    <div class="profile-update-element">
                      <input type="text" name="address" id="address" value="<?= $user->address ?>" required>
                    </div>

                    <!-- <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?= $user->email ?>" required> -->

                    <div class="profile-update-element">
                      <label for="mobile">Mobile No</label>
                    </div>
                    <div class="profile-update-element">
                      <input type="text" name="mobile" id="mobile" value="<?= $user->mobile ?>" required>
                    </div>
                    <div class="profile-update-element">
                      <label for="regNo">Registration Number</label>
                    </div>
                    <div class="profile-update-element">
                      <input type="text" name="regNo" id="regNo" value="<?= $user->regNo ?>" required>
                    </div>

                    <!-- <label for="password">Password</label>
    <input type="password" name="password" id="password" required> -->

    <div class="flex-d align-items-center justify-content-center">
                    <!-- <input type="submit" class="btn-text-green border mt-4" name="submit" value="Update"> -->
                    <button type="submit" class="btn-text-green border mt-4" name="submit" > <i class="fas fa-edit"></i>Update</button>
    </div>
                </form>



            </div>
        </div>
    </div>


    <!-- image Upload modal -->

    <div class="modal" id="image-upload">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Upload Image</h2>
            <!-- <form action="<?= ROOT_DIR ?>/rentalService/uploadImage" method="post" enctype="multipart/form-data">
                <input type="file" name="image" id="image" required>
                <input type="submit" class="btn mt-4" name="submit" value="Upload">
            </form> -->
            <!-- With image preview -->
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="image" id="profile-image-input" class="form-control-lg"  accept="image/png, image/jpg, image/gif, image/jpeg , image/webp" required>
                <div class="image-preview-container flex-d-c align-items-center">
                    
                    
                <img src="<?php echo ROOT_DIR ?>/uploads/images/rental_services/<?php echo $user->image; ?>" alt="" id="image-preview" class="image-preview">
                </div>
                <div class="flex-d align-items-center justify-content-center">

                <!-- <input type="submit" class="btn-text-green border mt-4" name="submit" value="Upload">  -->
                <button type="submit" class="btn-text-green border mt-4" name="submit" > <i class="fa fa-upload"></i>Upload</button>

                </div>
            </form>


        </div>
    </div>

    <!-- preview style -->

    <style>



        

        
    
    </style>


    <!-- Jquery open image upload modal -->

    <script>
        $(document).ready(function() {
            $('.profile-image').click(function() {
                $('#image-upload').css('display', 'block');
            });
        });

        // image preview jquery
        $(document).ready(function() {
            $('#profile-image-input').change(function() {
                var reader = new FileReader();

                // file type validation
                if (!/image\/\w+/.test(this.files[0].type)) {
                    alertmsg('File type not supported','error');
                    // clear file input
                    console.log('File type not supported');
                    $('#profile-image-input').val('');


                    return;
                }




                reader.onload = function(e) {
                    $('#image-preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            });
        });

        // upload image  using ajax
        $(document).ready(function() {
            $('#image-upload form').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    headers: {
                      'Authorization': 'Bearer ' +  getCookie('jwt_auth_token')
                    },
                    url:"<?= ROOT_DIR ?>/api/rentalService/uploadImage",
                    type: 'POST',
                    data: formData,
                    success: function(data) {
                        alertmsg('Image uploaded successfully','success');
                        $('#image-upload').css('display', 'none');

                        $('.profile-image').attr('src', '<?= ROOT_DIR ?>/uploads/images/rental_services/' + data.data.image);
                        // $('#profile-image-input').val('');
                        // $('#image-preview').attr('src', '');
                        // location.reload();
                    },
                    error: function(data) {
                        alertmsg('Image upload failed','error');
                        console.log(data);
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });
        });





    
    </script>



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


<script>
  // sidebar selected nav item

  // get current url and split it

  $(document).ready(function() {
    var url = window.location.href;
    var urlSplit = url.split('/');
    var page = urlSplit[urlSplit.length - 1];
    console.log(page);
    var navItems = document.querySelectorAll('.nav-item');

    navItems.forEach(function(item) {
      if (item.getAttribute('data-id') == page) {
        item.classList.add('active');
      }
    });
  });



</script>
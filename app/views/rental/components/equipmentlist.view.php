<div class="table-container flex-d-c">

    <!-- Table filter for each column -->
    <!-- button for show filter -->
    <div class="filter-btn">
        <button id="show-filter" class="btn-icon" aria-expanded="false"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
    </div>


    <div class="table-filter ">
        <div class="row">
            <div class="back-btn">
                <button id="hide-filter" class="btn-icon" aria-expanded="true"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
            </div>
        </div>
        <div class=" gap-3 flex-d-c">   
            <div class="row">
                <div class="col-lg-5 col-md-12 mw-300px">
                    <input type="text" class="form-control-lg" id="equipment-name-filter" placeholder="Search by Equipment Name">
                </div>

                <!-- Select type -->

                <div class="col-lg-5  col-md-12  mw-300px">
                    <select id="equipment-type-filter" class="form-control-lg">
                        <option value="">All Types</option>
                        <option value="tent">Tent</option>
                        <option value="cooking">Cooking</option>
                        <option value="backpack">Backpack</option>
                        <option value="sleeping">Sleeping</option>
                        <option value="clothing">Clothing</option>
                        <option value="footwear">Footwear</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <table class="data-table table-custom" id="equipment-table">
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Type</th>
                <th>Cost</th>
                <th>Quantity</th>
                <th>Action</th> <!-- Added Action Column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr data-id="<?= htmlspecialchars($equipment->id) ?>">
                    <td data-label="Equipment Name"><?= htmlspecialchars($equipment->name) ?></td>
                    <td data-label="Type"><?= htmlspecialchars($equipment->type) ?></td>
                    <td data-label="Cost">Rs<?= htmlspecialchars($equipment->fee) ?></td>
                    <td data-label="Count"><?= htmlspecialchars($equipment->count) ?></td>
                    <td data-label="Action"><button id="equipment-view-button" class="btn-text-green"><i class="fa fa-list" aria-hidden="true"></i> View</button></td> <!-- View Button for each row -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- modal for view button -->

<div id="view-equipment-modal" class="view-equipment-modal">
    <div class="modal-content" id="equipment-modal-content">
        <span class="close-button">&times;</span>
        <h2>Equipment Details</h2>
        <p><strong>Name:</strong> <span id="detail-name"></span></p>
        <p><strong>Type:</strong> <span id="detail-type"></span></p>
        <p><strong>Cost:</strong> <span id="detail-cost"></span></p>
        <p><strong>Rental Fee:</strong> <span id="detail-rental-fee"></span></p>
        <p><strong>Description:</strong> <span id="detail-description"></span></p>
        <p><strong>Count:</strong> <span id="detail-count"></span></p>
        <img id="detail-image" src="" alt="Equipment Image" style="max-width:100%;height:auto;">
    </div>
</div>





<script>
    function viewEquipment(){
        console.log('view equipment');

    var modal = document.getElementById("view-equipment-modal");
    var closeButton = document.querySelector(".close-button");

    closeButton.addEventListener("click", function() {
        modal.style.display = "none";
    });

    var viewButtons = document.querySelectorAll("#equipment-view-button");
    // console.log("a",viewButtons);
    viewButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            console.log('view button clicked');
            var row = button.closest('tr');

            var id = row.getAttribute('data-id');


            fetchEquipmentDetails(id);


            
            


            modal.style.display = "block";
        });
    });

    }

    function fetchEquipmentDetails(equipmentId) {
    $.ajax({
        headers: {
            Authorization: "Bearer " + getCookie('jwt_auth_token')
        },
        url: '<?= ROOT_DIR ?>/rentalService/getequipment/' + equipmentId,
        method: 'GET',
        success: function(data) {
            // console.log(data);

            // Create a new div element
            var newDiv = document.createElement("div");
            newDiv.innerHTML = data;
            var js = newDiv.querySelector('script').innerHTML;

            // Update the modal content and execute the script
            $('#equipment-modal-content').empty();
            $('#equipment-modal-content').html(data).promise().done(function() {
                console.log('equipment loaded');
                viewEquipment();
                eval(js);
            });
        },
        error: function(err) {
            console.log(err);
        }
    });
}


// filter equipment

 $(document).ready(function() {
    $('#show-filter').click(function() {
        $('.table-filter').slideDown();
        $('#show-filter').hide();
    });

    $('#hide-filter').click(function() {
        $('.table-filter').slideUp();
        $('#show-filter').show();
    });

    // client side filter (onchange)

    $('#equipment-name-filter').on('input', debounce(filterEquipment, 300));

    $('#equipment-type-filter').change(function() {
        filterEquipment();
    });

    $('#equipment-cost-filter-min').on('input', function() {
        filterEquipment();
    });

    $('#equipment-cost-filter-max').on('input', function() {
        filterEquipment();
    });




    // $('#equipment-filter-button').click(function() {
        function filterEquipment() {
        var name = $('#equipment-name-filter').val();
        var type = $('#equipment-type-filter').val();
        // var minCost = $('#equipment-cost-filter-min').val();
        // var maxCost = $('#equipment-cost-filter-max').val();

        // console.log(name, type, minCost, maxCost);

     
        $('#equipment-table tbody tr').each(function() {
            var row = $(this);
            var equipmentName = row.find('td').eq(0).text();
            var equipmentType = row.find('td').eq(1).text();
            // var equipmentCost = row.find('td').eq(2).text().replace('Rs', '');
            var equipmentCount = row.find('td').eq(3).text();

            // console.log(equipmentName, equipmentType, equipmentCost, equipmentCount);

            if (name && equipmentName.toLowerCase().indexOf(name.toLowerCase()) === -1) {
                row.hide();
            } else if (type && equipmentType.toLowerCase().indexOf(type.toLowerCase()) === -1) {
                row.hide();
            } else {
                row.show();
            }
        });
    }
});






</script>








<style>
  

</style>
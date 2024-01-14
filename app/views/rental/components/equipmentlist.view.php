<div class="table-container">

    <table class="data-table">
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Type</th>
                <th>Cost</th>
                <th>Count</th>
                <th>Action</th> <!-- Added Action Column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipments as $equipment): ?>
                <tr data-id="<?= htmlspecialchars($equipment->id) ?>">
                    <td data-label="Equipment Name"><?= htmlspecialchars($equipment->name) ?></td>
                    <td data-label="Type"><?= htmlspecialchars($equipment->type) ?></td>
                    <td data-label="Cost">$<?= htmlspecialchars($equipment->fee) ?></td>
                    <td data-label="Count"><?= htmlspecialchars($equipment->count) ?></td>
                    <td data-label="Action"><button class="equipment-view-button">View</button></td> <!-- View Button for each row -->
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
        <p><strong>Fee:</strong> <span id="detail-fee"></span></p>
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

    var viewButtons = document.querySelectorAll(".equipment-view-button");
    // console.log("a",viewButtons);
    viewButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            console.log('view button clicked');
            var row = button.closest('tr');

            var id = row.getAttribute('data-id');

            $.ajax({
                headers:{
                    Authorization: "Bearer " + getCookie('jwt_auth_token')
                },
                url: '<?= ROOT_DIR ?>/rentalService/getequipment/' + id,
                method: 'GET',
                success: function(data) {
                    console.log(data);
                    
                    //  create a new div element
                    var newDiv = document.createElement("div")
                    newDiv.innerHTML = data;
                    var js = newDiv.querySelector('script').innerHTML;
                    



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
            
            


            modal.style.display = "block";
        });
    });

    }


</script>








<style>
    /* Base styling for the modal */
.view-equipment-modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0,0,0,0.4); /* Black with opacity */
}

.view-equipment-modal .modal-content {
    position: relative;
    background-color: #fefefe;
    margin: 10% auto; /* 10% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 50%; /* Width in desktop */
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    animation: fadeIn 0.5s;
}

.view-equipment-modal .close-button {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.view-equipment-modal .equipment-details img 
{
    max-width: 300px;
    height: auto;
}

.close-button:hover,
.close-button:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.edit-equipment-button{
    background-color: #4CAF50; /* Green */
    width:90%;
    border: none;
    color: white;
    height: 30px;

    padding: 5px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 12px;

    margin: auto;

}


.delete-equipment-button {
    background-color: #f44336; /* Red */
    width:90%;
    border: none;
    height: 30px;

    color: white;
    padding: 5px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 12px;


}

/* Animation for the modal */
@keyframes fadeIn {
    from {opacity: 0;} 
    to {opacity: 1;}
}

/* Mobile responsive styling */
@media screen and (max-width: 600px) {
    .view-equipment-modal .modal-content {
        width: 80%; /* Wider in mobile */
        margin: 20% auto; /* More margin from the top in mobile */
    }
}

</style>
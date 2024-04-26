<div class="table-container flex-d-c">

    <!-- Table filter for each column -->
    <!-- button for show filter -->
    <div class="filter-btn mb-3">
        <button id="show-filter" class="btn-text-green border" aria-expanded="false"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
    </div>


    <div class="table-filter ">
        <div class="row ">
            <div class="back-btn mb-3">
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

                <!-- <div class="col-lg-5 col-md-12 mw-300px">
                    <checkbox id="equipment-count-filter" class="form-control-lg" placeholder="Search by Count">
                        <label for="equipment-count-filter">Show Disabled Items</label>
                </div> -->
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
                    <td data-label="Cost">Rs<?= htmlspecialchars($equipment->cost) ?></td>
                    <td data-label="Count"><?= htmlspecialchars($equipment->count) ?></td>
                    <td data-label="Action"><button id="equipment-view-button" class="btn-text-green"><i class="fa fa-list" aria-hidden="true"></i> View</button></td> <!-- View Button for each row -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- modal for view button -->

<div id="view-equipment-modal" class="view-equipment-modal modal">
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













<style>
  

</style>
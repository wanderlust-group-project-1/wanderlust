<!-- <?php
foreach ($data["schedule"] as $schedule){
?>
<div class="schedule-details" id="schedule-details-<?php echo htmlspecialchars($schedule->id); ?>" data-id="<?php echo htmlspecialchars($schedule->id); ?>">
    <span class="close">&times;</span>
    <div class="container flex-d-c gap-4 p-md-0 ">
        <h2>Schedule Details</h2>
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <table class="table-details">
                    <tr>
                        <td><strong>Day:</strong></td>
                        <td><?php echo htmlspecialchars($schedule->day); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Start Time:</strong></td>
                        <td><?php echo htmlspecialchars($schedule->start_time); ?></td>
                    </tr>
                    <tr>
                        <td><strong>End Time:</strong></td>
                        <td><?php echo htmlspecialchars($schedule->end_time); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="edit-button">
            <button class="edit-schedule-button btn btn-full m-1" data-id="<?php echo htmlspecialchars($schedule->id); ?>">Edit</button>
        </div>
        <div class="delete-button">
            <button class="delete-schedule-button btn btn-danger btn-full m-1" data-id="<?php echo htmlspecialchars($schedule->id); ?>">Delete</button>
        </div>
    </div>
</div>
<?php
}
?> -->

<!--modal box edit schedule-->

<div class="modal edit-schedule-modal" id="edit-schedule-modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <form id="update-schedule-form" scheduleId="<?php echo htmlspecialchars($schedule->id);?>" class="form" method="POST" enctype="multipart/form-data">
            <h2>Update Schedule</h2>
            <div class="col-lg-5 col-md-12 p-2 flex-d-c gap-2">
                <div class="form-group">
                    <label for="day">Day</label>
                    <input type="text" name="day" id="day" class="form-control" value="<?php echo htmlspecialchars($schedule->day); ?>" required>

                    <label for="start_time">Start Time</label>
                    <input type="text" name="start_time" id="start_time" class="form-control" value="<?php echo htmlspecialchars($schedule->start_time); ?>" required>

                    <label for="end_time">End Time</label>
                    <input type="text" name="end_time" id="end_time" class="form-control" value="<?php echo htmlspecialchars($schedule->end_time); ?>" required>
                </div>

            <div class="row">
                <input type="submit" class="btn mt-4" name="submit" value="Update">
            </div>
        </form>
    </div>
</div>


<div id="delete-schedule-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Delete Schedule</h2>
        <p>Are you sure you want to delete this schedule?</p>
        <div class="flex-d gap-2 mt-5">
            <button class="delete-schedule-confirm btn btn-danger m-1" data-id="<?php echo htmlspecialchars($schedule->id); ?>">Delete</button>
            <button class="close-button btn btn-full m-1">Cancel</button>
        </div>
    </div>
</div>


<style>
    .delete-schedule-modal {
        display: none;
        position: fixed;
        z-index: 200;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.4);
        /* Unified background color */
        /* padding-top: 60px; */
    }

    .edit-schedule-modal {
        display: none;
        position: fixed;
        z-index: 200;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.4);
    }
</style>
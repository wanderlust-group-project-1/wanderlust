<?php
require_once('../app/views/admin/layout/header.php');
require_once('../app/views/admin/components/navbar.php');
require_once('../app/views/admin/layout/sidebar.php');
// show($guides);
?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Mobile</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Assuming guides is an array of data
            foreach ($guides as $guide) {
            ?>
                <tr key="<?php echo $guide->id ?>">
                    <td><?php echo $guide->name; ?></td>
                    <td><?php echo $guide->mobile; ?></td>
                    <td><span class="status <?php echo $guide->status; ?>"><?php echo $guide->status; ?></span></td>
                    <td><button class="view-button">View</button></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Rest of the code remains unchanged -->

<script>
    // Update the modal content with the fetched data
    $.get(`<?php echo ROOT_DIR?>/admin/guides/viewUser/${id}`, function(data) {
        $("#user").html(data);
    });
    // ... (rest of the script remains unchanged)
</script>

<?php
require_once('../app/views/admin/layout/footer.php');
?>

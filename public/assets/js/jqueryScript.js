$(document).on('click', '.close', function() {
    var modal = $(this).closest('.modal');

    modal.hide();
});
// close or modal-close both 

$(document).on('click', '.modal-close', function() {
    var modal = $(this).closest('.modal');
    modal.hide();
});

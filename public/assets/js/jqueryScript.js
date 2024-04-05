$(document).on('click', '.close', function() {
    var modal = $(this).closest('.modal');
    console.log("modal-close");


    modal.hide();
});
// close or modal-close both 

$(document).on('click', '.modal-close', function() {
    var modal = $(this).closest('.modal');
    console.log("modal-close");

    modal.hide();
});


// close when click outside of modal
// $(document).on('click', function(event) {
//     console.log("click outside");
//     if ($(event.target).is('.modal')) {
//         $('.modal').hide();
//     }
// });


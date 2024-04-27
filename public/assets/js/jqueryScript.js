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



$(document).on('click', '.modal', function(event) {
    console.log("click outside");
    if ($(event.target).is('.modal')) {
        $(event.target).hide();
    }
});

// close when click outside of modal
// $(document).on('click', function(event) {
//     console.log("click outside");
//     if ($(event.target).is('.modal')) {
//         $('.modal').hide();
//     }
// });



        function showLoader() {
            $('#loading-overlay').css('visibility', 'visible');
        }

        function hideLoader() {
            $('#loading-overlay').css('visibility', 'hidden');
        }




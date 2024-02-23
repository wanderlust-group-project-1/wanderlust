$(document).ready(function() {

    console.log('Tooltip script loaded');

    // <div id="tooltip" style="display: none; position: absolute; padding: 10px; background: black; color: white; border-radius: 5px;">Tooltip text!</div>

    // Add a tooltip element to the body
    $('body').append('<div id="tooltip-element" style="display: none; position: absolute; padding: 10px; background: black; color: white; border-radius: 5px; z-index: 1000;"></div>');

    // Add a mouseenter and mouseleave event to all elements with the tooltip class


    // $('.tooltip').mouseenter(function(e) {

    $(document).on('mouseenter', '.tooltip', function(e) {
        
        // Position the tooltip based on the hover target's position
        console.log('Hovered over tooltip element');
        var tooltip = $('#tooltip-element');
        var targetPosition = $(this).offset();
        var text = $(this).data('tooltip');

        // Set the tooltip text
        tooltip.text(text);

        
        tooltip.css({
            display: 'block',
            // top: targetPosition.top + $(this).outerHeight(), // Position below the target element
            top: targetPosition.top - 35,
            left: targetPosition.left

        });

        // Hide the tooltip after 5 seconds
        setTimeout(hideTooltip, 4000);
    // }).mouseleave(function() {
    }).on('mouseleave', '.tooltip', function() {
        // Hide the tooltip when the mouse leaves

        $('#tooltip-element').css('display', 'none');
    });
});

// set timeout to hide the tooltip after 5 seconds

function hideTooltip() {
    $('#tooltip-element').css('display', 'none');
}

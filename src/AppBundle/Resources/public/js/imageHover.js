$(document).ready(function () {
    $('.homePageImage').bind('mouseover', function () {
        var text = $(this).attr('alt');
        $(this).fadeTo('fast', '.1');
        $(this).parent().parent().find('.imageOverlay').show().addClass('removemargin');
    }).bind('mouseout', function () {
        $(this).fadeTo('fast', 1)
    });
});

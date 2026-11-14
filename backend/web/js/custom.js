$(window).ready(function () {

    $('.stash_category').on('change', function() {
        var id = $(this).attr('id');
        var val = $(this).attr('value');
        var text = $('label[for="'+id+'"]').text();

        // $('ul[data-parent_id="'+val+'"] input:checkbox').prop('checked', $(this).prop("checked"));

        if ($(this).is(':checked')) {

            $('.stash_category_container').append('<span class="'+id+'">- '+text+'<br></span>');

        } else {

            $('.'+id).remove();

        }
    })

});
$(document).ready(function() {
    $('.theiaStickySidebarContainer').theiaStickySidebar({
        additionalMarginTop: 90
    })
});
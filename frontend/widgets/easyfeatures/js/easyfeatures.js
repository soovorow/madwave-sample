/**
 * Created by Lexsc on 29-Jun-17.
 */

var items = $('.featwidget-item');
var buttons = items.find($('.featwidget-btn'));
var firstInfo = items.first().find($('.featwidget-info'));
var firstArrow = firstInfo.parent().find($('.featwidget-arrow'));

var openedInfo;

$(document).ready(function() {
    items.each(function() {
        var x = $(this).data('featwidget-x');
        var y = $(this).data('featwidget-y');

        $(this).css({
            top: y + '%',
            left: x + '%'
        });
    });
    // firstInfo.fadeIn();
    // firstArrow.fadeIn();
    // firstInfo.parent().find($('.fa-plus')).removeClass('fa-plus').addClass('fa-minus');
    // var rightOffset = firstInfo.parent().offsetParent().width() - firstInfo.parent().outerWidth() - firstInfo.parent().position().left;
    // if(rightOffset <= 10) {
    //     openedInfo = firstInfo;
    //     firstInfo.css({left: rightOffset});
    // }
});

buttons.on('click', function() {
    var info = $(this).parent().find($('.featwidget-info'));
    var arrow = $(this).find($('.featwidget-arrow'));
    $('.featwidget-btn').find($('.fa-minus')).removeClass('fa-minus').addClass('fa-plus');
    if (!info.is(":visible")) {
        $('.featwidget-info').fadeOut('fast', function() {
            $('.featwidget-info').not(openedInfo).css({marginLeft: 0});
        });
        $('.featwidget-arrow').fadeOut('fast');
        info.fadeIn('fast');
        arrow.fadeIn('fast');
        info.parent().find($('.fa-plus')).removeClass('fa-plus').addClass('fa-minus');
        var rightOffset = info.parent().offsetParent().width() - info.parent().outerWidth() - info.parent().position().left;
        if(rightOffset <= 10) {
            openedInfo = info;
            info.css({left: rightOffset});
        }
    } else {
        info.parent().find($('.fa-minus')).removeClass('fa-minus').addClass('fa-plus');
        info.fadeOut('fast', function () {
            info.css({left: 0});
        });
        arrow.fadeOut('fast');
    }
});
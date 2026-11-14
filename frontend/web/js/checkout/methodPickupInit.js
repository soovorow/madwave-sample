function methodPickupInit() {
    if (typeof initMap == 'function')
        return initMap();
}

var list = $('#picking_up_list');
var pickup_search = $('#methodpickup-_search');
var point_id;

$('.picking-up-m').on('shown.bs.tab', function (e) {
    if (typeof initMap == 'function') {
        initMap();
    }
});
$('.picking-up-l').on('shown.bs.tab', function (e) {
    initMiniMap();
});
body.on('change', '[name="Delivery[picking_up_point]"]', function(){
    initMiniMap()
});
body.on('input', '#methodpickup-_search', function() {
    list.addClass('active');
    $('#picking_up_map').removeClass('active');
    $('.picking-up-m').removeClass('active');
    $('.picking-up-l').addClass('active');
    ajaxit('post', '/checkout/ajax/load-pick-up-radio-list', list, {keyphrase: $(this).val()})
        .done(function(response) {
            list.html(response);
        });
});
body.on('click', '.btn.on-map', function () {

    var c = 'selected';
    var is_selected = $(this).hasClass(c);
    var point_fake = $(this).prev();
    var point = $('input[name="MethodPickup[picking_up_point]"][value="' + point_fake.val() + '"]');

    var point_picker = point.next().find('.pick-point-in-list');


    point_fake.prop('checked', !is_selected);
    point.prop('checked', !is_selected);

    if (is_selected) {
        $('.btn.on-map').removeClass(c);
        point_id = 0;
        infowindow.close();
        $('[name="MethodPickup[picking_up_point]"]:not(:checked) + span.not-map').parent().fadeIn();
    } else {
        $('.btn.on-map').removeClass(c);
        $(this).addClass(c);
        point_id = point_fake.val();

        pickPoint(point_picker);

        $('#pick-up-error').hide();
    }

});
body.on('click', '.pick-point-in-list', function () {
    pickPoint($(this));
});
body.on('change', '[name="MethodPickup[picking_up_point]"]', function () {
    initMiniMap();
});
body.on('click', '[name="MethodPickup[picking_up_point]"] + span', function () {
    initMiniMap();
});


function pickPoint(el) {
    if (el.hasClass('pick-point-change')) {
        $('[name="MethodPickup[picking_up_point]"]:not(:checked) + span.not-map').parent().fadeIn();
        el.text('Выбрать');
        el.removeClass('pick-point-change');
        el.removeClass('btn-primary');
        el.addClass('btn-danger');
        $('.btn.on-map').removeClass('selected');
        point_id = 0;
        infowindow.close();
        var pickup_search = $('#methodpickup-_search');
        if (pickup_search.val().length > 0) {
            pickup_search.val(null);
            ajaxit('post', '/checkout/ajax/load-pick-up-radio-list', list, {keyphrase: pickup_search.val()})
                .done(function(response) {
                    list.html(response);
                });

            $('html, body').animate({
                scrollTop: $("#delivery_0").offset().top
            }, 1000);
        }
    } else {
        point_id = $('[name="MethodPickup[picking_up_point]"]:checked').val();
        $('[name="MethodPickup[picking_up_point]"]:not(:checked) + span.not-map').parent().fadeOut();
        $('input[name="MethodPickup[picking_up_point]"][value="' + point_id + '"]');
        el.text('Выбрать другой');
        el.addClass('pick-point-change');
        el.removeClass('btn-danger');
        el.addClass('btn-primary');

        $('html, body').animate({
            scrollTop: $("#payment_methods_h4").offset().top
        }, 1000);

        // TODO open right infoWindow
        markers.map(function(m) {
            if(m.id == point_id) {
                infowindow.open(map, m)
            }
        })
    }
}
var body = $('body');

// init theia sticky sidebar
$('.theiaContainer').theiaStickySidebar({additionalMarginTop: 30});

// On change value, save all fields in session
var checkout_form = $('#checkoutForm');
body.on('change', '#checkoutForm input', function () {
    ajaxit('post', '/checkout/ajax/save-checkout-form-in-session', null, {model: checkout_form.serialize()})
        .done(function (response) {
            // console.log(response)
        })
});
var check_container = $("#payment_type-contaienr");

body.on('change', '[name="Payment[payment_method]"]', function() {
    ajaxit(
        'post',
        '/checkout/ajax/refresh-checkout-final-widget',
        //TODO: Сделать более универсально
        check_container, {val: ($(this).val() == 'ТГ505444' || $(this).val() == 'ТГ507040')})
        .done(function (data) {check_container.html(JSON.parse(data).check)});
});
body.on('change', '[name="MethodPickup[picking_up_point]"]', function () {
    if ($('[name="MethodPickup[picking_up_point]"]:checked').length === 0) {
        $('#pick-up-error').show();
    } else {
        $('#pick-up-error').hide();
    }
});
checkout_form.on('blur', 'input', function () {
    duplicateErrors()
});
checkout_form.on('change', 'input', function () {
    duplicateErrors()
});

function duplicateErrors() {
    var errors = $('#checkoutForm').find('.has-error .help-block-error');
    $('.error-copy').remove();

    $.each(errors, function (index, value) {
        $('#payment_type-contaienr').append('<div class="error-copy" style="color: #fd3a3a">* ' + value.innerHTML + '<br><br></div>');
    });
}

checkout_form.on('beforeValidate', function (e) {
    validateAutocomplete();
    return true;
});
checkout_form.on('afterValidate', function (e) {
    validateAutocomplete();
    duplicateErrors();
    return true;
});
body.on('blur', '#methodcourier-_autocomplete', function () {
    validateAutocomplete()
});
body.on('blur', '#methodmail-_autocomplete', function () {
    validateAutocomplete()
});

body.on('blur', '#customer-firstname', function () {
    $('#methodmail-firstname').val($(this).val());
    $('#methodcourier-firstname').val($(this).val());
});


// On change delivery method show tab and initialize method
body.on('change', '[data-delivery]', function(){
    $(this).tab('show');
    initDelivery($(this).data('delivery'));
});

// Init autocomplete and add listener to filling up related fields
var place_input = $('.place-input'),
    place_result = $('.place-result'),
    priceContainer = $('#delivery-price');

function initAutocomplete(method, autoComplete, cForm) {
    var input = document.getElementById(''+method+'-_autocomplete');
    autoComplete = new google.maps.places.Autocomplete(input,{
        types: ['geocode'],
        componentRestrictions: {country: 'ru'}
    });
    autoComplete.addListener('place_changed', function () {
        fillInAddress(method, autoComplete, cForm);
    });
    google.maps.event.addDomListener(input, 'keydown', function(e) {
        if (e.keyCode == 13) e.preventDefault();
    });
}
function fillInAddress(method, autoComplete, cForm) {
    var house = $('#'+method+'-house'),
        place = autoComplete.getPlace(),
        e = [], aa, lo, k;
    // define locality variables
    for (var i = 0; i < place.address_components.length; i++) {
        var component = place.address_components[i],
            type = component.types[0],
            cType = cForm[type];
        if (cForm[type]) document.getElementById(cType[1]).value = component[cType[0]];
        if (type == 'administrative_area_level_1') aa = component['long_name'];
        if (type == 'locality') lo = component['long_name'];
    }
    if (method === 'methodcourier') {
        // load courier price
        // console.log(lo);
        // console.log(aa);
        if ($('#methodcourier-house').val() > 0) {
            loadCourierPrice({locality: lo,admin_area: aa})
        }
    }
    // composite array vith values for validation
    for (k in cForm) {if ($('#'+cForm[k][1]).val() == 0 && cForm[k][3] === true) e.push(k);}
    // if it has components, add error message for each
    if (e.length > 0) {
        var autCo = $('.field-'+method+'-_autocomplete'),
            msg = 'Обратите внимание, вы не указали';
        e.map(function(k) {
            autCo.append('<div class="alert alert-danger mb0i">'+ msg + ' <b>' +cForm[k][2] + '</b></div>');
        });
        return true
    }

    // toggle views
    // $('#checkoutForm').yiiActiveForm('validate', 'methodcourier-locality');
    house.parents('.tab-pane').find(place_result).show();
    house.parents('.tab-pane').find(place_input).hide();

    // focus next input
    house.val() == 0 ? house.focus() : $('#'+method+'-apartment').focus();
}
function validateAutocomplete() {
    if ($("[name=\"Delivery[delivery_method]\"]:checked").val() == 2) {
        if ($('#methodcourier-street').val() === '') {
            setTimeout(function(){
                $('#checkoutForm').yiiActiveForm('updateAttribute', 'methodcourier-_autocomplete', ["Пожалуйста, выберите адрес для автоматического заполнения формы"]);
            }, 100)
        }
    }
    if ($("[name=\"Delivery[delivery_method]\"]:checked").val() == 1) {
        if ($('#methodmail-street').val() === '') {
            setTimeout(function(){
                $('#checkoutForm').yiiActiveForm('updateAttribute', 'methodmail-_autocomplete', ["Пожалуйста, выберите адрес для автоматического заполнения формы"]);
            }, 100)
        }
    }
}

// clear alerts on input
body.on('input', '.autocomplete-palce', function () {$(this).parent().find('.alert').remove()});

body.on('blur', '#methodcourier-locality', function () {
    loadCourierPrice({"locality": $(this).val()});
});

// On click give ability to change address
body.on('click', '.place-edit', function () {
    $(this).parents('.tab-pane').find(place_result).hide();
    $(this).parents('.tab-pane').find(place_input).show();
    $('.place-result input').val(null);
    loadCourierPrice({})
});

var checkout_check = $('#payment_type-contaienr');
function loadCourierPrice(data) {
    ajaxit('post', '/checkout/ajax/load-courier-price', checkout_check, data)
        .done(function(response) {
            checkout_check.html(JSON.parse(response).check);
        });
}

// On change housing reset postal code
var gcode;
body.on('input', '#methodmail-house', function () {googleGeocode($(this).val(), 'methodmail')});
body.on('input', '#methodcourier-house', function () {googleGeocode($(this).val(), 'methodcourier')});
function googleGeocode(num, method) {
    var geocoder = new google.maps.Geocoder();
    var address = [];
    var arr = {
        street: $('#'+method+'-street').val(),
        house: num,
        city: $('#'+method+'-locality').val(),
        country: 'Россия'
    };

    for (var k in arr) address.push(arr[k]);

    geocoder.geocode({'address': address.join(", ")}, function(results, status) {
        if (status == 'OK') {
            gcode = results;
            var lo, aa;
            var postCode = gcode[0].address_components[6];
            if (typeof postCode !== 'undefined') {
                $('#'+method+'-postalcode').val(postCode.long_name);
            }
            for (var i = 0; i < gcode[0].address_components.length; i++) {
                var component = gcode[0].address_components[i],
                    type = component.types[0];

                if (type == 'administrative_area_level_1')
                    aa = component['long_name'];

                if (type == 'locality')
                    lo = component['long_name'];
            }
            if (method === 'methodcourier') {
                loadCourierPrice({locality: lo,admin_area: aa})
            }
        }
    });

}
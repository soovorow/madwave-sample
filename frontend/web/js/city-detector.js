$('body')
    .on('click', '#city-picker', function() {
        if (!$('.pac-container').length) {
            placesAutocomplete();
        }
    })
    .on('click', '.call-city-picker', function() {
        $('#navigation')
            .removeClass('pushed')
            .removeClass('opened');
        $('#city-picker').modal('show');
    })
    .on('click', '.set-city', function() {
        $('.set-city').removeClass('active');
        $(this).addClass('active');
        setLocation($(this).data('city'));
    })
;

$(function () {
    $.ajax({
        type: "POST",
        url:  '/citydetector/ajax/load-modal',
        data: {},
        success: function(response){
            $('body').append(response);
        },
        error: function(error){console.log(error)}
    });
});

function setLocation(input) {
    $.ajax({
        type: "POST",
        url:  '/citydetector/ajax/set-location',
        data: {input: input},
        success: function(response){
            $('.location-city')
                .text(response)
                .val(response);
            $('#city-picker').modal('hide');
            location.reload();
        },
        error: function(error){console.log(error)}
    });
}

function placesAutocomplete() {
    var input = document.getElementById('city-picker-input');
    var options = {
        types: ['(cities)'],
        componentRestrictions: {country: 'ru'}
    };
    autocomplete = new google.maps.places.Autocomplete(input, options);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        $('.set-city').removeClass('active');
        setLocation($('#city-picker-input').val());
    });
}

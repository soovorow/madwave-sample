zoom();

$('body')
    .on( 'click', '.all_imgs img', function() {
        var src = $(this).attr('src'),
            large = src.replace('small', 'large'),
            original = src.replace('small', 'original');
        $('#zoom').attr('src', large);
        $('#zoom').attr('data-zoom-image', original);
        $('.zoomWindowContainer > div').css('background-image', 'url("'+original+'")');
    })

    .on('click mouseover', '.product-card-new-images-small img', function() {
        var src = $(this).attr('src'),
            large = src.replace('small', 'large'),
            original = src.replace('small', 'original');
        var el = $(this).parents('.product-card-new').find('#product-card-new-images-large');
        el
            .fadeOut('fast', function() {
                el.attr('src', large);
                el.fadeIn('fast');
            })
    })

    .on( 'change', '#variant select[name="color"]', function() {
        var color = $(this).val();
        var size = $('#size-picker').val();
        var product_id = $(this).data('product_id');
        var container =  $('#variant');

        console.log(color);
        console.log(size);
        console.log(product_id);

        $.ajax({
            'type': 'post',
            'url': '/ajax/load-view',
            'data': {
                'color': color, 'size': size, 'product_id': product_id
            },
            'beforeSend': function () {
                container.addClass('loading')
            },
            'complete': function () {
                container.removeClass('loading')
            },
            'success': function (response) {
                console.log(response);
                var r = JSON.parse(response);
                container.html($(r.view));
                window.history.pushState(null,'hi','?sku='+r.sku);
                zoom();
                ga('send', 'pageview', window.location.href );
            }
        })
    })
    .on( 'change', '#variant select[name="size"]', function() {
        var container =  $('#variant');
        var size = $(this).val();
        var color = $('#color-picker').val();
        var product_id = $(this).data('product_id');
        $.ajax({
            'type': 'post',
            'url': '/ajax/load-view',
            'data': {
                'color': color, 'size': size, 'product_id': product_id
            },
            'beforeSend': function () {
                container.addClass('loading')
            },
            'complete': function () {
                container.removeClass('loading')
            },
            'success': function (response) {
                var r = JSON.parse(response);
                container.html($(r.view));
                window.history.pushState(null,'hi','?sku='+r.sku);
                zoom();
                ga('send', 'pageview', window.location.href );
            }
        });
    })
    .on( 'click', '.edit-review', function() {
        var container = $(this).parents('.testimonials');
        ajaxLoad(container, '/ajax/load-review-editing', {review_id: $(this).data('review-id')});
    })

    .on('click', '.btn-write-review', function () {
        $('#testimonialsForm').modal();
    })

    .on('click', '.vote', function () {

        var bool = $(this).data('vote'),
            el = $(this);

        console.log(bool);

        var c = $('.vote-container');

        $.ajax({
            type: "POST",
            url:  '/ajax/vote-comment',
            async: false,
            data: {id: $(this).parents('.testimonials').data('review_id'), bool: $(this).data('vote')},
            beforeSend: function () {$(c).addClass('loading')},
            complete: function () {$(c).removeClass('loading')},
            success: function(response){
                // console.log(response);

                if (response == 'guest') {
                    el.parents('.vote-container').addClass('firbidden');
                    return false;
                }

                el.parents('.vote-container').find('.vote-up span').html(response[1]);
                el.parents('.vote-container').find('.vote-down span').html(response[3]);

                el.addClass('active');
                if (el.hasClass('vote-up')) {
                    var vote_down =el.parents('.vote-container').find('.vote-down');
                    vote_down.removeClass('active');
                } else {
                    var vote_up = el.parents('.vote-container').find('.vote-up');
                    vote_up.removeClass('active');
                }
            },
            error: function(error){console.log(error)}
        });

    })
    .on( 'click', '.description .toggler', function() {
        $('.description').toggleClass('open')
    })
;

function zoom() {
    $('.zoomContainer').remove();
    $("#zoom").elevateZoom({
        zoomWindowFadeIn: 500,
        zoomWindowFadeOut: 500,
        zoomWindowWidth: $('#product-description-width').width(),
        zoomWindowHeight: 500,
        lensFadeIn: 500,
        lensFadeOut: 500,
    });
}
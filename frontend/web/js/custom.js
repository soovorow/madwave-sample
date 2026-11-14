var body = $('body');

function ajaxit(type, url, container, data) {
    return $.ajax({
        url: url,
        type: type,
        data: data,
        'beforeSend': function () {
            if (container instanceof HTMLElement) {
                container.addClass('loading')
            }
        },
        'complete': function () {
            if (container instanceof HTMLElement) {
                container.removeClass('loading')
            }
        }
    });
}

function ajaxLoad(container, url, data) {
    if (container.length > 0) {
        $.ajax({
            type: "POST",
            url:  url,
            data: data,
            // async: false,
            beforeSend: function () {
                container.addClass('loading')
            },
            complete: function () {
                container.removeClass('loading')
            },
            success: function(response){
                container.html(response);
            },
            error: function(response){
                console.log(response);
            }
        });
    }
}

function easyAjax(a, c, l, d) {
    var r = false;
    $.ajax({
        type: "POST",
        url:  '/ajax/' + a,
        async: false,
        data: d,
        beforeSend: function () {
            $(c).addClass('loading')
        },
        complete: function () {
            $(c).removeClass('loading')
        },
        success: function(response) {
            $(l).html(response);
            r = true;
        },
        error: function(error) {
            console.log(error)
        }
    });
    return r;
}

$(document).ready(function() {
    // Prevent # errors
    $('[href="#"]').click(function (e) {
        e.preventDefault();
    });

    $('.slide').on('click', function() {
        var el = $(this), active;
        active = !!el.hasClass('active');
        if (!active) {
            el.addClass('active');
        }
        $(this).next('.slide-content').slideToggle('fast', function() {
            if (active) {
                el.removeClass('active');
            }
        });
    });

    $('#filter-action').on('change', function () {
        $('#filter_form').attr('action', $(this).val())
    });

    body.on('input', '#search-store', function() {
        var store_result = $('.shops-results');
        $('#picking_up_map').removeClass('active');
        $('.picking-up-m').removeClass('active');
        $('.picking-up-l').addClass('active');
        ajaxit('post', '/ajax/load-store-list', store_result, {keyphrase: $(this).val()})
            .success(function(response) {
                store_result.html(response);
                initMap()
            });
    });

    // Smooth scroll
    $('a[href^="#section"]').click(function() {
        var el = $(this).attr('href');
        $('body').animate({
            scrollTop: $(el).offset().top}, 1000);
        return false;
    });

    // bootstrap tooltip
    $(function () {
        var dataToggle = $('[data-toggle="tooltip"]');
        if (dataToggle.length) {
            dataToggle.tooltip()
        }
    });

    // Product tabs
    $('.product-tabs').on('click', 'a:not(.active)', function(e) {
        e.preventDefault();
        $(this).addClass('active').siblings().removeClass('active')
            .closest('.row').find('.product-tabs-content').removeClass('active').eq($(this).index()).addClass('active');
    });

    // Contact mobile toggles
    // $('.shops-results-item').on('click', function(){
    //     $(this).toggleClass('active');
    //     $(this).find('.shops-results-item-toggle').toggleClass('active');
    // });

    $('#shopsShowList').on('click', function(){
        $(this).addClass('active');
        $('#shopsShowMap').removeClass('active');
        $('.shops-results-scroll').addClass('active');
        $('.shops-results-map').removeClass('active');
    });

    $('#shopsShowMap').on('click', function(){
        $(this).addClass('active');
        $('#shopsShowList').removeClass('active');
        $('.shops-results-scroll').removeClass('active');
        $('.shops-results-map').addClass('active');
        initMap();
    });

    $('#shopsShowFilters').on('click', function(){
        $('.shops-mobile-filter').fadeIn();
    });

    $('#shopHideFilters').on('click', function(){
        $('.shops-mobile-filter').fadeOut();
    });

    // Product comments
    $('.product-testimonial-replies-toggle').on('click', function(){
        if ($(this).text() === "Скрыть"){
            $(this).text("Показать");
            $(this).parent().parent().find('.product-testimonial-author').fadeOut();
            $(this).parent().parent().find('.product-testimonial-date').fadeOut();
            $(this).parent().parent().find('.product-testimonial-content-type').fadeOut();
        } else {
            $(this).text("Скрыть");
            $(this).parent().parent().find('.product-testimonial-author').fadeIn();
            $(this).parent().parent().find('.product-testimonial-date').fadeIn();
            $(this).parent().parent().find('.product-testimonial-content-type').fadeIn();
        }
    });

    $('.openBonusModal').on('click', function (e) {
        e.preventDefault();
        $('#bonusModal').modal();
    });

    $('.openSignupModal').on('click', function (e) {
        e.preventDefault();
        $('#signupModal').modal();
    });

    $('#bonusModal .openSignupModal').on('click', function (e) {
        e.preventDefault();
        $('#bonusModal').modal('toggle');
        $('#signupModal').modal();
    });

    // Delivery page
    $('.delivery-wrapper').on('click', function(){
        $(this).toggleClass('active');
        $(this).find('.delivery-item-toggle').toggleClass('active');
        $(this).find('.delivery-content-hidden').toggleClass('active');
    });

    //TODO: Для рендера после загрузки
    // $.ajax({
    //     type: "POST",
    //     url:  '/ajax/load-popup',
    //     data: {},
    //     success: function(response){
    //         $('body').append(response);
    //     },
    //     error: function(error){console.log(error)}
    // });
});

$('#sort-select').on('change', function () {
    window.location = $(this).val()
});
$('#subcat-select').on('change', function () {
    window.location = $(this).val()
});

function loadModal(type, params) {
    $('#modal_bootstrap').remove();
    $('.modal-backdrop').remove();
    $.ajax({
        type: 'POST',
        url: '/ajax/load-modal',
        data: {type: type, params: params},
        success: function (response) {
            $('body').append(response);
            $('#modal_bootstrap').modal('show');
        },
        error: function(response){
            console.log(response);
        }
    });
}

function gaCartAction(product, action, quantity, list) {
    ga('ec:addProduct', {
        'id': product.id,
        'name': product.name,
        'category': product.category,
        'brand': product.brand,
        'variant': product.variant,
        'price': product.price,
        'quantity': quantity
    });
    ga('ec:setAction', action, {
        'list': list
    });
    ga('send', 'event', 'UX', 'click', 'add to cart');
}

$('#main-banner').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    infinite: true,
    dots: true,
    autoplay: true,
    autoplaySpeed: 5000
});

/**
 * * * * * * * layout * * * * * * *
 */

var navigation = $('#navigation'),
    catalog = $('#catalog'),
    didScroll,
    lastScrollTop = 0,
    delta = 5,
    navbarHeight = $('#header').outerHeight();

$(window).scroll(function(event){
    didScroll = true;
});

setInterval(function() {
    var header = $('#header');
    var hamburger = $('.humburger');
    if (didScroll) {
        var st = $(this).scrollTop();
        if(Math.abs(lastScrollTop - st) <= delta)
            return;
        if (st > lastScrollTop && st > navbarHeight){
            header.removeClass('nav-down').addClass('nav-up');
        } else {
            if(st + $(window).height() < $(document).height()) {
                header.removeClass('nav-up').addClass('nav-down');
            }
        }
        if ($(window).width() > 991 && $(window).scrollTop() > 52 && !hamburger.is(':hover')) {
            header.addClass('fixed');
        } else {
            header.removeClass('fixed');
        }

        lastScrollTop = st;
        didScroll = false;
    }
}, 250);

body
// footer
    .on('click', '.goHome', function() {
        $("html, body").animate({ scrollTop: 0 }, "fast");
    })

    // mobile nav
    .on('click', '.humburger .fa', function() {
        navigation.removeClass('pushed');
        navigation.toggleClass('opened');
        catalog.removeClass('opened');
        if (navigation.hasClass('opened') || navigation.hasClass('pushed')) {
            body.addClass('locked');
        } else {
            body.removeClass('locked');
        }
    })
    .on('click', '.catalog-opener', function() {
        navigation.removeClass('opened');
        navigation.toggleClass('pushed');
        catalog.toggleClass('opened');
    })
    .on('click', '.close-catalog', function() {
        catalog.removeClass('opened');
        navigation.removeClass('pushed');
        navigation.addClass('opened');
    })
    .on('click', '.close-nav', function() {
        catalog.removeClass('opened');
        navigation.removeClass('pushed');
        navigation.removeClass('opened');
        body.removeClass('locked');
    })
    .on('click', '.open-childs', function(e) {
        e.preventDefault();
        if ($(window).width() < 992) {
            var el = $(this);
            var active = !!el.hasClass('active');
            var childs = el.next('.slide-content');
            var href = el.attr('href');

            if (!active) {
                el.addClass('active');
            }

            if (!childs.children().first().hasClass('link-all') && $(this).hasClass('item')) {
                childs.prepend('<li class="link-all"><a href="' + href + '">Смотреть все</a></li>');
            }

            if ($(this).find('.right-arrow').hasClass('rotate')) {
                $(this).find('.right-arrow').toggleClass('opened')
            }

            childs.slideToggle('fast', function() {
                if (active) {el.removeClass('active');}
            });

        } else {
            if (!$(this).hasClass('block')) {
                window.location = $(this).attr('href')
            }
        }
    })

;

body
// google analytics events
    .on('click', '.product-wrap', function () {
        ga('ec:addProduct', $(this).children('.product').data('ga_product'));
        ga('ec:setAction', 'click', {list: $(this).children('.product').data('list')});
        ga('send', 'event', 'UX', 'click', 'Product Click');
    })
    .on( 'click', '.btn-ga-cart-action', function () {
        var product = $(this).data('product'),
            action = $(this).data('action'),
            quantity = $(this).data('quantity');
        gaCartAction(product, action, quantity)
    })

    // add to cart
    .on( 'click', '.add_to_cart-modal', function () {
        loadModal('add-to-cart', {sku: $(this).data('sku')})
    })
;


body.on('focus', '#add_to_cart-quantity', function () {
    $(this).removeClass('hasError')
});

body.on('click', '.add_to_cart-ajax', function () {

    var btn = $(this),
        list = btn.data('list'),
        quantity = btn.parents('#variant').find('select[name="quantity"]').val(),
        product = btn.data('product'),
        quantity_field = $('#add_to_cart-quantity');

    if (btn.hasClass('btn-warning')) {
        if (quantity_field.val().length < 6) {
            quantity_field.addClass('hasError');
            return false;
        }
    } else {
        if (btn.hasClass('complete')) {
            return window.location = '/cart'
        }
        btn.addClass('complete');
    }

    $.ajax({
        type: "POST",
        url:  '/ajax/add-to-cart',
        data: {
            sku: $(this).data('sku'),
            quantity: quantity,
            subscriber: $(this).parents('.add-to-cart-container').find('input[name="subscriber"]').val()
        },
        beforeSend: function () {
            btn.addClass('loading');
        },
        complete: function () {
            btn.removeClass('loading');
        },
        success: function(response){
            gaCartAction(product, 'add', quantity, list);
            refreshCart();
            if (btn.hasClass('btn-warning')) {
                btn.text('Оповещание придет на ваш e-mail');
                return false;
            }
        },
        error: function(response){
            console.log(response);
        }
    })
})
    .on( 'change', '.modal-body select[name="size"]', function () {
        ajaxLoad($(".add_to_cart-container"), '/ajax/reload-addtocart', {'sku': $(this).val()} );
    })
    .on('click', '#modal-close', function() {
        $('#modal').hide();
        $('#modal-close').hide();
        $('html').css('overflow','visible')
    })

    // helpers
    .on( 'click', '.data-link', function() {
        window.location = $(this).data('url');
    })
;

// catalog js
body
    .on('change', 'select#sorting', function() {
        window.location = $(this).val();
    })
    .on('click', '.btn-slide_filter', function() {
        $('#filter_form').slideToggle();
    })
;

function refreshCart() {
    ajaxLoad($('.cart-container'), '/checkout/ajax/refresh-cart');
    if (window.location.pathname === '/checkout/cart/index') {
        location.reload();
    }
    if (typeof refreshCheckoutWidget === "function") {
        refreshCheckoutWidget();
    }
}


// product page js

slickProductCards('.related-products');
slickProductCards('.best-sellers');
slickProductCards('.viewed-products');

function slickProductCards(el) {
    $(el).slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        infinite: false,
        dots: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });
}

// profile js
body
    .on('click', '.edit-user-data', function () {
        $('#user-data').find('input').attr('disabled', false);
        $('.save-user-data').removeClass('hidden');
        $(this).hide();
    })
;

// home page js
var page = $('#show-more').data('page');
body
    .on('click', '#show-more', function () {
        $('#show-more').attr('data-page', page);
        $.ajax({
            type: "POST",
            url:  '/ajax/load-next-page',
            data: {'page': page},
            beforeSend: function () {
                $('#show-more').find('.fa').addClass('fa-spin')
            },
            complete: function () {
                $('#show-more').find('.fa').removeClass('fa-spin')
            },
            success: function(response){
                $('.home-products').append(response);
                page++;
                $('#show-more').attr('data-page', page)
            },
            error: function(response){
                console.log(response);
            }
        });
    })
    .on('change', '.contact_city', function () {
        var container = $('#contact-map');
        $.ajax({
            type: "POST",
            url:  '/ajax/load-metro',
            data: {
                'city_id': $(this).val()
            },
            dataType: "json",
            beforeSend: function () {
                container.addClass('loading')
            },
            complete: function () {
                container.removeClass('loading')
            },
            success: function(response){
                console.log(response);
                container.find('.address').html(response.address);
                container.find('.phone-numb').html(response.phone);
                container.find('.time').html(response.time);
                container.find('.show-on-map').attr('data-coords', response.coordinates);
                container.find('#contact_metro').html(response.select);
            },
            error: function(response){
                console.log(response);
            }
        });
    })
    .on('change', '.contact_metro', function () {
        var container = $('#contact-map');
        $.ajax({
            type: "POST",
            url:  '/ajax/load-metro',
            data: {'metro_id': $(this).val()},
            dataType: "json",
            beforeSend: function () {
                container.addClass('loading')
            },
            complete: function () {
                container.removeClass('loading')
            },
            success: function(response){
                console.log(response);
                container.find('.address').html(response.address);
                container.find('.phone-numb').html(response.phone);
                container.find('.time').html(response.time);
                container.find('.show-on-map').attr('data-coords', response.coordinates);
            },
            error: function(response){
                console.log(response);
            }
        });
    })
    .on('click', '.show-on-map', function(evt) {
        var container = $('#contact-map'),
            contactMetro = $('.contact_metro');
        $.ajax({
            type: "POST",
            url:  '/ajax/load-metro',
            data: {
                'metro_id': contactMetro.val() > 0 ? contactMetro.val() : $('.contact_city').val()
            },
            dataType: "json",
            beforeSend: function () {
                container.addClass('loading')
            },
            complete: function () {
                container.removeClass('loading')
            },
            success: function(response) {
                var point = new google.maps.LatLng(response.coordinates[0], response.coordinates[1]);
                map.setCenter(point);
                map.setZoom(16);
                var marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(response.coordinates[0]),
                        lng: parseFloat(response.coordinates[1])
                    },
                    map: map,
                    // icon: '/marker.png',
                    title: 'Hello World!'
                });
                $('.cont').addClass('slide-down')
            }
        });
    })
    .on('click touchstart', '.cont', function() {
        $(this).removeClass('slide-down')
    })
;

$(function(){
    $('.spoiler-text').hide();
    $('.spoiler-toggle').click(function() {
        $(this).next().animate({height: 'toggle'});
    }); // end spoiler-toggle
}); // end document ready


function gmapInitializeer(){
    if (typeof initMap === "function")
        return initMap();
}

var delay = (function() {
    var timer = 0;
    return function(callback, ms) {
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

body.on('click', '.search-submit', function() {
    $('html').addClass('lock');
    $('#search-modal').fadeIn();
    $('#search-input').focus();
});

body.on('click', '.search-modal__close', function() {
    $('html').removeClass('lock');
    $('#search-modal').fadeOut();
});

body.on('click', '#search-suggestions .a', function() {
    $('#search-input').val($(this).text());
    search($(this).text());
});

body.on('keyup', '#search-input', function() {
    search($(this).val());
});

function search(val) {
    val = val.toLowerCase();
    var searchResult = $('#search__result');
    searchResult.addClass('loading');
    delay(function(){
        ajaxit('POST', '/ajax/search', searchResult, {'key': val})
            .done(function (response) {
                searchResult.html(response);
                searchResult.removeClass('loading');
            })
    }, 500 );
    ga('send', 'pageview', '/catalog/search?keywords='+val);
}

$('#bonusModal').find('.close').click(function () {
    ga('send', 'event', 'Pop-up', 'click', 'close');
});
$('.pp-cont').click(function () {
    ga('send', 'event', 'Pop-up', 'click', 'continue in assignment');
});
$('.pp-hav').click(function () {
    ga('send', 'event', 'Pop-up', 'click', 'i have card already');
});
$('.pp-suc').click(function () {
    ga('send', 'event', 'Pop-up', 'click', 'success');
});
$('#popup-form-card-cancel').click(function () {
    ga('send', 'event', 'Pop-up', 'click', 'i have not card');
});

$('.dropdown-toggle').dropdown();

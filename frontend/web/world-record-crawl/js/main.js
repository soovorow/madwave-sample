//'10420' and want to display it like this: 01:44.20

function getShareText($element) {
    var text = strip($element);
    text = text.replace("Поздравляем!", "");
    text = text.replace("Ваш", "Мой");
    text = text.replace("Вам", "Мне");
    text = text.replace(/(?:\r\n|\r|\n)/g, '');
    text = text.replace(/\s\s+/g, ' ');
    return text.trim();
}

function strip(html) {
    var tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
}

function leading0(number){
    return number < 10 ? "0" : "";
}

function convertStringTimeToSot(input) {
    var parts = input.split(':'),
        minutes = +parseInt(parts[0]),
        seconds = +parseInt(parts[1]),
        hundredths = +parseInt(parts[2]);
    return parseInt(minutes * 60 * 100 + seconds * 100 + hundredths);
}

function convertTimeToSot(mins, secs, huns) {
    var minutes = +mins,
        seconds = +secs,
        hundredths = +huns;
    return parseInt(minutes * 60 * 100 + seconds * 100 + hundredths);
}

function convertSotToTime(hundredths, short) {
    mins = parseInt((hundredths / 100) / 60);
    secs = parseInt((hundredths / 100) % 60);
    huns = parseInt(hundredths % 100);
    suffix = '';
    if(mins > 0) {
        if(typeof short === "undefined" || short === true) {
            suffix = ' мин';
        }
        else {
            suffix = ' минут';
        }
        time = leading0(mins) + mins + ":" + leading0(secs) + secs + "." + leading0(huns) + huns + suffix;
    }
    else {
        if(typeof short === "undefined" || short === true) {
            suffix = ' сек';
        }
        else {
            suffix = ' секунд';
        }
        time = leading0(secs) + secs + "." + leading0(huns) + huns + suffix;
    }
    return time;
}




(function ($) {

    var slider = document.getElementById('slider');
    noUiSlider.create(slider, {
        start: 0,
        step: 1,
        range: {
            'min': 0,
            'max': 0
        }
    });

    var site = 'http://www.madwave.ru/world-record-crawl';
    var YaShareInstanсe = new Ya.share({
        element: 'ya_share',
        l10n: 'ru',
        theme: 'counter',
        services: ['vkontakte', 'facebook', 'twitter', 'pinterest'],
        image: site + '/main.jpg',
        onready: function(instance) {
            $('#calc').click(function() {
                instance.updateShareLink(
                    site,
                    getShareText($('.result-block.active').find('.col-md-12').html())
                );
            });
        }
    });
    YaShareInstanсe.updateShareLink('http://api.yandex.ru', 'API');

    var values = {};

    $('#calc-form').validate({
        rules: {
            min: {
                minlength: 0,
                maxlength: 2,
                range: [0, 30],
                //digits: true
            },
            sec: {
                required: true,
                minlength: 0,
                maxlength: 2,
                range: [0, 60],
                //digits: true

            },
            sot: {
                required: true,
                minlength: 0,
                maxlength: 2,
                range: [0, 99],
                //digits: true
            },
        },
    });

    $("input[name='min']").TouchSpin({
        min: 0,
        max: 30,
        step: 1,
        decimals: 0,
        verticalbuttons: true,
        initval: '0'
    });

    $("input[name='sec']").TouchSpin({
        min: 0,
        max: 59,
        step: 1,
        decimals: 0,
        verticalbuttons: true,
        initval: '0',
        leadingzero: true
    });

    $("input[name='sot']").TouchSpin({
        min: 0,
        max: 99,
        step: 1,
        decimals: 0,
        verticalbuttons: true,
        initval: '0',
        leadingzero: true
    });

    $('#fullpage').fullpage({
        //sectionsColor: ['#1bbc9b', '#4BBFC3', '#7BAABE', 'whitesmoke', '#ccddff'],
        //anchors: ['page1', 'page2', 'page3', 'page4', 'page5', 'page6'],
        //menu: '#menu',
        lockAnchors: true,
        css3: true,
        scrollingSpeed: 1000
    });
    $.fn.fullpage.setMouseWheelScrolling(false);
    $.fn.fullpage.setAllowScrolling(false);


    var gender   = null;
    var style    = null;
    var distance = null;
    var styleNames = [];
    styleNames[1] = 'баттерфляем';
    styleNames[2] = 'брассом';
    styleNames[3] = 'на спине';
    styleNames[4] = 'вольным стилем';

    $('.go-to-next-page').on('click touchstart', function (e) {
        e.preventDefault();
        $.fn.fullpage.moveSectionDown();
    });

    $('.gender').on('click touchstart', function (e) {
        e.preventDefault();
        gender = $(this).data('gender');
        if(gender == 'm') {
            $('.postavil-or-postavial').text('поставил');
        }
        else {
            $('.postavil-or-postavial').text('поставила');
        }
        $.fn.fullpage.moveSectionDown();
    });

    $('.style').on('click touchstart', function (e) {
        e.preventDefault();
        style = $(this).data('style');
        if(style == 4) {
            $('.freestyle').show();
        }
        else {
            $('.freestyle').hide();
        }
        $.fn.fullpage.moveSectionDown();
    });

    $('.distance').on('click touchstart', function (e) {
        e.preventDefault();
        distance = $(this).data('distance');

        $('span.replace.distance').text(distance);
        $('span.replace.stylename').text(styleNames[style]);

        $.fn.fullpage.moveSectionDown();
    });

    $('.size').on('click touchstart', function (e) {
        e.preventDefault();
        var size = $(this).data('size');
        //console.log({gender: gender, size: size, distance: distance, style: style});
       /* $.get('calc/calc.php', {gender: gender, size: size, distance: distance, style: style})
            .done(function(data) {
                console.log(data);
                values = data;
                //alert( "Data Loaded: " + data );
            });*/

        var request = $.ajax({
            url: 'calc/calc.php',
            method: 'GET',
            data: {gender: gender, size: size, distance: distance, style: style},
            dataType: 'json'
        });

        request.done(function( msg ) {
            //console.log(msg);
            values = msg;
        });

        request.fail(function( jqXHR, textStatus ) {
            console( "Request failed: " + textStatus );
        });

        $.fn.fullpage.moveSectionDown();
    });

    $('#calc').on('click touchstart', function (e) {
        e.preventDefault();
        /*console.log(values);*/

        $('.result-block').hide();
        $('.result-block').removeClass('active');

        var min = parseInt($('input[name="min"]').val());
        var sek = parseInt($('input[name="sec"]').val());
        var sot = parseInt($('input[name="sot"]').val());

        if(min == 0 && sek == 0 && sot == 0) {
            // nothing to do
        }
        else {
            if($('#calc-form').valid()) {
                var result = 0;
                var output = {
                    name: '',
                    year: '',
                    time: ''
                };

                if(min == '') {
                    min = 0;
                }
                if(sek == '') {
                    sek = 0;
                }
                if(sot == '') {
                    sot = 0;
                }

                var my_hundredths = convertStringTimeToSot(parseInt(min)+':'+parseInt(sek)+':'+parseInt(sot));

                var first_hundredths = convertStringTimeToSot(values[0][0]);
                var last_hundredths = convertStringTimeToSot(values[values.length-1][0]);

                if(my_hundredths >= first_hundredths) {
                    result = 1;
                    output['name'] = values[0][1];
                    output['year'] = values[0][4];
                    output['time'] = convertSotToTime(my_hundredths - first_hundredths, false);
                }
                if(my_hundredths > last_hundredths && my_hundredths < first_hundredths) {
                    result = 2;
                    $.each(values, function (index, value) {
                        //var time = value[0].split(':');
                        var time = value[0];
                        time = convertStringTimeToSot(time);
                        if(parseInt(my_hundredths) > parseInt(time)) {
                            output['name'] = values[index-1][1];
                            output['year'] = values[index-1][4];
                            return false;
                        }
                    });
                }
                if(my_hundredths <= last_hundredths) {
                    result = 3;
                    output['name'] = values[values.length-1][1];
                    output['year'] = values[values.length-1][4];
                }

                if(result != 0) {
                    $('#result-'+result).show();
                    $('#result-'+result).addClass('active');
                    $('#result-'+result).find('.time-delta').text(output['time']);
                    $('#result-'+result).find('.name-val').text(output['name']);
                    $('#result-'+result).find('.year-val').text(output['year']);
                }


                slider.noUiSlider.destroy();
                noUiSlider.create(slider, {
                    start: 0,
                    step: 1,
                    range: {
                        'min': 0,
                        'max': values.length - 1
                    }
                });
                slider.noUiSlider.on('update', function(value, handle ) {
                    value = parseInt(value);
                    var name = values[value][1];
                    var country = values[value][2];
                    var date = values[value][3];
                    var year = values[value][4];
                    var time = convertStringTimeToSot(values[value][0]);
                    var color = '';

                    if(my_hundredths < time) {
                        color = 'green';
                        var delta = time - my_hundredths;
                        $('.faster-or-not').text('вы быстрее на');
                    }
                    else {
                        color = 'red';
                        var delta = my_hundredths - time;
                        $('.faster-or-not').text('вам недостает');
                    }
                    delta = convertSotToTime(delta);

                    time = convertSotToTime(time);
                    $('p.name').text(name);
                    $('p.year').text(year);
                    $('span.country-val').text(country);
                    $('span.date').text(date);
                    $('span.time-val').text(time);
                    $('span.faster-val').text(delta);
                    $('span.faster-val').removeClass('red');
                    $('span.faster-val').removeClass('green');
                    $('span.faster-val').addClass(color);

                    //input.value = values[handle];
                });

                $.fn.fullpage.moveSectionDown();
            }
        }


    });

    $('.go-to-top').on('click touchstart', function(e){
        e.preventDefault();
        //$.fn.fullpage.moveTo(1);
        $.fn.fullpage.moveSectionUp();
    });

})(jQuery);


<?php

/* @var $stores \yii\db\Query */
/* @var $stores_array [] */

$locations = [];
?>

<style>
    .spoiler-toggle {
        margin: 0;
        padding: 15px 0;
    }
    .spoiler-toggle:hover {
        cursor: pointer;
        background: #eee ;
    }
</style>

<div class="shops-results active" style="">
    <div class="">
        <div class="col-sm-4 shops-results-scroll active pr0" style="padding: 0">
            <div class="">
                <div class="shops-result-num" style="margin-bottom: 30px">
                    Найдено
                    <?= $stores->count() ?>
                    магазинов
                </div>
                <?php
                $cities = [];
                foreach ($stores_array as $i => $store) {
                    $cities[$store['city']][$i] = $store;
                }
                ?>
                <?php foreach ($cities as $city => $data): ?>
                    <h4 class='spoiler-toggle' style='padding-left: 40px'><?= $city ?></h4>
                    <div class='spoiler-text'>
                        <?php foreach ($data as $i => $store): ?>
                            <?php
                            $metro = !empty($store['metro_title']) ?
                                '<div class="shops-results-row">'.
                                    '<i class="ico ico-metro fa fa-train" style="background: '.$store['metro-title'].'"></i>'.
                                    '<span>'.$store['metro_title'].'</span>'.
                                '</div>' :
                                '';

                            $address = '<div class="shops-results-row">'.
                                    '<i class="ico ico-loc-gray"></i>'.
                                    '<span>'.$store['address'].'</span>'.
                                '</div>';

                            $hours = '<div class="shops-results-row">'.
                                    '<i class="ico ico-time"></i>'.
                                    '<span>'.$store['working_hours'].'</span>'.
                                '</div>';

                            $phone = '<div class="shops-results-row">'.
                                    '<i class="ico ico-mobile"></i>'.
                                    '<span>'.$store['phone'].'</span>'.
                                '</div>';

                            $pick_up = '<div class="shops-results-row">'.
                                    '<i class="ico ico-delivery"></i>'.
                                    '<span>'.
                                        '<b>Самовывоз:</b> '
                                        .($store['is_pick_up_point'] ? 'Да' : 'Нет').
                                    '</span>'.
                                '</div>';

                            $title = '<div class="shops-results-row">'.
                                    '<i class="ico ico-place"></i>'.
                                    '<span>'.
                                        '<b>'.$store['title'].'</b>'.
                                    '</span>'.
                                '</div>';

                            $locations[] = [
                                'id' => $store['id'],
                                'title' => $store['title'],
                                'coordinates' => [
                                    'lat' => floatval($store['latitude']),
                                    'lng' => floatval($store['longitude']),
                                ],
                                'content' => '<br>'.$title.$metro.$address.$hours.$phone.$pick_up,
                            ];

                            ?>
                            <div class="shops-results-item active" data-id="<?= $store['id'] ?>">
                                <?= $address ?>
                                <?= $title ?>
                                <?= $metro ?>
                                <?= $hours ?>
                                <?= $phone ?>
                                <?= $pick_up ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
        <div class="col-sm-8 shops-results-map no-pad-l" style="padding: 0">
            <!-- google map-->
            <div id="shopsMap"></div>
            <script>
                var locations = <?= \yii\helpers\Json::encode($locations) ?>;
                var infowindow;
                var markers;
                var map;
                function initMap() {
                    map = new google.maps.Map(document.getElementById('shopsMap'), {
                        center: {
                            lat: <?= $stores->all()[0]['latitude'] ?>,
                            lng: <?= $stores->all()[0]['longitude'] ?>
                        },
                        scrollwheel: false,
                        zoom: 4,
                        styles: [
                            {
                                "featureType": "all",
                                "elementType": "all",
                                "stylers": [
                                    {
                                        "saturation": -100
                                    },
                                    {
                                        "gamma": 0.5
                                    }
                                ]
                            }
                        ]
                    });
                    infowindow = new google.maps.InfoWindow({content: ''});
                    markers = locations.map(function (locations) {
                        var m = new google.maps.Marker({
                            position: locations.coordinates,
                            title: locations.title,
                            id: locations.id,
                        });
                        bindInfoWindow(m, map, infowindow, locations.content);
                        return m;
                    });

                    var markerCluster = new MarkerClusterer(map, markers,
                        {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'}
                        );
                }
                function bindInfoWindow(marker, map, infowindow, html) {
                    google.maps.event.addListener(marker, 'click', function () {
                        infowindow.setContent(html);
                        infowindow.open(map, marker);
                    });
                }
            </script>
            <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
        </div>
    </div>
</div>
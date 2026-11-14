<?php

/* @var $stores \yii\db\Query */
/* @var $store \common\models\SelfPickUpPoints */

$locations = [];
?>

<div class="shops-results active">
    <div class="row">
        <div class="col-sm-4 shops-results-scroll active pr0">
            <div class="border-left">
                <div class="shops-result-num">
                    Найдено
                    <?= $stores->count() ?>
                    магазинов
                </div>
                <?php foreach ($stores->all() as $i => $store): ?>
                    <?php
                    $metro = !empty($store->metro_title) ?
                        '<div class="shops-results-row">'.
                            '<i class="ico ico-metro fa fa-train" style="background: '.$store->metro_color.'"></i>'.
                            '<span>'.$store->metro_title.'</span>'.
                        '</div>' :
                        '';

                    $address = '<div class="shops-results-row">'.
                            '<i class="ico ico-loc-gray"></i>'.
                            '<span>'.$store->address.'</span>'.
                        '</div>';

                    $hours = '<div class="shops-results-row">'.
                            '<i class="ico ico-time"></i>'.
                            '<span>'.$store->working_hours.'</span>'.
                        '</div>';

                    $phone = '<div class="shops-results-row">'.
                            '<i class="ico ico-mobile"></i>'.
                            '<span>'.$store->phone.'</span>'.
                        '</div>';

                    $pick_up = '<div class="shops-results-row">'.
                            '<i class="ico ico-delivery"></i>'.
                            '<span>'.
                                '<b>Самовывоз:</b> '.
                                ($store->is_pick_up_point ? 'Да' : 'Нет').
                            '</span>'.
                        '</div>';

                    $title = '<div class="shops-results-row">'.
                            '<i class="ico ico-place"></i>'.
                            '<span>'.
                                '<b>'.$store->title.'</b>'.
                            '</span>'.
                        '</div>';

                    $locations[] = [
                        'id' => $store->id,
                        'title' => $store->title,
                        'coordinates' => [
                            'lat' => floatval($store->latitude),
                            'lng' => floatval($store->longitude),
                        ],
                        'content' => '<br>'.$title.$metro.$address.$hours.$phone.$pick_up,
                    ];
                    ?>
                    <div class="shops-results-item <?= $i == 0 ? 'active' : '' ?>" data-id="<?= $store->id ?>">
                        <div class="shops-results-item-toggle active"><i class="fa fa-caret-down"></i></div>
                        <?= $title ?>
                        <?= $metro ?>
                        <?= $address ?>
                        <?= $hours ?>
                        <?= $phone ?>
                        <?= $pick_up ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-sm-8 shops-results-map no-pad-l">
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
                            lat: <?= $stores->all()[0]->latitude ?>,
                            lng: <?= $stores->all()[0]->longitude ?>
                        },
                        scrollwheel: false,
                        zoom: <?= $stores->count() > 2 ? 10 : 13 ?>
                    });
                    infowindow = new google.maps.InfoWindow({content: ''});
                    markers = locations.map(function (locations) {
                        var m = new google.maps.Marker({
                            position: locations.coordinates,
                            title: locations.title,
                            id: locations.id,
                            icon: 'https://www.madwave.ru/images/icons/marker_cluster/m0.png'
                        });
                        bindInfoWindow(m, map, infowindow, locations.content);
                        return m;
                    });

                    var markerCluster = new MarkerClusterer(map, markers, {
                        imagePath: 'https://www.madwave.ru/images/icons/marker_cluster/m'
                    });
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
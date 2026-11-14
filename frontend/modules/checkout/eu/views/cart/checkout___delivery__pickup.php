<?php

/**
 * @var $form \yii\widgets\ActiveForm
 * @var $model \frontend\modules\checkout\eu\models\delivery_methods\MethodPickup;
 */

$data = $model->pick_up_points;

if ($model->pick_up_points_count > 0) { ?>

    <div id="delivery_0" class="tab-pane active schedule-pane">
        <h4 class="divide">Выберите магазин</h4>

        <?/*= $form->field($model, '_search')->textInput([
            'class' => 'form-control rad0',
            'placeholder' => 'Введите название магазина, адрес или станцию метро'
        ]) */?>


        <ul class="list-or-map" role="tablist">

            <li role="presentation" class="picking-up-l">
                <a href="#picking_up_list" aria-controls="picking_up_list" role="tab" data-toggle="tab">
                    Списком</a></li>

            <li role="presentation" class="picking-up-m active">
                <a href="#picking_up_map" aria-controls="picking_up_map" role="tab" data-toggle="tab">
                    На карте</a></li>
        </ul>

        <div class="tab-content">
            <div id="picking_up_list" class="tab-pane clearfix schedule-pane mb30" >
                <?= $this->render('checkout___delivery__pickup__list', [
                    'data' => $model->pick_up_points,
                    'model' => $model,
                    'form' => $form,
                ]) ?>
            </div>
            <div id="picking_up_map" class="tab-pane schedule-pane active">
                <div id="map"></div>
                <script>

                    <?php
                    if ($model->pick_up_points_count > 1) {
                        $lat = $model->pick_up_points[1]->latitude;
                        $lng = $model->pick_up_points[1]->longitude;
                    } else {
                        $lat = $model->pick_up_points[0]->latitude;
                        $lng = $model->pick_up_points[0]->longitude;
                    }
                    $zoom = $model->pick_up_points_count > 2 ? 11 : 13;
                    if ($model->_city == 'Москва') {
                        $zoom = 9;
                    }
                    ?>
                    var locations = <?= $model->getJsonLocationsArray($form) ?>;
                    var _city = '<?= $model->_city ?>';
                    var infowindow;
                    var markers;
                    var map;
                    function initMap() {
                        map = new google.maps.Map(document.getElementById('map'), {
                            scrollwheel: false,
                            center: {lat: <?= $lat ?>, lng: <?= $lng ?>},
                            zoom: <?= $zoom ?>
                        });
                        infowindow = new google.maps.InfoWindow({content: ''});

                        markers = locations.map(function (location) {
                            var m = new google.maps.Marker({
                                position: location.coordinates,
                                title: location.title,
                                'id': location.id,
                                icon: '/images/icons/marker_cluster/m0.png'
                            });
                            bindInfoWindow(m, map, infowindow, location.content);
                            if ($('[name="MethodPickup[picking_up_point]"]:checked').length > 0) {
                                if ($('[name="MethodPickup[picking_up_point]"]:checked').val() == location.id) {
                                    infowindow.setContent(location.content);
                                    infowindow.open(map, m);
                                }
                            } else {
                                infowindow.setContent(location.content);
                                infowindow.open(map, m);
                            }
                            return m;
                        });

                        var markerCluster = new MarkerClusterer(map, markers,
                            {imagePath: '/images/icons/marker_cluster/m'});

                        setTimeout(function () {
                            openInfoWindow()
                        }, 500)

                    }

                    function openInfoWindow() {
                        if (!($('[name="MethodPickup[picking_up_point]"]:checked').length > 0)) {
                            if (_city == 'Санкт-Петербург') {
                                infowindow.setContent(locations[1].content);
                                infowindow.open(map, markers[1]);
                            }
                            if (_city == 'Москва') {
                                infowindow.setContent(locations[0].content);
                                infowindow.open(map, markers[0]);
                            }
                        }
                    }

                    function bindInfoWindow(marker, map, infowindow, html) {
                        google.maps.event.addListener(marker, 'click', function () {
                            infowindow.setContent(html);
                            infowindow.open(map, marker);
                            $('input[name="MethodPickup[picking_up_point_fake]"][value="' + point_id + '"]').next().addClass('selected')
                        });
                    }
                </script>
                <script
                    src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
            </div>
        </div>
    </div>
<?php } ?>
<?php
use yii\helpers\Html;

$items = [];
if (count($data) > 0) {
    foreach ($data as $i => $p) {
        $items[$p->id] = '
            <span class="not-map">
                <i></i>
                <span class="l">'.$p->address.'<div class="pull-right hidden-xs">м. '.$p->metro_title.'</div></span>
                <div class="slide">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5>Магазин:</h5> '.$p->title.'
                            <h5>Метро:</h5> '.$p->metro_title.'
                            <h5>Адрес:</h5> '.$p->address.'
                            <h5>Время работы:</h5> '.$p->working_hours.'
                            <h5>Контактный телефон:</h5> '.$p->phone.'
                        </div>
                        <div class="col-sm-6 text-right">
                            <div id="map_'.$p->id.'" class="inlist-map" ></div>
                            <br>
                            <span class="btn btn-lg btn-danger pick-point-in-list">Выбрать</span>
                        </div>
                    </div>
                </div>
            </span>
    ';
    }
}
?>

<div class="col-xs-12  help-block-radio">
    <h4 class="clearfix pick-up-head">
        <small class="pull-left">Адреса магазинов</small>
        <small class="pull-right">Метро</small>
    </h4>

    <?= $form->field($model, 'picking_up_point')->radioList($items, [
        'class' => 'delivery-radio list welled',
        'item' => function ($index, $label, $name, $checked, $value) {
            return Html::tag('div', Html::radio($name, $checked, [
                'label' => $label,
                'value' => $value,
                'data-delivery' => $index,
            ]), ['class' => 'radio']);
        }
    ])->label(false) ?>

    <script>
        function initMiniMap() {
            <?php foreach ($data as $p) { ?>
            var myLatLng = {lat: <?= $p->latitude ?>, lng: <?= $p->longitude ?>};
            var map_<?= $p->id ?> = new google.maps.Map(document.getElementById('map_<?= $p->id ?>'), {
                center: myLatLng,
                zoom: 14
            });
            var marker_<?= $p->id ?> = new google.maps.Marker({
                position: myLatLng,
                map: map_<?= $p->id ?>,
                title: '<?= $p->title ?>'
            });
            <?php } ?>
        }
    </script>
</div>
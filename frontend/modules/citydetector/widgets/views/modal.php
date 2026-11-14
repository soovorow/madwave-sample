<?php
use frontend\modules\citydetector\CityDetector;

$highestCities = CityDetector::highestCities();

$first = array_slice($highestCities, 0, 14);
$second = array_slice($highestCities, 14, 14);
$third = array_slice($highestCities, 28, 14);

$user_city = CityDetector::getCity();
?>

<div id="city-picker" class="modal fade" tabindex="0" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2>Выбор города <br> <small>Всего <span>1204</span> города</small></h2>
            </div>
            <div class="modal-body">
                <label for="city-picker-input">выберите или введите название вашего города</label>
                <input id="city-picker-input" type="text" class="form-control location-city" value="<?= $user_city ?>">
                <hr>
                <p><strong>Если вы не видите вашего города в списке, введите его в строку поиска</strong></p>
                <hr>
                <div class="row">
                    <?php foreach ([$first, $second, $third] as $list) {
                        ?>
                        <div class="col-md-4">
                            <ul>
                                <?php foreach ($list as $i => $c) { ?>
                                    <li><span class="set-city <?= strcmp($user_city, $c) == 0 ? 'active' : '' ?>"
                                              data-city="<?= $c ?>">
                                            <?= $c ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
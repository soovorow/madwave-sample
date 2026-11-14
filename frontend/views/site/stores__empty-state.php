<h3 class="text-center" style="font-weight: normal; margin-top: 60px">
    К сожалению, <?= $missCity ? 'в Вашем городе' : 'По вашему запросу' ?> не найдено ни одного магазина!
    <br>
    <br>
    <?php if ($missCity) { ?>
    <small>
        Но Вы можете воспользоваться нашей быстрой и удобной доставкой!
        <br>
        Mad Wave доставляет товары по всей России
    </small>
    <br>
    <br>
    <?php } ?>
    <a href="<?= \yii\helpers\Url::to([$missCity ? 'site/delivery' : 'site/stores']) ?>"
       class="btn btn-danger">
        <?= $missCity ? 'Перейти на страницу' : 'Сбросить поиск' ?></a>
</h3>

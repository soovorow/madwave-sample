<?php
/** @var $product \common\models\Product */
/** @var $review \common\models\Testimonials */
?>
<div>
    <h1>Новый отзыв о товаре <?= \yii\bootstrap\Html::a($product->name, $product->getUrl()) ?></h1>
    Пользователь оставил новый отзыв на сайте, он не будет опубликован пока вы не измените его статус.
    <br>
    <br>
    <?= \yii\bootstrap\Html::a('Перейти к редактированию', 'http://admin.yii2.madwave.ru/reviews/update?id='.$review->id) ?>
    <br>
    <br>
    <table width="100%">
        <tbody>
        <tr><td><b>Оценка</b></td><td><?= $review->rating ?></td></tr>
        <tr><td><b>Качество</b></td><td><?= $review->getQualityLabel() ?></td></tr>
        <tr><td><b>Размер</b></td><td><?= $review->getSizeMatchLabel() ?></td></tr>
        <tr><td><b>Приемущества</b></td><td><?= $review->pros ?></td></tr>
        <tr><td><b>Недостатки</b></td><td><?= $review->cons ?></td></tr>
        <tr><td><b>Комментарий</b></td><td><?= $review->text ?></td></tr>
        </tbody>
    </table>
</div>
<div class="text-center" style="padding: 100px 40px ">
    <div class="h1"><?= Yii::t('app', 'There are no matching products.') ?></div>
    <div class="h4"><?= Yii::t('app', 'try to reset the search terms') ?></div>
    <br>
    <br>
    <?= \yii\bootstrap\Html::a(Yii::t('app', 'Reset filters'), isset($url) ? $url : \yii\helpers\Url::canonical(), ['class' => 'btn btn-default']) ?>
</div>
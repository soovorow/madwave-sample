<style>
    body, html {
        padding: 0;
        margin: 0;
    }
    .code {
       position: absolute;
       color: #fff;
       z-index: 1;
       top: 42%;
       font-family: sans-serif;
       font-weight: bold;
       font-size: 200%;
        width: 100%;
        text-align: center;
    }
</style>
<?php
use frontend\modules\checkout\ru\models\pricing\PromoCode;
use yii\helpers\Html;

$codes = PromoCode::find()->where(['like', 'title', 'Невская волна'])->all();
foreach ($codes as $c) {
    /** @var $c PromoCode */
    ?>
    <div style="    width: 50%;
    float: left;
    position: relative;
    box-sizing: border-box;">
        <?= Html::img('/images/'.$c->value.'.png', ['style' => 'width:100%']) ?>
        <div class="code"><?= $c->code ?></div>
    </div>
<?php } ?>
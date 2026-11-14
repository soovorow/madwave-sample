<?php
use common\models\AppString;
use yii\helpers\Html;
/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div style="width: 780px; margin: 0 auto">
        <div style="background: #000000; padding: 20px 15px; margin-bottom: 15px">
            <img width="196" height="46"
                 src="https://gallery.mailchimp.com/40bb46ae772e003aa28c582e3/images/968e5ea4-b4ae-4b22-bd2d-6b55a5a5ce32.png"
                 style="width: 196px; height: 46px; margin: 0;" alt="Mad Wave">
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            <img width="180" height="53"
                 src="https://gallery.mailchimp.com/40bb46ae772e003aa28c582e3/images/237f3496-77f6-4547-a838-63d61d6e608c.png"
                 style="width: 180px;height: 53px; margin-top: -7px" alt="8 800 100 57 19 - звонок по России бесплатный">
        </div>
        <div style="padding: 15px">
            <?= $content ?>
        </div>
        <hr>
        <div style="font-size: 12px; padding: 15px">
            <i>Copyright © <?= date('Y') ?> Mad Wave, <?= Yii::t('app', 'All rights reserved') ?>.</i>
            <br><?= Yii::t('app', 'You received this email because you registered on madwave.eu') ?>
            <br>
            <br>
            <b><?= Yii::t('app', 'Our address') ?>:</b>
            Mad Wave
            <br><?= AppString::getString('footer_address') ?>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
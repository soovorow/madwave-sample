<?php
$session = Yii::$app->session;
$key = 'accept_cookies_policy';
$this->registerJs(<<<JS
    $('#cookies-alert .dismiss-alert').on('click', function() {
        $('#cookies-alert').hide();
        $.ajax({
            url: '/site/accept-cookies-policy',
            type: 'POST',
            data: {accept: true},
        });
    });
JS
);
if (!$session->has($key)) { ?>
<section class="hidden-xs" id="cookies-alert" style="background: #333; color: #fff; padding: 15px;">
    <div class="container">
        By continuing to use this site, you agree to use of cookies.
        <a href="<?= \yii\helpers\Url::to(['/site/cookies-policy']) ?>">
            Find out more.
        </a>
        <div class="dismiss-alert pull-right pointer">×</div>
    </div>
</section>
<?php } ?>
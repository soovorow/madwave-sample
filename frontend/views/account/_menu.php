<?= yii\widgets\Menu::widget([
    'options' => ['class' => 'nav nav-pills nav-stacked'],
    'items' => [
        ['label' => Yii::t('app', 'My orders'), 'url' => ['account/orders'], 'active' =>
            \Yii::$app->controller->action->id == 'order-details' ||
            \Yii::$app->controller->action->id == 'orders'
        ],
//        ['label' => 'Cкидки и купоны', 'url' => ['account/coupons']],
        ['label' => Yii::t('app', 'Profile'), 'url' => ['account/profile']],
        ['label' => Yii::t('app', 'Log out'),
            'url' => \yii\helpers\Url::to(['site/logout']),
            'template' => '<a href="{url}" data-method="post">{label}</a>'],
    ],
]); ?>
<?php
use backend\assets\AppAsset;
use backend\assets\BowerAsset;
use common\models\Settings;
use common\models\User;
use frontend\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
BowerAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="<?= Yii::$app->controller->id .' '.Yii::$app->controller->action->id ?>">
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            $rights = Yii::$app->user->identity->rights;
            NavBar::begin([
                'brandLabel' => 'Mad Wave',
                'brandUrl' => Yii::$app->params['baseUrl'],
                'options' => ['class' => 'navbar-inverse navbar-fixed-top',],
            ]);
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
            } else {

                if ($rights > User::RIGHT_LEVEL_1) {
                    $menuItems[] = ['label' => 'Менеджмент', 'items' => [
                        ['label' => 'Заказы', 'url' => ['/order/index']],
                        ['label' => 'Отзывы', 'url' => ['/reviews/index']]
                    ]];
                }

                if ($rights > User::RIGHT_LEVEL_5) {
                    $menuItems[] = ['label' => 'Content', 'items' => [
                        ['label' => 'Shop the look', 'url' => ['/shop-the-look/index']],
                        ['label' => 'Collections', 'url' => ['/collections/index']],
                        ['label' => 'Homepage Banners', 'url' => ['/homepage-banner/index']],
                        ['label' => 'Product Top Banners', 'url' => ['/top-banner/index']],
                        ['label' => 'Pinned Items', 'url' => ['/pinned-items/index']],
                        ['label' => 'Features', 'url' => ['/product_features/features/index']],
                        ['label' => 'EasyFeatures', 'url' => ['/easy-features/index']],
                        ['label' => 'Картинки', 'url' => ['/image/list']],
                        ['label' => 'Challenge', 'url' => ['/challenge/index']],
                        ['label' => 'Blog', 'url' => ['/blog-post/index']],
                        ['label' => 'Stores', 'url' => ['/self-pick-up-points/index']],
                        ['label' => 'Strings', 'url' => ['/string/index']],
                        ['label' => 'Pages', 'url' => ['/page/index']],
                    ]];
                }

                if ($rights > User::RIGHT_LEVEL_7) {
                    $menuItems[] = ['label' => 'Shop', 'items' => [
                        ['label' => 'Discounts', 'url' => ['/discounts/index']],
                        ['label' => 'Products', 'url' => ['/product/index']],
                        ['label' => 'Categories', 'url' => ['/local-category/index']],
                        ['label' => 'Price watcher', 'url' => ['/price_watcher/price-watcher/index']],
                    ]];
                }

                if ($rights > User::RIGHT_LEVEL_8) {
                    $menuItems[] = ['label' => 'Logs', 'items' => [
                        ['label' => 'Action Log', 'url' => ['/action-log/index']],
                        ['label' => 'Warnings', 'url' => ['/warnings/index']],
                        ['label' => 'Logs', 'url' => ['/log/index']],
                    ]];
                }

                if ($rights >= User::RIGHT_LEVEL_9) {
                    $menuItems[] = ['label' => 'System', 'items' => [
                        ['label' => 'Users', 'url' => ['/users/index']],
                        ['label' => 'Redirects', 'url' => ['/redirect/index']],
                    ]];
                }

                $menuItems[] = ['label' => Yii::$app->user->identity->email, 'items' => [
                    [
                        'label' => 'Выход ',
                        'url' => ['/site/logout'],
                        'linkOptions' => ['data-method' => 'post']
                    ]
                ]];
            }
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            NavBar::end();
        ?>

        <div class="container">
            <?= Alert::widget() ?>

            <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>
        <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>

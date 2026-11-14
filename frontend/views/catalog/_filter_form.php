<?php

use common\models\LocalCategory;
use frontend\models\filter\CatalogFilter;
use yii\helpers\Html;

/**
 * @var $category LocalCategory
 */

$filter = new CatalogFilter(['category' => $category]);

$parentCategory = $category->parent_id != null ? $category->getParent() : null;

$isHidden = $category->hidden_in_menu === 1;

$hiddenCategories = LocalCategory::find()
    ->where(['parent_id' => $isHidden ? $category->parent_id : $category->id])
    ->andWhere(['>', 'hidden_in_menu', 0])
    ->all();

if (($hiddenCategories || $isHidden) && $category->id !== 98 && $category->id !== 96) { ?>
    <ul class="nav nav-pills nav-stacked nav-catalog-subcat hidden-xs hidden-sm">
        <li class="<?= !$isHidden ? 'active' : '' ?>">
            <a <?= $isHidden ? 'href="'.$parentCategory->getUrl().'"' : '' ?>>
                <?= Yii::t('app', 'All products') ?></a>
        </li>
        <?php foreach ($hiddenCategories as $hiddenCat) {
            /** @var $hiddenCat LocalCategory */
            echo Html::tag('li', Html::a($hiddenCat->getTitle(), $hiddenCat->getUrl()), [
                'class' => $hiddenCat->id == $category->id ? 'active' : ''
            ]);
        } ?>
    </ul>
<?php } ?>

<div id="filter__container">
    <?php $filter->render() ?>
</div>


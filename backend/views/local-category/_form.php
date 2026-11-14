<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\LocalCategory */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    #category_header {
        height: 160px;
        position: relative;
        overflow: hidden;
    }
    #category_header img {
        width: 100%;
        position: absolute;
        top: 50%;
        margin-top: -32%;
    }
</style>
<div class="category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-xs-12 col-md-8 col-lg-9">
            <div id="category_header" class="category__header hidden-xs">
                <?= $model->getCover() ?>
            </div>

            <br>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'slug')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'meta_title')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'h1')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'desc')->textarea() ?>

                    <?= $form->field($model, 'meta_description')->textarea() ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'title_en')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'slug_en')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'meta_title_en')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'h1_en')->textInput(['maxlength' => 255]) ?>

                    <?= $form->field($model, 'desc_en')->textarea() ?>

                    <?= $form->field($model, 'meta_desc_en')->textarea() ?>
                </div>
            </div>


        </div>
        <div class="col-xs-12 col-md-4 col-lg-3">

            <?= $form->field($model, 'img')->fileInput() ?>

            <?= $form->field($model, 'hidden_in_menu')->dropDownList([
                0 => 'Visible',
                1 => 'Hidden',
            ]); ?>

            <?= $form->field($model, 'parent_id')->dropDownList(ArrayHelper::map($categories, 'id', 'title'), ['prompt' => 'Root']) ?>

            <?= $form->field($model, 'url_path')->textInput() ?>

            <?= $form->field($model, 'url_path_2')->textInput() ?>

            <?= $form->field($model, 'url_path_3')->textInput() ?>

            <?= $form->field($model, 'url_path_4')->textInput() ?>

            <?= $form->field($model, 'yandex_market_title')->textInput() ?>

            <?= $form->field($model, 'google_taxanomy_key')->dropDownList(\frontend\controllers\GoogleController::taxonomy(), [
                'prompt' => 'Choose one...'
            ]) ?>

        </div>
    </div>


        <div class="col-xs-12">
            <br>
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary pull-right' : 'btn btn-primary pull-right']) ?>
        </div>


    <br>
    <br>
    <br>

    <?php

    if ($model->parentCategory) { ?>
        <div class="row">
            <div class="col-xs-6 theiaStickySidebarContainer ">
                <div class="stash_category_container theiaStickySidebar">
                    <label for="#">Отображать следующие категории</label>
                    <br>

                    <?php if ($linkedCategories = $model->getGlobalCategories()) {
                        foreach ($linkedCategories as $lc) {
                            ?>
                            <span class="stash_category_<?= $lc->category_id ?>">- <?= $lc->name_ru ?><br></span>
                        <? }
                    } ?>

                </div>
            </div>
            <div class="col-xs-6">
                <?= $model->renderLinkedCategoryTree(); ?>
            </div>
        </div>

    <? } ?>

    <?php ActiveForm::end(); ?>

</div>

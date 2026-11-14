<?php

/**
 * @var $testimonialsForm \common\models\Testimonials
 */
use yii\helpers\Html;

if ($testimonialsForm->isNewRecord) {
    $testimonialsForm->size_match = 4;
    $testimonialsForm->quantity = 7;
}

?>

<?php if (!$isEmpty) { ?>
<div class="modal fade" id="testimonialsForm" tabindex="-1" role="dialog" aria-labelledby="testimonialsFormLabel">
    <?php }  ?>
    <div id="testimonials-modal" class="modal-dialog" role="document">

        <?php $form = \yii\widgets\ActiveForm::begin([
            'action' => ['/review/create', 'product_id' => $model->id],
        ]); ?>
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12">
                    <h2 class="modal-title" id="testimonialsFormLabel">
                        Оставить отзыв
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </h2>
                </div>
            </div>
            <div class="modal-body clearfix">

                <?php if (!$testimonialsForm->isNewRecord) {
                    echo $form->field($testimonialsForm, 'id')->hiddenInput(['value' => $testimonialsForm->id])->label(false);
                } ?>
                <div class="col-xs-12">
                    <div class="row">
                        <div class="col-xs-5"><h4>Ваша оценка</h4></div>
                        <div class="col-xs-7" style="padding-top: 8px">
                            <div class="clearfix">
                                <fieldset>
                <span class="star-cb-group">
                  <input type="radio" id="rating-5" name="Testimonials[rating]" value="5" <?= isset($force) && $testimonialsForm->rating == 5 ? 'checked' : (!isset($force) ? 'checked' : '') ?>/><label for="rating-5"></label>
                  <input type="radio" id="rating-4" name="Testimonials[rating]" value="4" <?= isset($force) && $testimonialsForm->rating == 4 ? 'checked' : '' ?> /><label for="rating-4"></label>
                  <input type="radio" id="rating-3" name="Testimonials[rating]" value="3" <?= isset($force) && $testimonialsForm->rating == 3 ? 'checked' : '' ?>/><label for="rating-3"></label>
                  <input type="radio" id="rating-2" name="Testimonials[rating]" value="2" <?= isset($force) && $testimonialsForm->rating == 2 ? 'checked' : '' ?>/><label for="rating-2"></label>
                  <input type="radio" id="rating-1" name="Testimonials[rating]" value="1" <?= isset($force) && $testimonialsForm->rating == 1 ? 'checked' : '' ?>/><label for="rating-1"></label>
                </span>
                                </fieldset>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-5"><h4>Качество</h4></div>
                        <div class="col-xs-7" style="padding-top: 8px">
                            <?= $testimonialsForm->getFormField($form, 'quantity', ['Низкое', 'Высокое']) ?>
                        </div>
                    </div>

                    <?php if (count($model->getVariantsBySizes(null, $v->color_id)) > 0 && !empty($model->getVariantsBySizes(null, $v->color_id)[0]['size_id'])) { ?>

                        <div class="row">
                            <div class="col-xs-5"><h4>Соответствие размеру</h4></div>
                            <div class="col-xs-7">
                                <?= $testimonialsForm->getFormField($form, 'size_match', ['Мал', 'Велик']) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-5"><h4>Размер</h4></div>
                            <div class="col-xs-7" style="padding-top: 8px">
                                <?= $form->field($testimonialsForm, 'size')->dropDownList($model->renderSizes($v->color_id, $v->id, null, true), ['prompt' => 'Выберите размер'])->label(false) ?>
                            </div>
                        </div>
                    <?php } else {
                        echo $form->field($testimonialsForm, 'size')->hiddenInput(['value' => 'Отсутствует'])->label(false);
                    }?>

                </div>

                <div class="col-xs-12">
                    <?= $form->field($testimonialsForm, 'pros')
                        ->textarea(['placeholder' => 'Опишите плюсы товара', 'class' => 'form-control rad0'])
                        ->label('<h4><i class="fa custom-plus">+</i>Плюсы</h4>') ?>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($testimonialsForm, 'cons')
                        ->textarea(['placeholder' => 'Опишите минусы товара', 'class' => 'form-control rad0'])
                        ->label('<h4><i class="fa custom-minus">-</i>Минусы</h4>') ?>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($testimonialsForm, 'text')
                        ->textarea(['placeholder' => 'Оставьте ваш комментарий', 'class' => 'form-control rad0'])
                        ->label('<h4><i class="fa fa-comment-o"></i>Комментарий</h4>') ?>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($testimonialsForm, 'name')
                        ->textInput(['placeholder' => 'Введите имя', 'class' => 'form-control rad0'])
                        ->label('<h4>Ваше имя</h4>') ?>
                </div>
                <div class="col-xs-12">
                    <?= $form->field($testimonialsForm, 'email')
                        ->textInput(['placeholder' => 'Укажите вашу почту', 'class' => 'form-control rad0'])
                        ->label('<h4><i class="fa" style="color: red">*</i>Ваш e-mail</h4>') ?>
                    <p style="color: #ccc ">
                        Адрес не будет виден посетителям
                    </p>
                    <br>
                </div>

                <div class="col-xs-12">
                    <?= $form->field($model, 'reCaptcha')->widget(
                        \himiklab\yii2\recaptcha\ReCaptcha::className()
                    ) ?>

                    <p>
                        <input type="submit" value="<?= $testimonialsForm->isNewRecord ? 'Оставить отзыв' : 'Сохранить изменения' ?>" class="btn btn-lg btn-primary">
                    </p>
                </div>
            </div>
        </div>
        <?php $form::end() ?>

    </div>
    <?php if (!$isEmpty) { ?>
</div>
<?php }  ?>


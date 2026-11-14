<?php

use common\models\User;

/**
 * @var $model User
 */

?>

<h1>
    User #<?= $model->id ?> - <?= $model->email ?>
</h1>
<br>
<br>
<div class="row">
    <?php $form = \yii\bootstrap\ActiveForm::begin() ?>
    <div class="col-md-6">
        <table class="table">
            <tbody>
            <tr><td><b>Email</b></td><td><?= $model->email ?></td></tr>
            <tr><td><b>Rights</b></td><td><?= $form->field($model, 'rights')->dropDownList([
                    User::RIGHT_LEVEL_9 => 'Level 9',
                    User::RIGHT_LEVEL_8 => 'Level 8',
                    User::RIGHT_LEVEL_7 => 'Level 7',
                    User::RIGHT_LEVEL_6 => 'Level 6',
                    User::RIGHT_LEVEL_5 => 'Level 5',
                    User::RIGHT_LEVEL_4 => 'Level 4',
                    User::RIGHT_LEVEL_3 => 'Level 3',
                    User::RIGHT_LEVEL_2 => 'Level 2',
                    User::RIGHT_LEVEL_1 => 'Level 1',
                ])->label(false) ?></td></tr>
            <tr><td><b>Registration date</b></td><td><?= date('d/m/Y', $model->created_at) ?></td></tr>
            </tbody>
        </table>
        <br>
        <?= \yii\helpers\Html::submitButton('save') ?>
    </div>
    <?php $form::end() ?>
</div>

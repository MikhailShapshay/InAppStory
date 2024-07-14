<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\PromoCode|\yii\db\ActiveRecord */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="promo-code-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_used')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

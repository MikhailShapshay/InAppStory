<?php

/* @var $this View */

/* @var $model LoginForm */

use app\models\LoginForm;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\View;

$this->title = 'Авторизация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, заполните следующие поля для входа:</p>

    <?php
    $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Имя пользователя') ?>

    <?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>

    <?= $form->field($model, 'rememberMe')->checkbox()->label('Запомнить меня') ?>

    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php
    ActiveForm::end(); ?>
</div>

<?php

/* @var $this \yii\web\View */
/* @var $model \app\models\PromoCode */

use yii\helpers\Html;

$this->title = 'Создать Промокод';
$this->params['breadcrumbs'][] = ['label' => 'Промокоды', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="promo-code-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

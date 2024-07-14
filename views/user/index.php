<?php

/* @var $this \yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    <p>
        <?= Html::a('Создать пользователя', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Сгенерировать 50 пользователей', ['generate-users'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'username',
            'email',
            'auth_key',
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'class' => 'yii\widgets\LinkPager',
            'options' => ['class' => 'pagination'], // класс для стилизации
            'linkOptions' => ['class' => 'page-link'], // класс для ссылок
            'disabledPageCssClass' => 'disabled', // класс для отключенных ссылок
            'activePageCssClass' => 'active', // класс для активной страницы
        ],
    ]); ?>
</div>

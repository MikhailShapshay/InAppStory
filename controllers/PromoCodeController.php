<?php

namespace app\controllers;

use Faker\Factory;
use Yii;
use yii\base\Security;
use yii\web\Controller;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\PromoCode;

class PromoCodeController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Добавьте поведение аутентификации, если необходимо
        return $behaviors;
    }

    // Действие для генерации промокодов
    public function actionGeneratePromoCodes()
    {
        $faker = Factory::create();

        // Определим количество итераций для генерации
        $iterations = 2;
        $batchSize = 25;

        for ($i = 0; $i < $iterations; $i++) {
            $promoCodes = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $promoCode = new PromoCode();
                $promoCode->code = $this->generateUniqueCode(6); // Генерация уникального кода
                $promoCodes[] = [$promoCode->code];
            }
            Yii::$app->db->createCommand()->batchInsert(PromoCode::tableName(), ['code'], $promoCodes)->execute();
        }

        Yii::$app->session->setFlash('success', 'Промокоды успешно сгенерированы!');

        return $this->redirect(['index']);
    }

    // Действие для отображения списка всех промокодов
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PromoCode::find(),
            'pagination' => [
                'pageSize' => 25, // Количество строк на странице
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // Действие для просмотра деталей промокода
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    // Действие для создания нового промокода
    public function actionCreate()
    {
        $model = new PromoCode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    // Действие для обновления существующего промокода
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    // Действие для удаления промокода
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    // Действие для получения доступного промокода
    public function actionGet()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $promoCode = PromoCode::findAvailablePromoCode();

            if ($promoCode === null) {
                throw new NotFoundHttpException('Нет доступных промокодов.');
            }

            $promoCode->markAsUsed();
            $transaction->commit();

            Yii::$app->session->setFlash('success', 'Промокод успешно получен.');

            return $this->redirect(['index']);
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Не удалось получить промокод: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    // Найти модель промокода по ID
    protected function findModel($id): ?PromoCode
    {
        if (($model = PromoCode::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Промокод не найден.');
    }

    // Генерация уникального кода
    private function generateUniqueCode($length)
    {
        $faker = Factory::create();
        $unique = false;
        $code = '';

        while (!$unique) {
            $code = $faker->regexify('[A-Za-z0-9]{' . $length . '}');
            $existing = PromoCode::findOne(['code' => $code]);
            if (!$existing) {
                $unique = true;
            }
        }

        return $code;
    }
}
